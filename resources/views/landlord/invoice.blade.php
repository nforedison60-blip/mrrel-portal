@extends('layouts.landlord')

@section('content')
@php
    $biz = config('mrrel.business');
    $bank = config('mrrel.bank');
    $hasBank = filled($bank['account']);
    $chips = ['Unpaid' => 'wn', 'Paid' => 'ok', 'Cancelled' => ''];
@endphp
<style>@media print { nav, .no-print, .sd, .tp { display: none !important; } }</style>

    <div class="row no-print" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <h1>Invoice {{ $invoice->number }}</h1>
            <div class="m">Issued {{ $invoice->created_at->format('d M Y') }}@if($invoice->due_at), due {{ $invoice->due_at->format('d M Y') }}@endif</div>
        </div>
        <div>
            <a href="{{ route('landlord.invoices') }}" class="btn g" style="text-decoration:none;">All invoices</a>
            <button type="button" class="btn g" onclick="window.print()">Print or save as PDF</button>
        </div>
    </div>

    <div class="sp">
        <div class="card">
            <div class="row" style="justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h3 style="margin:0;">{{ $biz['name'] }}</h3>
                    <div class="m sm">{{ $biz['email'] }}@if($biz['whatsapp']) &middot; WhatsApp {{ $biz['whatsapp'] }}@endif</div>
                    @if($biz['reg_no'])<div class="m sm">Reg. no. {{ $biz['reg_no'] }}</div>@endif
                    @if($biz['vat_no'])<div class="m sm">VAT no. {{ $biz['vat_no'] }}</div>@endif
                </div>
                <div style="text-align:right;">
                    <div class="m sm">Invoice to</div>
                    <b>{{ $landlord->name }}</b>
                    <div class="m sm">{{ $landlord->email }}</div>
                    @if($landlord->phone)<div class="m sm">{{ $landlord->phone }}</div>@endif
                </div>
            </div>

            <table class="tb" style="margin-top: 20px;">
                <tr><th>Description</th><th style="text-align:right;">Amount</th></tr>
                <tr>
                    <td>
                        {{ $invoice->description }}
                        @if($invoice->property)
                            <div class="m sm">{{ $invoice->property->suburb }}, {{ $invoice->property->city }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">R{{ number_format($invoice->amount, 2) }}</td>
                </tr>
                <tr><td style="text-align:right;"><b>Total due</b></td><td style="text-align:right;"><b>R{{ number_format($invoice->amount, 2) }}</b></td></tr>
            </table>

            <div style="margin-top: 14px;">
                <span class="chip {{ $chips[$invoice->status] ?? '' }}">{{ $invoice->status }}</span>
                @if($invoice->isOverdue()) <span class="chip wn">Overdue</span> @endif
                @if($invoice->isPaid())
                    <span class="m sm">Paid {{ $invoice->paid_at->format('d M Y') }} by {{ $invoice->payment_method === 'Ozow' ? 'online EFT' : 'EFT' }}</span>
                @endif
            </div>
        </div>

        <div>
            @if($invoice->status === 'Unpaid')
                <div class="card no-print">
                    <h3>Pay online</h3>
                    <p class="m sm">Instant EFT through your own bank. Your receipt is issued as soon as your bank confirms.</p>
                    <form method="POST" action="{{ route('landlord.invoices.pay', $invoice) }}">
                        @csrf
                        <button type="submit" class="btn" style="width:100%;">Pay R{{ number_format($invoice->amount, 2) }} now</button>
                    </form>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Or pay by EFT</h3>
                    @if($hasBank)
                        <table class="tb">
                            @if($bank['name'])<tr><td>Bank</td><td style="text-align:right;">{{ $bank['name'] }}</td></tr>@endif
                            <tr><td>Account holder</td><td style="text-align:right;">{{ $bank['holder'] }}</td></tr>
                            <tr><td>Account number</td><td style="text-align:right;">{{ $bank['account'] }}</td></tr>
                            @if($bank['type'])<tr><td>Account type</td><td style="text-align:right;">{{ $bank['type'] }}</td></tr>@endif
                            @if($bank['branch'])<tr><td>Branch code</td><td style="text-align:right;">{{ $bank['branch'] }}</td></tr>@endif
                            <tr><td><b>Reference</b></td><td style="text-align:right;"><b>{{ $invoice->number }}</b></td></tr>
                        </table>
                        <p class="m sm" style="margin-top:10px;">Use the reference exactly as shown, then email your proof of payment to {{ config('mrrel.pop_email') }} with {{ $invoice->number }} in the subject. MrRel marks the invoice as paid when the money reaches the account, and your receipt appears here.</p>
                    @else
                        <p class="m sm">MrRel's bank details are not set up yet. Please pay online, or contact {{ $biz['email'] }}.</p>
                    @endif
                </div>
            @elseif($invoice->isPaid())
                <div class="card no-print">
                    <h3>Paid, thank you</h3>
                    <p class="m sm">Receipt {{ $invoice->receipt_number }}</p>
                    <a href="{{ route('landlord.invoices.receipt', $invoice) }}" class="btn" style="text-decoration:none;display:block;text-align:center;">View receipt</a>
                </div>
            @else
                <div class="card"><h3>Cancelled</h3><p class="m sm">This invoice was cancelled. Nothing is owed.</p></div>
            @endif
        </div>
    </div>
@endsection
