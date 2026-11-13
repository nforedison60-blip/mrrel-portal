@extends('layouts.provider')

@section('content')
    <h1>Bookings and Payouts</h1>
    <div class="m" style="margin-bottom: 20px;">Manage your active jobs.</div>

    @forelse($bookings as $booking)
        <div class="card" style="margin-bottom: 20px;">
            <div class="row" style="justify-content: space-between; margin-bottom: 12px;">
                <div>
                    <h3>Booking BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</h3>
                    <div class="m sm">Customer: {{ $booking->customer_name }}</div>
                </div>
                <div>
                    <span class="chip ok">R{{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>

            <div class="card" style="background: var(--bg); border: 1px solid var(--l); padding: 16px; margin-bottom: 16px;">
                <h4 style="margin-bottom: 12px;">Update Job Status</h4>
                <div class="pl">
                    <form method="POST" action="{{ route('provider.booking.status', $booking->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="On my way">
                        <button type="submit" class="btn {{ $booking->tracking_status == 'On my way' ? '' : 'g' }}">On my way</button>
                    </form>
                    
                    <form method="POST" action="{{ route('provider.booking.status', $booking->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="Arrived">
                        <button type="submit" class="btn {{ $booking->tracking_status == 'Arrived' ? '' : 'g' }}">Arrived</button>
                    </form>

                    <form method="POST" action="{{ route('provider.booking.status', $booking->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="Loading">
                        <button type="submit" class="btn {{ $booking->tracking_status == 'Loading' ? '' : 'g' }}">Loading</button>
                    </form>

                    <form method="POST" action="{{ route('provider.booking.status', $booking->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="In transit">
                        <button type="submit" class="btn {{ $booking->tracking_status == 'In transit' ? '' : 'g' }}">In transit</button>
                    </form>

                    <form method="POST" action="{{ route('provider.booking.status', $booking->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="Delivered">
                        <button type="submit" class="btn {{ $booking->tracking_status == 'Delivered' ? '' : 'g' }}">Delivered</button>
                    </form>
                </div>
                <div class="m sm" style="margin-top: 10px;">Current Status: <b>{{ $booking->tracking_status }}</b></div>
            </div>

            <a href="{{ route('provider.tracking') }}" class="btn">Start Live Tracking</a>
        </div>
    @empty
        <div class="card">
            <p class="m">You don't have any active bookings right now.</p>
        </div>
    @endforelse
@endsection