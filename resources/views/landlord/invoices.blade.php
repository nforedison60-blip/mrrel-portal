@extends('layouts.landlord')

@section('content')
@php
    $unpaid = $invoices->where('status', 'Unpaid');
    $paid = $invoices->where('status', 'Paid');
    $chips = ['Unpaid' => 'wn', 'Paid' => 'ok', 'Cancelled' => ''];
@endphp

    <h1>Invoices</h1>
    <div class="m">When MrRel concludes a lease for one of your properties, the letting fee invoice appears here. You can pay online or by EFT and download your receipt.</div>

    <div class="g3" style="margin-top: 16px;">
        <div class="card"><div class="m sm">To pay</div><div class="big">R{{ number_format($unpaid->sum('amount'), 2) }}</div><div class="m sm">{{ $unpaid->count() }} {{ \Illuminate\Support\Str::plural('invoice', $unpaid->count()) }} waiting</div></div>
        <div class="card"><div class="m sm">Paid</div><div class="big">R{{ number_format($paid->sum('amount'), 2) }}</div><div class="m sm">{{ $paid->count() }} {{ \Illuminate\Support\Str::plural('invoice', $paid->count()) }}</div></div>
        <div class="card"><div class="m sm">Receipts</div><div class="big">{{ $paid->count() }}</div><div class="m sm">Ready to download</div></div>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>Your invoices</h3>
        @if($invoices->isEmpty())
            <p class="m">No invoices yet. You will see one here once a tenant has accepted and the lease is concluded.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Invoice</th>
                    <th>For</th>
                    <th>Amount</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($invoices as $invoice)
                    <tr>
                        <td><b>{{ $invoice->number }}</b><div class="m sm">{{ $invoice->created_at->format('d M Y') }}</div></td>
                        <td>{{ $invoice->description }}</td>
                        <td>R{{ number_format($invoice->amount, 2) }}</td>
                        <td>{{ $invoice->due_at ? $invoice->due_at->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="chip {{ $chips[$invoice->status] ?? '' }}">{{ $invoice->status }}</span>
                            @if($invoice->isOverdue()) <span class="chip wn">Overdue</span> @endif
                        </td>
                        <td>
                            @if($invoice->status === 'Unpaid')
                                <a href="{{ route('landlord.invoices.show', $invoice) }}" class="btn" style="padding:6px 14px;font-size:13px;margin:0;text-decoration:none;">Pay now</a>
                            @elseif($invoice->status === 'Paid')
                                <a href="{{ route('landlord.invoices.receipt', $invoice) }}" class="btn g" style="padding:6px 14px;font-size:13px;margin:0;text-decoration:none;">Receipt</a>
                            @else
                                <a href="{{ route('landlord.invoices.show', $invoice) }}" class="btn g" style="padding:6px 14px;font-size:13px;margin:0;text-decoration:none;">View</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
