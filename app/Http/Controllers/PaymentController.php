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
    public function checkout()
    {
        return view('payment.checkout');
    }

    public function process(Request $request)
    {
        $booking = Booking::latest()->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'No active booking found to pay for.');
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

    public function success()
    {
        $booking = Booking::latest()->first();
        
        if ($booking) {
            $booking->update(['status' => 'Paid']);
        }

        return "Payment Successful! Thank you. Check your email for the receipt.";
    }

    public function cancel()
    {
        return "Payment Cancelled.";
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