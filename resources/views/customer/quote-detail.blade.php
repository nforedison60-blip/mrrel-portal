@extends('layouts.app')

@section('content')
@php
    $sr = $quote->serviceRequest;
    $booking = \App\Models\Booking::where('user_id', auth()->id())
        ->whereHas('quote', fn($q) => $q->where('service_request_id', $quote->service_request_id))
        ->first();
    $thisOneBooked = $booking && $booking->quote_id == $quote->id;
    $initial = strtoupper(substr($quote->provider_name, 0, 1));
@endphp

    <div class="m sm"><a href="{{ route('customer.quotes') }}" style="color:inherit;">Quotes</a> / QTN-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }}</div>
    <h1>Quote from {{ $quote->provider_name }}</h1>
    <div class="m" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <span>QTN-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }} · Sent {{ $quote->created_at->format('d M Y') }}</span>
        @if($thisOneBooked)
            <span class="chip ok">Booked</span>
        @elseif($booking)
            <span class="chip">You booked another provider</span>
        @else
            <span class="chip wn">Awaiting your acceptance</span>
        @endif
    </div>

    <div class="sp" style="margin-top: 20px;">
        <div>
            <div class="card">
                <h3>Your provider</h3>
                <div class="row" style="margin-top: 12px;">
                    <div class="av" style="width: 64px; height: 64px; font-size: 24px;">{{ $initial }}</div>
                    <div>
                        <b>{{ $quote->provider_name }}</b>
                        <div class="m sm">Contact and vehicle details are shared once you book and pay.</div>
                    </div>
                </div>
                <p class="m sm" style="margin-top: 12px;">This service is provided by {{ $quote->provider_name }}. MrRel provides the platform that connects you.</p>
            </div>

            @if($sr)
                <div class="card" style="margin-top: 16px;">
                    <h3>The job</h3>
                    <div class="g2" style="margin:8px 0 0">
                        <div><span class="m">Service:</span> {{ $sr->services_needed }}</div>
                        <div><span class="m">From:</span> {{ $sr->moving_from }}</div>
                        @if($sr->moving_to)<div><span class="m">To:</span> {{ $sr->moving_to }}</div>@endif
                        <div><span class="m">Date:</span> {{ \Carbon\Carbon::parse($sr->move_date)->format('D d M Y') }} ({{ $sr->date_flexibility }})</div>
                        <div><span class="m">Items:</span> {{ $sr->item_size }}@if($sr->items_count), {{ $sr->items_count }}@endif</div>
                        <div><span class="m">Pickup access:</span> {{ $sr->access_pickup }}</div>
                        <div><span class="m">Delivery access:</span> {{ $sr->access_delivery }}</div>
                        @if($sr->budget)<div><span class="m">Your budget:</span> {{ $sr->budget }}</div>@endif
                    </div>
                    @if($sr->details)
                        <p class="m sm" style="margin-top: 12px;"><b>Your notes:</b> {{ $sr->details }}</p>
                    @endif
                </div>
            @endif

            <div class="card" style="margin-top: 16px;">
                <h3>Price and terms</h3>
                <div class="g2" style="margin:8px 0 0">
                    <div><span class="m">Total:</span> <b>R{{ number_format($quote->total_price, 2) }}</b></div>
                    <div><span class="m">Price type:</span> {{ $quote->price_type }}</div>
                    <div><span class="m">Valid for:</span> {{ $quote->validity }}</div>
                </div>
                <div class="g2" style="margin-top: 16px;">
                    <div>
                        <b class="sm">Included</b>
                        <p class="sm m">{{ $quote->included ?: 'None specified' }}</p>
                    </div>
                    <div>
                        <b class="sm">Not included</b>
                        <p class="sm m">{{ $quote->not_included ?: 'None specified' }}</p>
                    </div>
                </div>
                @if($quote->note)
                    <p class="sm" style="margin-top: 12px;"><b>Provider note:</b> "{{ $quote->note }}"</p>
                @endif
                <p class="m sm" style="margin-top: 12px;">If the items, route or access change, ask the provider for a new quote before the job.</p>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>After the job</h3>
                <p class="sm m">When the work is finished, you confirm it in your bookings page, or report a problem. Problems can be reported at any time under Account & help.</p>
            </div>
        </div>

        <div class="st">
            <div class="card" style="border:2px solid var(--t);">
                <div class="m sm">Total</div>
                <div class="big" style="font-size:40px">R{{ number_format($quote->total_price, 2) }}</div>
                <span class="chip">{{ $quote->price_type }} price</span>

                <p class="m sm" style="margin-top: 16px;">
                    You pay securely online. The provider is paid after the job is confirmed, under MrRel's completion process.
                </p>

                @if($thisOneBooked)
                    <a href="{{ route('customer.bookings', ['booking' => $booking->id]) }}" class="btn" style="width:100%; margin-top:16px; text-align:center; text-decoration:none;">
                        {{ $booking->status === 'Paid' ? 'View booking' : 'Go to booking and pay' }}
                    </a>
                @elseif($booking)
                    <p class="m sm" style="margin-top: 16px;">You have already booked another provider for this request.</p>
                @else
                    <form method="POST" action="{{ route('customer.bookings.store') }}" style="margin-top: 16px;">
                        @csrf
                        <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                        <button type="submit" class="btn" style="width:100%;">Accept and pay</button>
                    </form>
                    <p class="m sm" style="margin-top: 16px;">No signature needed. We record your acceptance with the date and time, and you then go to the payment page.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
