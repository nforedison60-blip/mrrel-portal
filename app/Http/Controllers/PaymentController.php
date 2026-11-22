<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPaidCustomer;
use App\Mail\BookingPaidProvider;
use Illuminate\Support\Facades\Log;

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

        $siteCode = env('OZOW_SITE_CODE');
        $privateKey = env('OZOW_PRIVATE_KEY');
        $isTest = env('OZOW_IS_TEST', 'false') === 'true' ? 'true' : 'false';

        $amount = number_format($booking->total_price, 2, '.', '');
        $reference = 'BKG-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        $bankReference = 'MrRel'; 
        
        // URLs must be defined BEFORE the hash string
        $successUrl = url('/payment/success');
        $cancelUrl = url('/payment/cancel');
        $errorUrl = url('/payment/cancel');
        $notifyUrl = url('/payment/notify');

        // THE FIX: Exact field order required by Ozow for a payment request
        // SiteCode + CountryCode + CurrencyCode + Amount + TransactionReference + BankReference + CancelUrl + ErrorUrl + SuccessUrl + NotifyUrl + IsTest + PrivateKey
        $hashString = $siteCode . 'ZA' . 'ZAR' . $amount . $reference . $bankReference . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey;
        
        // Lowercase the ENTIRE string before hashing
        $hashString = strtolower($hashString);
        $hashCheck = hash('sha512', $hashString);

        $url = 'https://pay.ozow.com/';
        
        $html = '<form id="ozow_form" action="' . $url . '" method="post">';
        $html .= '<input type="hidden" name="SiteCode" value="' . $siteCode . '">';
        $html .= '<input type="hidden" name="CountryCode" value="ZA">';
        $html .= '<input type="hidden" name="CurrencyCode" value="ZAR">';
        $html .= '<input type="hidden" name="Amount" value="' . $amount . '">';
        $html .= '<input type="hidden" name="TransactionReference" value="' . $reference . '">';
        $html .= '<input type="hidden" name="BankReference" value="' . $bankReference . '">';
        $html .= '<input type="hidden" name="HashCheck" value="' . $hashCheck . '">';
        $html .= '<input type="hidden" name="SuccessUrl" value="' . $successUrl . '">';
        $html .= '<input type="hidden" name="CancelUrl" value="' . $cancelUrl . '">';
        $html .= '<input type="hidden" name="ErrorUrl" value="' . $errorUrl . '">';
        $html .= '<input type="hidden" name="NotifyUrl" value="' . $notifyUrl . '">';
        $html .= '<input type="hidden" name="IsTest" value="' . $isTest . '">';
        $html .= '</form>';
        $html .= '<script>document.getElementById("ozow_form").submit();</script>';

        return $html;
    }

    public function success(Request $request)
    {
        // Ozow sends the customer back with the booking reference (for example BKG-0007)
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

        // If the bank says the payment did not complete, do not mark it as paid
        $gatewayStatus = $request->query('Status');
        if ($gatewayStatus && strtolower($gatewayStatus) !== 'complete') {
            return redirect()->route('customer.bookings', ['booking' => $booking->id])
                ->with('error', 'Your bank did not confirm the payment. Please try again with Pay now.');
        }

        // TODO (needs a test payment with Ozow): confirm payment ONLY from the signed notify message below.
        $booking->update(['status' => 'Paid']);

        return redirect()->route('customer.bookings', ['booking' => $booking->id])
            ->with('success', 'Thank you! Your payment has been recorded.');
    }

    public function cancel()
    {
        $booking = Booking::where('user_id', auth()->id())->where('status', '!=', 'Paid')->latest()->first();

        return redirect()->route('customer.bookings', $booking ? ['booking' => $booking->id] : [])
            ->with('error', 'The payment was cancelled, so nothing was charged. You can try again with Pay now.');
    }

    public function notify(Request $request)
    {
        Log::info('Ozow Webhook Received: ', $request->all());

        $status = $request->input('Status');
        $reference = $request->input('TransactionReference');

        if ($status === 'Complete') {
            $bookingId = str_replace('BKG-', '', $reference);
            $booking = Booking::find($bookingId);

            if ($booking && $booking->status !== 'Paid') {
                $booking->update(['status' => 'Paid']);

                try {
                    Mail::to('nforedison60@gmail.com')->send(new BookingPaidCustomer($booking));
                    Mail::to('nforedison60@gmail.com')->send(new BookingPaidProvider($booking));
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                }
            }
        }

        return response('OK', 200);
    }
}