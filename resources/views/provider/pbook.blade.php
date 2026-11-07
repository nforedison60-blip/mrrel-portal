@extends('layouts.provider')

@section('content')
    <h1>Bookings, payouts and reviews</h1>
    <div class="m">Expected earnings are not a cash balance.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Bookings</h3>
        @if($bookings->isEmpty())
            <p class="m">No bookings yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $booking->customer_name }}</td>
                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                    <td>R{{ number_format($booking->total_price, 2) }}</td>
                    <td><span class="chip ok">{{ $booking->status }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Payout ledger</h3>
        @if($bookings->isEmpty())
            <p class="m">No payouts yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Customer paid</th>
                    <th>Job complete</th>
                    <th>Ready for payout</th>
                    <th>Paid out</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>No</td>
                    <td>—</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Reviews</h3>
            <ul>
                <li>5 stars: On time and careful. Example.</li>
                <li>4 stars: Good price. Example.</li>
            </ul>
            <button type="button" class="btn g">Respond</button>
        </div>
        <div class="card">
            <h3>Problems</h3>
            <ul>
                <li>No active problems.</li>
            </ul>
            <button type="button" class="btn g">Add evidence</button>
        </div>
    </div>
@endsection