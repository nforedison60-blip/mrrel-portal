@extends('layouts.app')

@section('content')
@php
    // Which of this customer's requests already have a booking?
    $bookingByRequest = \App\Models\Booking::where('user_id', auth()->id())->with('quote')->get()
        ->filter(fn($b) => optional($b->quote)->service_request_id)
        ->keyBy(fn($b) => $b->quote->service_request_id);
    $hasRequests = \App\Models\ServiceRequest::where('user_id', auth()->id())->where('status', '!=', 'Draft')->exists();
@endphp

    <h1>Compare your quotes</h1>
    <div class="m" style="margin-bottom: 20px;">Review the quotes providers have sent you, then accept the one you like.</div>

    @forelse($quotes as $quote)
        @php
            $sr = $quote->serviceRequest;
            $booking = $bookingByRequest->get($quote->service_request_id);
            $thisOneBooked = $booking && $booking->quote_id == $quote->id;
        @endphp
        <div class="card" style="margin-top: 16px;">
            @if($sr)
                <div class="m sm" style="margin-bottom: 12px;">
                    For your request: <b>{{ $sr->services_needed }}</b>,
                    {{ $sr->moving_from }}@if($sr->moving_to) to {{ $sr->moving_to }}@endif,
                    {{ \Carbon\Carbon::parse($sr->move_date)->format('D d M Y') }}
                </div>
            @endif

            <div class="row" style="justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="row" style="margin-bottom: 8px;">
                        <div class="av">{{ strtoupper(substr($quote->provider_name, 0, 1)) }}</div>
                        <div>
                            <b>{{ $quote->provider_name }}</b>
                            <div class="m sm">Provider</div>
                        </div>
                    </div>
                    <div class="big">R{{ number_format($quote->total_price, 2) }}</div>
                    <div class="m sm">{{ $quote->price_type }} price</div>
                </div>
                <div style="text-align: right;">
                    @if($thisOneBooked)
                        <span class="chip ok">Booked</span>
                    @else
                        <span class="chip">Valid for {{ $quote->validity }}</span>
                    @endif
                </div>
            </div>

            <div class="g2" style="margin-top: 16px; margin-bottom: 16px;">
                <div class="m sm"><b>Included:</b> {{ $quote->included ?: 'None specified' }}</div>
                <div class="m sm"><b>Not included:</b> {{ $quote->not_included ?: 'None specified' }}</div>
            </div>

            @if($quote->note)
                <div class="m sm" style="background: var(--bg); padding: 12px; border-radius: 8px; border: 1px solid var(--l);">
                    <b>Provider note:</b> "{{ $quote->note }}"
                </div>
            @endif

            <div style="margin-top: 16px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                @if($thisOneBooked)
                    <a href="{{ route('customer.bookings', ['booking' => $booking->id]) }}" class="btn" style="text-decoration:none; margin:0;">
                        {{ $booking->status === 'Paid' ? 'View booking' : 'Go to booking and pay' }}
                    </a>
                @elseif($booking)
                    <span class="m sm">You have booked another provider for this request.</span>
                @else
                    <form method="POST" action="{{ route('customer.bookings.store') }}" style="margin:0;">
                        @csrf
                        <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                        <button type="submit" class="btn" style="margin:0;">Accept quote and pay</button>
                    </form>
                @endif
                <a href="{{ route('customer.quote.show', $quote->id) }}" class="btn g" style="text-decoration:none; margin:0;">View details</a>
            </div>
        </div>
    @empty
        <div class="card" style="margin-top: 20px; text-align: center; padding: 40px;">
            <h3>No quotes yet</h3>
            @if($hasRequests)
                <p class="m">Providers can see your request. Their quotes will appear here as soon as they send them.</p>
                <a href="{{ route('customer.requests') }}" class="btn" style="text-decoration:none;">View my requests</a>
            @else
                <p class="m">Quotes come from providers after you send a request. Start by telling us what you need.</p>
                <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">Make a request</a>
            @endif
        </div>
    @endforelse
@endsection
