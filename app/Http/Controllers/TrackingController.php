<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPaidCustomer;
use App\Mail\BookingPaidProvider;

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

        $merchantId = '10000100';
        $merchantKey = '46f0cd694581a';
        $passphrase = 'jt7NOE43FZPn'; 
        $url = 'https://sandbox.payfast.co.za/eng/process';
        $baseUrl = url('/'); 

        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'return_url' => $baseUrl . '/payment/success',
            'cancel_url' => $baseUrl . '/payment/cancel',
            'notify_url' => $baseUrl . '/payment/notify',
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
        $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
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
        $booking = Booking::latest()->first();
        
        if ($booking) {
            $booking->update(['status' => 'Paid']);

            // Get the logged in user's real email
            $customerEmail = auth()->user()->email;
            
            // Send to real customer
            Mail::to($customerEmail)->send(new BookingPaidCustomer($booking));
            
            // Send to provider (using your email for testing)
            Mail::to($customerEmail)->send(new BookingPaidProvider($booking));
        }

        return "Payment Successful! Thank you. Check your email for the receipt.";
    }

    public function cancel()
    {
        return "Payment Cancelled.";
    }
}