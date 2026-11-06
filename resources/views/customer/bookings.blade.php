@extends('layouts.app')

@section('content')
    @if($booking)
        <h1>Booking BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</h1>
        <div class="m">Moving one bed · Sat 31 Oct · Parklands to Bellville</div>

        <div class="sp">
            <div>
                <div class="card">
                    <h3>Your provider</h3>
                    <div class="row">
                        <span class="av">P</span>
                        <div>
                            <b>Pieter</b> <span class="chip ok">Verified</span>
                            <div class="m sm">Sample Movers B · 1-ton bakkie</div>
                            <div class="m sm">4.8 rating</div>
                        </div>
                    </div>
                    <p class="sm" style="margin-top: 12px;">
                        <b>Vehicle:</b> White 1-ton bakkie · <b>Plate:</b> CA 000 000<br>
                        <b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00
                    </p>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Progress</h3>
                    <div style="margin-bottom: 8px;">
                        <span class="chip ok">Requested</span>
                        <span class="chip ok">Quote accepted</span>
                        <span class="chip ok">Paid</span>
                        <span class="chip">Job done</span>
                        <span class="chip">Confirmed</span>
                    </div>
                    <p class="m sm">Current step: {{ $booking->status }}</p>
                </div>
            </div>

            <div>
                <div class="card">
                    <h3>Payment</h3>
                    <div class="big">R{{ number_format($booking->total_price, 2) }}</div>
                    <div style="margin-bottom: 12px;"><span class="chip ok">{{ $booking->status }}</span></div>
                    <div class="m sm" style="margin-bottom: 12px;">Receipt BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}-R1</div>
                    <button type="button" class="btn g w">Download receipt</button>
                </div>
            </div>
        </div>
    @else
        <div class="card" style="margin-top: 20px;">
            <h3>No active bookings</h3>
            <p class="m">When you accept a quote, your booking details will appear here.</p>
            <a href="{{ route('customer.quotes') }}" class="btn" style="text-decoration:none;">View Quotes</a>
        </div>
    @endif
@endsection