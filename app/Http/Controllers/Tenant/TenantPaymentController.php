<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PropertyMatch;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\ViewingRequest;
use App\Support\TenantAccess;
use App\Support\TenantFees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Pays the matching fee and the optional attendance fee through Ozow.
 * Nothing here ever marks a payment as paid: only the signed message at /payment/notify does that.
 */
class TenantPaymentController extends Controller
{
    // ---- Step 3: the once-off matching fee -------------------------------------------------

    public function startMatchingFee()
    {
        $userId = auth()->id();

        if (TenantAccess::hasPaidMatchingFee($userId)) {
            return redirect()->route('customer.matches')->with('success', 'You have already paid the matching fee.');
        }
        if (!PropertyMatch::where('user_id', $userId)->where('status', 'Offered')->exists()) {
            return redirect()->route('customer.needs.create')->with('error', 'The matching fee is paid once MrRel has found a match for you.');
        }

        // Reuse an unfinished attempt so retries never create duplicates
        $payment = TenantPayment::where('user_id', $userId)
            ->where('type', TenantPayment::MATCHING_FEE)
            ->where('status', 'Pending')
            ->latest()->first();

        if (!$payment) {
            $payment = TenantPayment::create([
                'user_id'        => $userId,
                'type'           => TenantPayment::MATCHING_FEE,
                'amount'         => TenantFees::matchingFee(),
                'status'         => 'Pending',
                'rental_need_id' => optional(RentalNeed::where('user_id', $userId)->latest()->first())->id,
            ]);
        }

        if (request('method') === 'bank') {
            return redirect()->route('bank.show', $payment->reference());
        }

        return $this->ozowForm($payment, route('customer.matches'));
    }

    // ---- Step 5: optional attendance --------------------------------------------------------

    private function ownViewing(ViewingRequest $viewing): ViewingRequest
    {
        abort_unless((int) $viewing->user_id === (int) auth()->id(), 404);
        return $viewing;
    }

    public function attendanceShow(ViewingRequest $viewing)
    {
        $this->ownViewing($viewing);
        $userId = auth()->id();

        if (!TenantAccess::hasPaidMatchingFee($userId) || !TenantFees::attendanceAvailable()) {
            return redirect()->route('customer.shortlist');
        }
        if ($viewing->mrrel_attends) {
            return redirect()->route('customer.shortlist')->with('success', 'Someone from MrRel will attend this viewing for you.');
        }

        $property = $viewing->property;
        $pending = TenantPayment::where('user_id', $userId)->where('type', TenantPayment::ATTENDANCE)
            ->where('viewing_request_id', $viewing->id)->where('status', 'Pending')->latest()->first();

        // A saved attempt keeps its price; otherwise work it out now
        $estimate = $pending
            ? ['fee' => (float) $pending->amount] + (array) $pending->meta
            : ($property ? TenantFees::attendanceEstimate($property) : null);

        if (!$estimate) {
            return redirect()->route('customer.shortlist')
                ->with('error', 'We could not work out the distance to this place, so attending for you is not available for it.');
        }

        return view('customer.attendance', ['viewing' => $viewing, 'property' => $property, 'estimate' => $estimate]);
    }

    public function startAttendance(ViewingRequest $viewing)
    {
        $this->ownViewing($viewing);
        $userId = auth()->id();

        if (!TenantAccess::hasPaidMatchingFee($userId) || !TenantFees::attendanceAvailable()) {
            return redirect()->route('customer.shortlist')->with('error', 'Attendance is not available right now.');
        }
        if ($viewing->mrrel_attends) {
            return redirect()->route('customer.shortlist')->with('success', 'Someone from MrRel will attend this viewing for you.');
        }

        $payment = TenantPayment::where('user_id', $userId)->where('type', TenantPayment::ATTENDANCE)
            ->where('viewing_request_id', $viewing->id)->where('status', 'Pending')->latest()->first();

        if (!$payment) {
            $estimate = $viewing->property ? TenantFees::attendanceEstimate($viewing->property) : null;
            if (!$estimate) {
                return redirect()->route('customer.shortlist')->with('error', 'We could not work out the cost for this place.');
            }
            // The fee is worked out here, on the server, and stored. The browser never sends an amount.
            $payment = TenantPayment::create([
                'user_id'            => $userId,
                'type'               => TenantPayment::ATTENDANCE,
                'amount'             => $estimate['fee'],
                'status'             => 'Pending',
                'rental_need_id'     => $viewing->rental_need_id,
                'viewing_request_id' => $viewing->id,
                'meta'               => [
                    'one_way_km'    => $estimate['one_way_km'],
                    'round_trip_km' => $estimate['round_trip_km'],
                    'extra_km'      => $estimate['extra_km'],
                    'fee'           => $estimate['fee'],
                    'estimate'      => true,
                ],
            ]);
        }

        if (request('method') === 'bank') {
            return redirect()->route('bank.show', $payment->reference());
        }

        return $this->ozowForm($payment, route('customer.shortlist'));
    }

    // ---- Return pages (never mark anything paid) ---------------------------------------------

    public function returned(Request $request)
    {
        $payment = $this->ownPaymentFromReference((string) $request->query('TransactionReference'));
        $to = $payment->type === TenantPayment::MATCHING_FEE ? route('customer.matches') : route('customer.shortlist');

        if ($payment->status === 'Paid') {
            return redirect($to)->with('success', 'Thank you! Your payment is confirmed.');
        }
        $status = $request->query('Status');
        if ($status && strtolower($status) !== 'complete') {
            return redirect($to)->with('error', 'Your bank did not confirm the payment. You can try again.');
        }
        return redirect($to)->with('success', 'Thank you! We are waiting for your bank to confirm the payment. Refresh in a minute or two.');
    }

    public function cancelled(Request $request)
    {
        $to = route('customer.matches');
        if (preg_match('/^TPY-(\d+)$/', (string) $request->query('TransactionReference'), $m)) {
            $payment = TenantPayment::where('user_id', auth()->id())->find((int) $m[1]);
            if ($payment && $payment->type === TenantPayment::ATTENDANCE) {
                $to = route('customer.shortlist');
            }
        }
        return redirect($to)->with('error', 'The payment was cancelled, so nothing was charged. You can try again.');
    }

    // A tenant can only open their own payment rows (404 otherwise)
    private function ownPaymentFromReference(string $reference): TenantPayment
    {
        abort_unless(preg_match('/^TPY-(\d+)$/', $reference, $m), 404);
        return TenantPayment::where('user_id', auth()->id())->findOrFail((int) $m[1]);
    }

    // ---- Ozow ------------------------------------------------------------------------------

    private function ozowForm(TenantPayment $payment, string $fallback)
    {
        $siteCode = config('services.ozow.site_code');
        $privateKey = config('services.ozow.private_key');
        if (!$siteCode || !$privateKey) {
            Log::error('Ozow is not configured: OZOW_SITE_CODE or OZOW_PRIVATE_KEY is missing.');
            return redirect($fallback)->with('error', 'Payments are not available right now. Please try again later.');
        }

        $isTest = config('services.ozow.is_test') ? 'true' : 'false';
        $amount = number_format((float) $payment->amount, 2, '.', '');
        $reference = $payment->reference();
        $bankReference = 'MrRel';

        $successUrl = route('tenant.payments.back');
        $cancelUrl = route('tenant.payments.cancelled');
        $errorUrl = route('tenant.payments.cancelled');
        $notifyUrl = url('/payment/notify'); // the one signed address that confirms payments

        $hashCheck = hash('sha512', strtolower($siteCode . 'ZA' . 'ZAR' . $amount . $reference . $bankReference
            . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey));

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

        return response($html);
    }
}
