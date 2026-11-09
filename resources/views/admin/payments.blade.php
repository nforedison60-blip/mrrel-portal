@extends('layouts.admin')

@section('content')
    <h1>Payments and Payouts</h1>
    <div class="m">Monitor platform revenue and provider payouts.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Total Revenue</div>
            <div class="big">R{{ number_format($totalRevenue, 2) }}</div>
            <div class="m sm">From all bookings</div>
        </div>
        <div class="card">
            <div class="m sm">Pending Payouts</div>
            <div class="big">R{{ number_format($pendingPayouts, 2) }}</div>
            <div class="m sm">Owed to providers</div>
        </div>
        <div class="card">
            <div class="m sm">Total Bookings</div>
            <div class="big">{{ $totalBookings }}</div>
            <div class="m sm">Completed transactions</div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Transaction Ledger</h3>
        @if($bookings->isEmpty())
            <p class="m">No transactions yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $booking->customer_name }}</td>
                    <td><b>R{{ number_format($booking->total_price, 2) }}</b></td>
                    <td><span class="chip ok">{{ $booking->status }}</span></td>
                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection