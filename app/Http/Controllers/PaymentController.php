<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class PaymentController extends Controller
{
    public function checkout()
    {
        return view('payment.checkout');
    }

    public function process(Request $request)
    {
        // 1. Get the latest booking for the logged-in user (or just the latest booking for testing)
        // In a real app, you would pass a specific booking ID to this route.
        $booking = Booking::latest()->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'No active booking found to pay for.');
        }

        // 2. Use the official PayFast Sandbox credentials (Replace with your real ones later)
        $merchantId = '10000100';
        $merchantKey = '46f0cd694581a';
        $passphrase = 'jt7NOE43FZPn'; 
        
        // 3. Sandbox URL
        $url = 'https://sandbox.payfast.co.za/eng/process';

        // 4. Dynamically build the URLs based on the current domain
        $baseUrl = url('/'); 

        // 5. Build the data using the REAL booking amount and REAL customer details
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
            'amount' => number_format($booking->total_price, 2, '.', ''), // THE REAL PRICE
            'item_name' => 'MrRel Service Payment for Booking #' . $booking->id,
        ];

        // 6. Generate the PayFast signature
        $pfParamString = '';
        foreach ($data as $key => $val) {
            $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
        }
        
        $pfParamString = rtrim($pfParamString, '&');
        $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
        $data['signature'] = md5($pfParamString);

        // 7. Build the auto-submitting HTML form
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
        return "Payment Successful! Thank you.";
    }

    public function cancel()
    {
        return "Payment Cancelled.";
    }
}