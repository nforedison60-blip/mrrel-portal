@extends('layouts.admin')

@section('content')
    <h1>All Bookings</h1>
    <div class="m">Monitor all confirmed and paid bookings across the platform.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Confirmed Jobs</h3>
        @if($bookings->isEmpty())
            <p class="m">No bookings have been made yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Provider</th>
                    <th>Total Paid</th>
                    <th>Status</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <b>{{ $booking->customer_name }}</b><br>
                        <span class="m sm">{{ $booking->created_at->format('d M Y') }}</span>
                    </td>
                    <td>
                        @if($booking->quote)
                            {{ $booking->quote->provider_name }}
                        @else
                            <span class="m sm">Unknown</span>
                        @endif
                    </td>
                    <td><b>R{{ number_format($booking->total_price, 2) }}</b></td>
                    <td><span class="chip ok">{{ $booking->status }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection