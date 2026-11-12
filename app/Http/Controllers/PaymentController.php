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

        $merchantId = env('PAYFAST_MERCHANT_ID');
        $merchantKey = env('PAYFAST_MERCHANT_KEY');
        $passphrase = env('PAYFAST_PASSPHRASE', null); 
        
        $url = 'https://www.payfast.co.za/eng/process';
        $baseUrl = url('/'); 

        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'return_url' => $baseUrl . '/payment/success',
            'cancel_url' => $baseUrl . '/payment/cancel',
            'notify_url' => $baseUrl . '/payment/notify', // THIS IS THE WEBHOOK URL
            'name_first' => auth()->user()->name ?? 'Customer',
            'name_last' => 'User',
            'email_address' => auth()->user()->email ?? 'test@example.com',
            'm_payment_id' => 'BKG-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
            'amount' => number_format($booking->total_price, 2, '.', ''),
            'item_name' => 'MrRel Service Payment for Booking #' . $booking->id,
        ];

        $pfParamString = '';
        foreach ($data as $key => $val) {
            $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
        }
        
        $pfParamString = rtrim($pfParamString, '&');
        
        if ($passphrase) {
            $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
        }
        
        $data['signature'] = md5($pfParamString);

        $html = '<form id="payfast_form" action="' . $url . '" method="post">';
        foreach ($data as $key => $val) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '">';
        }
        $html .= '</form>';
        $html .= '<script>document.getElementById("payfast_form").submit();</script>';

        return $html;
    }

    public function success()
    {
        // We now rely on the webhook to mark as paid. 
        // This page just tells the user it went through.
        return "Payment Successful! Thank you. Check your email for the receipt.";
    }

    public function cancel()
    {
        return "Payment Cancelled.";
    }

    // THE PAYFAST WEBHOOK (ITN)
    public function notify(Request $request)
    {
        // 1. Log the incoming ping so we can see it in laravel.log
        Log::info('PayFast ITN Received: ', $request->all());

        // 2. Get the data from PayFast
        $paymentStatus = $request->input('payment_status');
        $mPaymentId = $request->input('m_payment_id'); // E.g., BKG-0001
        $pfPaymentId = $request->input('pf_payment_id');

        if ($paymentStatus === 'COMPLETE') {
            // 3. Extract the booking ID from the m_payment_id
            $bookingId = str_replace('BKG-', '', $mPaymentId);
            $booking = Booking::find($bookingId);

            if ($booking) {
                // 4. Mark as Paid
                $booking->update(['status' => 'Paid']);

                // 5. Send the emails
                try {
                    Mail::to('nforedison60@gmail.com')->send(new BookingPaidCustomer($booking));
                    Mail::to('nforedison60@gmail.com')->send(new BookingPaidProvider($booking));
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                }

                Log::info('Booking #' . $bookingId . ' marked as PAID via webhook.');
            }
        }

        // 6. Always tell PayFast we received the ping successfully
        return response('OK', 200);
    }
}