<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// The landlord's side: see invoices, pay them, download receipts
class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())->with('property')->latest()->get();

        return view('landlord.invoices', ['invoices' => $invoices]);
    }

    public function show(Invoice $invoice)
    {
        $this->own($invoice);

        return view('landlord.invoice', ['invoice' => $invoice->load('property'), 'landlord' => auth()->user()]);
    }

    public function receipt(Invoice $invoice)
    {
        $this->own($invoice);
        if (! $invoice->isPaid()) {
            return redirect()->route('landlord.invoices.show', $invoice)->with('error', 'A receipt is issued once the invoice is paid.');
        }

        return view('landlord.receipt', ['invoice' => $invoice->load('property'), 'landlord' => auth()->user()]);
    }

    // Starts an Ozow instant-EFT payment
    public function pay(Invoice $invoice)
    {
        $this->own($invoice);
        if ($invoice->status !== 'Unpaid') {
            return redirect()->route('landlord.invoices.show', $invoice)->with('error', 'This invoice is not waiting for payment.');
        }

        $siteCode   = config('services.ozow.site_code');
        $privateKey = config('services.ozow.private_key');
        if (! $siteCode || ! $privateKey) {
            Log::error('Ozow is not configured: OZOW_SITE_CODE or OZOW_PRIVATE_KEY is missing.');
            return redirect()->route('landlord.invoices.show', $invoice)
                ->with('error', 'Online payment is not available right now. Please pay by EFT with the details below.');
        }

        $isTest        = config('services.ozow.is_test') ? 'true' : 'false';
        $amount        = number_format((float) $invoice->amount, 2, '.', '');
        $reference     = $invoice->gatewayReference();
        $bankReference = 'MrRel';

        $successUrl = url('/landlord/payment/return');
        $cancelUrl  = url('/landlord/payment/cancelled');
        $errorUrl   = url('/landlord/payment/cancelled');
        $notifyUrl  = url('/payment/notify'); // the same signed notify address the bookings use

        // Ozow field order, lower-cased, then SHA512 (same as the booking payments)
        $hashString = strtolower($siteCode . 'ZA' . 'ZAR' . $amount . $reference . $bankReference
            . $cancelUrl . $errorUrl . $successUrl . $notifyUrl . $isTest . $privateKey);

        $fields = [
            'SiteCode' => $siteCode, 'CountryCode' => 'ZA', 'CurrencyCode' => 'ZAR', 'Amount' => $amount,
            'TransactionReference' => $reference, 'BankReference' => $bankReference, 'HashCheck' => hash('sha512', $hashString),
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

    // The landlord comes back from the bank. This page NEVER marks anything as paid:
    // only the signed message Ozow sends to /payment/notify can do that.
    public function back(Request $request)
    {
        $invoice = null;
        $reference = $request->query('TransactionReference');
        if ($reference && preg_match('/^INV-(\d+)$/', $reference, $m)) {
            $invoice = Invoice::where('user_id', auth()->id())->find((int) $m[1]);
        }
        if (! $invoice) {
            return redirect()->route('landlord.invoices');
        }

        $to = redirect()->route('landlord.invoices.show', $invoice);
        if ($invoice->isPaid()) {
            return $to->with('success', 'Thank you! Your payment is confirmed and your receipt is ready.');
        }
        $status = $request->query('Status');
        if ($status && strtolower($status) !== 'complete') {
            return $to->with('error', 'Your bank did not confirm the payment. You can try again below.');
        }

        return $to->with('success', 'Thank you! We are waiting for your bank to confirm the payment. Refresh this page in a minute or two.');
    }

    public function cancelled()
    {
        return redirect()->route('landlord.invoices')
            ->with('error', 'The payment was cancelled, so nothing was charged. You can try again from the invoice.');
    }

    private function own(Invoice $invoice): void
    {
        abort_unless((int) $invoice->user_id === (int) auth()->id(), 404);
    }
}
