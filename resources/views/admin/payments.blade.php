@extends('layouts.admin')

@section('content')
@php
    $bookings->load('quote');
    $paid = $bookings->where('status', 'Paid');
    $thisMonth = $paid->filter(fn($b) => $b->created_at && $b->created_at->isCurrentMonth());
    $confirmed = $paid->where('job_confirmed', true);
@endphp

    <h1>Payments</h1>
    <div class="m">Money paid by customers, and which jobs they have confirmed. Payouts to providers are made by hand for now.</div>

    <div class="g4" style="margin-top:20px;">
        <div class="card">
            <div class="m sm">Received this month</div>
            <div class="big">R{{ number_format($thisMonth->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $thisMonth->count() }} {{ \Illuminate\Support\Str::plural('booking', $thisMonth->count()) }}</div>
        </div>
        <div class="card">
            <div class="m sm">Received in total</div>
            <div class="big">R{{ number_format($paid->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $paid->count() }} {{ \Illuminate\Support\Str::plural('booking', $paid->count()) }}</div>
        </div>
        <div class="card">
            <div class="m sm">Confirmed by customers</div>
            <div class="big">R{{ number_format($confirmed->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $confirmed->count() }} {{ \Illuminate\Support\Str::plural('job', $confirmed->count()) }} done</div>
        </div>
        <div class="card">
            <div class="m sm">Waiting for confirmation</div>
            <div class="big">R{{ number_format($paid->sum('total_price') - $confirmed->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $paid->count() - $confirmed->count() }} {{ \Illuminate\Support\Str::plural('job', $paid->count() - $confirmed->count()) }} open</div>
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Bookings</h3>
        @if($bookings->isEmpty())
            <p class="m">No bookings yet. They appear here when a customer books a quote.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Provider</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Job confirmed</th>
                    <th>Date</th>
                </tr>
                @foreach($bookings as $b)
                    <tr>
                        <td>BKG-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($b->quote)->provider_name ?: '—' }}</td>
                        <td>{{ $b->customer_name }}</td>
                        <td>R{{ number_format($b->total_price, 2) }}</td>
                        <td><span class="chip {{ $b->status === 'Paid' ? 'ok' : 'wn' }}">{{ $b->status }}</span></td>
                        <td><span class="chip {{ $b->job_confirmed ? 'ok' : '' }}">{{ $b->job_confirmed ? 'Yes' : 'Not yet' }}</span></td>
                        <td class="m sm">{{ optional($b->created_at)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection