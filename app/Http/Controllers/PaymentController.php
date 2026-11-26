<?php

namespace App\Http\Controllers;

use App\Mail\BookingPaidCustomer;
use App\Mail\BookingPaidProvider;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        // Pay the booking the customer clicked on, or else their newest unpaid one
        $booking = $request->filled('booking')
            ? Booking::where('user_id', auth()->id())->with('quote')->find($request->booking)
            : Booking::where('user_id', auth()->id())->where('status', '!=', 'Paid')->with('quote')->latest()->first();

        return view('payment.checkout', ['booking' => $booking]);
    }

    public function process(Request $request)
    {
        $request->validate(['booking_id' => 'required|integer']);

        $booking = Booking::where('user_id', auth()->id())->find($request->booking_id);
        if (!$booking) {
            return redirect()->route('customer.bookings')->with('error', 'No booking found to pay for.');
        }
        if ($booking->status === 'Paid') {
            return redirect()->route('customer.bookings', ['booking' => $booking->id])->with('error', 'This booking is already paid.');
        }

        // config() and not env(): env() returns nothing once the config is cached (as on Laravel Cloud)
        $siteCode   = config('services.ozow.site_code');
        $privateKey = config('services.ozow.private_key');
        if (!$siteCode || !$privateKey) {
            Log::error('Ozow is not configured: OZOW_SITE_CODE or OZOW_PRIVATE_KEY is missing.');
            return redirect()->route('customer.bookings', ['booking' => $booking->id])
                ->with('error', 'Payments are not available right now. Please try again later.');
        }

        $isTest        = config('services.ozow.is_test') ? 'true' : 'false';
        $amount        = number_format($booking->total_price, 2, '.', '');
        $reference     = 'BKG-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        $bankReference = 'MrRel';

        $successUrl = url('/payment/success');
        $cancelUrl  = url('/payment/cancel');
        $errorUrl   = url('/payment/cancel');
        $notifyUrl  = url('/payment/notify');

        // Ozow field order: SiteCode, CountryCode, CurrencyCode, Amount, TransactionReference, BankReference,
        // CancelUrl, ErrorUrl, SuccessUrl, NotifyUrl, IsTest, PrivateKey. Lower-cased, then SHA512.
        $hashString = strtolower($siteCode . 'ZA' . 'ZAR' . $amount . $reference . $bankReference
            . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey);
        $hashCheck = hash('sha512', $hashString);

        $fields = [
            'SiteCode' => $siteCode, 'CountryCode' => 'ZA', 'CurrencyCode' => 'ZAR', 'Amount' => $amount,
            'TransactionReference' => $reference, 'BankReference' => $bankReference, 'HashCheck' => $hashCheck,
            'SuccessUrl' => $successUrl, 'CancelUrl' => $cancelUrl, 'ErrorUrl' => $errorUrl,
            'NotifyUrl' => $notifyUrl, 'IsTest' => $isTest,
        ];

        $html = '<form id="ozow_form" action="https://pay.ozow.com/" method="post">';
        foreach ($fields as $name => $value) {
            $html .= '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '">';
        }
        $html .= '</form><script>document.getElementById("ozow_form").submit();</script>';
        return $html;
    }

    // The customer comes back here from the bank. This page NEVER marks anything as paid:
    // anyone can open this address by hand. Only the signed notify message below can do that.
    public function success(Request $request)
    {
        $booking = null;
        $reference = $request->query('TransactionReference');
        if ($reference && preg_match('/^BKG-(\d+)$/', $reference, $m)) {
            $booking = Booking::where('user_id', auth()->id())->find((int) $m[1]);
        }
        if (!$booking) {
            $booking = Booking::where('user_id', auth()->id())->where('status', '!=', 'Paid')->latest()->first();
        }
        if (!$booking) {
            return redirect()->route('customer.bookings');
        }

        $to = redirect()->route('customer.bookings', ['booking' => $booking->id]);

        if ($booking->status === 'Paid') {
            return $to->with('success', 'Thank you! Your payment is confirmed.');
        }
        $gatewayStatus = $request->query('Status');
        if ($gatewayStatus && strtolower($gatewayStatus) !== 'complete') {
            return $to->with('error', 'Your bank did not confirm the payment. Please try again with Pay now.');
        }
        return $to->with('success', 'Thank you! We are waiting for your bank to confirm the payment. This booking will show as Paid within a minute or two. Refresh this page to check.');
    }

    public function cancel()
    {
        $booking = Booking::where('user_id', auth()->id())->where('status', '!=', 'Paid')->latest()->first();
        return redirect()->route('customer.bookings', $booking ? ['booking' => $booking->id] : [])
            ->with('error', 'The payment was cancelled, so nothing was charged. You can try again with Pay now.');
    }

    // Ozow calls this from its own server (no login, no session). It is the ONLY thing that marks a booking Paid.
    public function notify(Request $request)
    {
        $data = $request->all();
        Log::info('Ozow notify received', ['reference' => $data['TransactionReference'] ?? null, 'status' => $data['Status'] ?? null]);

        if (!$this->hasValidHash($data)) {
            Log::warning('Ozow notify rejected: bad hash', ['reference' => $data['TransactionReference'] ?? null]);
            return response('Invalid hash', 400);
        }
        if (($data['SiteCode'] ?? null) !== config('services.ozow.site_code')) {
            Log::warning('Ozow notify rejected: wrong site code');
            return response('Invalid site', 400);
        }

        // A test payment must never confirm a booking on the live site
        $messageIsTest = filter_var($data['IsTest'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($messageIsTest && !config('services.ozow.is_test')) {
            Log::warning('Ozow notify rejected: test payment on live site');
            return response('Test payment', 400);
        }

        $reference = $data['TransactionReference'] ?? '';
        if (preg_match('/^LIN-(\d+)$/', (string) $reference, $lin)) {
            return $this->confirmIntroduction($data, (int) $lin[1]);
        }
        if (preg_match('/^TPY-(\d+)$/', (string) $reference, $tpy)) {
            return $this->confirmTenantPayment($data, (int) $tpy[1]);
        }
        // A landlord invoice (INV-0001)? Same signed message, different record.
        if (preg_match('/^INV-(\d+)$/', $reference, $invoiceMatch)) {
            return $this->confirmInvoice((int) $invoiceMatch[1], $data);
        }

        if (!preg_match('/^BKG-(\d+)$/', $reference, $m)) {
            return response('Unknown reference', 400);
        }

        if (strtolower($data['Status'] ?? '') !== 'complete') {
            return response('OK', 200); // cancelled / error / abandoned: nothing to do
        }

        $justPaid = null;
        DB::transaction(function () use ($m, $data, &$justPaid) {
            $booking = Booking::lockForUpdate()->find((int) $m[1]);
            if (!$booking) {
                Log::warning('Ozow notify: booking not found', ['id' => $m[1]]);
                return;
            }
            // The amount the bank received must match the price of the booking
            if (abs((float) ($data['Amount'] ?? 0) - (float) $booking->total_price) > 0.009) {
                Log::warning('Ozow notify rejected: amount mismatch', ['booking' => $booking->id, 'amount' => $data['Amount'] ?? null]);
                return;
            }
            if ($booking->status !== 'Paid') { // safe if Ozow sends the same message twice
                $booking->update(['status' => 'Paid']);
                $justPaid = $booking;
            }
        });

        if ($justPaid) {
            $this->sendPaidEmails($justPaid);
        }
        return response('OK', 200);
    }

    // Marks a landlord invoice paid. Only reached after the signature, site code and test-mode checks.
    private function confirmInvoice(int $id, array $data)
    {
        if (strtolower($data['Status'] ?? '') !== 'complete') {
            return response('OK', 200); // cancelled / error / abandoned: nothing to do
        }
        DB::transaction(function () use ($id, $data) {
            $invoice = \App\Models\Invoice::lockForUpdate()->find($id);
            if (!$invoice) {
                Log::warning('Ozow notify: invoice not found', ['id' => $id]);
                return;
            }
            if (abs((float) ($data['Amount'] ?? 0) - (float) $invoice->amount) > 0.009) {
                Log::warning('Ozow notify rejected: invoice amount mismatch', ['invoice' => $invoice->id, 'amount' => $data['Amount'] ?? null]);
                return;
            }
            if ($invoice->status === 'Unpaid') { // safe if Ozow sends the same message twice
                $invoice->markPaid('Ozow', $data['TransactionId'] ?? null);
            }
        });
        return response('OK', 200);
    }

    // A tenant fee (matching fee or viewing attendance). Only reached after the signature, site code and test-mode checks.
    private function confirmTenantPayment(array $data, int $id)
    {
        if (strtolower($data['Status'] ?? '') !== 'complete') {
            return response('OK', 200); // cancelled or failed: the payment stays Pending so the tenant can try again
        }

        DB::transaction(function () use ($data, $id) {
            $payment = \App\Models\TenantPayment::lockForUpdate()->find($id);
            if (!$payment) {
                Log::warning('Ozow notify: tenant payment not found', ['id' => $id]);
                return;
            }
            // The amount paid must match the amount we stored
            if (abs((float) ($data['Amount'] ?? 0) - (float) $payment->amount) > 0.009) {
                Log::warning('Ozow notify rejected: tenant payment amount mismatch', ['payment' => $payment->id, 'amount' => $data['Amount'] ?? null]);
                return;
            }
            if ($payment->status === 'Paid') {
                return; // the same message twice does nothing
            }
            $payment->update([
                'status' => 'Paid',
                'paid_at' => now(),
                'ozow_transaction_id' => $data['TransactionId'] ?? null,
            ]);
            if ($payment->type === 'viewing_attendance' && $payment->viewing_request_id) {
                $viewing = \App\Models\ViewingRequest::find($payment->viewing_request_id);
                if ($viewing) {
                    $viewing->forceFill(['mrrel_attends' => true])->save();
                }
            }
        });

        return response('OK', 200);
    }

    // A landlord introduction fee. Only reached after the signature, site code and test-mode checks.
    private function confirmIntroduction(array $data, int $id)
    {
        if (strtolower($data['Status'] ?? '') !== 'complete') {
            return response('OK', 200); // cancelled or failed: it stays unpaid so the landlord can try again
        }

        DB::transaction(function () use ($data, $id) {
            $match = \App\Models\PropertyMatch::lockForUpdate()->find($id);
            if (!$match || $match->intro_status === null) {
                Log::warning('Ozow notify: introduction not found or never started', ['id' => $id]);
                return;
            }
            // The amount paid must match the fee we stored when the landlord started paying
            if (abs((float) ($data['Amount'] ?? 0) - (float) $match->intro_amount) > 0.009) {
                Log::warning('Ozow notify rejected: introduction amount mismatch', ['match' => $match->id, 'amount' => $data['Amount'] ?? null]);
                return;
            }
            if ($match->intro_status === 'Paid') {
                return; // the same message twice does nothing
            }
            $match->forceFill([
                'intro_status' => 'Paid',
                'intro_paid_at' => now(),
                'intro_ozow_id' => $data['TransactionId'] ?? null,
                'landlord_status' => 'Interested',
            ])->save();
        });

        return response('OK', 200);
    }

    // Ozow signs its message: SHA512 of the fields (without Hash) + private key, lower-cased.
    private function hasValidHash(array $data): bool
    {
        $privateKey = config('services.ozow.private_key');
        $received = strtolower((string) ($data['Hash'] ?? ''));
        if (!$privateKey || $received === '') {
            return false;
        }

        $documentedOrder = ['SiteCode', 'TransactionId', 'TransactionReference', 'Amount', 'Status',
            'Optional1', 'Optional2', 'Optional3', 'Optional4', 'Optional5',
            'CurrencyCode', 'IsTest', 'StatusMessage'];

        $candidates = [];
        // 1) the order in Ozow's documentation
        $s = '';
        foreach ($documentedOrder as $key) {
            if (array_key_exists($key, $data)) {
                $s .= $data[$key];
            }
        }
        $candidates[] = $s;
        // 2) the order the fields arrived in
        $s = '';
        foreach ($data as $key => $value) {
            if ($key !== 'Hash' && is_scalar($value)) {
                $s .= $value;
            }
        }
        $candidates[] = $s;

        foreach ($candidates as $string) {
            $expected = hash('sha512', strtolower($string . $privateKey));
            if (hash_equals($expected, $received)) {
                return true;
            }
        }
        return false;
    }

    private function sendPaidEmails(Booking $booking): void
    {
        $booking->loadMissing('quote');
        $customer = User::find($booking->user_id);
        $provider = User::find(optional($booking->quote)->provider_user_id);

        try {
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new BookingPaidCustomer($booking));
            }
            if ($provider && $provider->email) {
                Mail::to($provider->email)->send(new BookingPaidProvider($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Paid-booking email failed: ' . $e->getMessage());
        }
    }
}
