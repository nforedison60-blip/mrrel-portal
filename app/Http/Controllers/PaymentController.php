<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout()
    {
        return view('payment.checkout');
    }

    public function process(Request $request)
    {
        // 1. Use the official PayFast Sandbox credentials
        $merchantId = '10000100';
        $merchantKey = '46f0cd694581a';
        $passphrase = 'jt7NOE43FZPn'; // Official Sandbox Passphrase
        
        // 2. Sandbox URL
        $url = 'https://sandbox.payfast.co.za/eng/process';

        // 3. Dynamically build the URLs based on the current domain (Local or Live)
        $baseUrl = url('/'); 

        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'return_url' => $baseUrl . '/payment/success',
            'cancel_url' => $baseUrl . '/payment/cancel',
            'notify_url' => $baseUrl . '/payment/notify', // Required by PayFast
            'name_first' => 'John',
            'name_last' => 'Doe',
            'email_address' => 'test@example.com',
            'm_payment_id' => 'BKG-' . rand(1000, 9999),
            'amount' => '150.00',
            'item_name' => 'MrRel Service Payment',
        ];

        // 4. Generate the PayFast signature
        $pfParamString = '';
        foreach ($data as $key => $val) {
            $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
        }
        
        $pfParamString = rtrim($pfParamString, '&');
        $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
        $data['signature'] = md5($pfParamString);

        // 5. Build the auto-submitting HTML form
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