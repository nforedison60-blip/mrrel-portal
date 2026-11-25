@extends('layouts.landlord')

@section('content')
@php
    $biz = config('mrrel.business');
@endphp
<style>@media print { nav, .no-print, .sd, .tp { display: none !important; } }</style>

    <div class="row no-print" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <h1>Receipt {{ $invoice->receipt_number }}</h1>
            <div class="m">Proof of payment for invoice {{ $invoice->number }}.</div>
        </div>
        <div>
            <a href="{{ route('landlord.invoices') }}" class="btn g" style="text-decoration:none;">All invoices</a>
            <button type="button" class="btn g" onclick="window.print()">Print or save as PDF</button>
        </div>
    </div>

    <div class="card" style="max-width: 640px;">
        <div class="row" style="justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="margin:0;">{{ $biz['name'] }}</h3>
                <div class="m sm">{{ $biz['email'] }}@if($biz['whatsapp']) &middot; WhatsApp {{ $biz['whatsapp'] }}@endif</div>
                @if($biz['reg_no'])<div class="m sm">Reg. no. {{ $biz['reg_no'] }}</div>@endif
                @if($biz['vat_no'])<div class="m sm">VAT no. {{ $biz['vat_no'] }}</div>@endif
            </div>
            <div style="text-align:right;">
                <div class="m sm">Received from</div>
                <b>{{ $landlord->name }}</b>
                <div class="m sm">{{ $landlord->email }}</div>
            </div>
        </div>

        <table class="tb" style="margin-top: 20px;">
            <tr><td>Receipt number</td><td style="text-align:right;">{{ $invoice->receipt_number }}</td></tr>
            <tr><td>For invoice</td><td style="text-align:right;">{{ $invoice->number }}</td></tr>
            <tr><td>Description</td><td style="text-align:right;">{{ $invoice->description }}</td></tr>
            <tr><td>Date paid</td><td style="text-align:right;">{{ $invoice->paid_at->format('d M Y, H:i') }}</td></tr>
            <tr><td>Payment method</td><td style="text-align:right;">{{ $invoice->payment_method === 'Ozow' ? 'Online EFT (Ozow)' : 'EFT' }}</td></tr>
            @if($invoice->payment_reference)<tr><td>Payment reference</td><td style="text-align:right;">{{ $invoice->payment_reference }}</td></tr>@endif
            <tr><td><b>Amount paid</b></td><td style="text-align:right;"><b>R{{ number_format($invoice->amount, 2) }}</b></td></tr>
        </table>
        <p class="m sm" style="margin-top: 14px;">Thank you for your payment.</p>
    </div>
@endsection
