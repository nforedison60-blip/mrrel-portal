@extends('layouts.app')

@section('content')
@if($booking)
@php
    $quote = $booking->quote;
    $sr = optional($quote)->serviceRequest;
    $providerName = optional($quote)->provider_name ?: 'Your provider';
    $providerUserId = optional($quote)->provider_user_id;
    $isPaid = $booking->status === 'Paid';
    $ref = 'BKG-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
    $providerUser = $providerUserId ? \App\Models\User::find($providerUserId) : null;
    $provider = $providerUser ? \App\Models\Provider::where('email', $providerUser->email)->first() : null;
    $reviewed = $providerUserId
        ? \App\Models\Review::where('user_id', auth()->id())->where('provider_user_id', $providerUserId)->exists()
        : false;
    $steps = ['On my way', 'Arrived', 'Loading', 'In transit', 'Delivered'];
@endphp

    @if($allBookings->count() > 1)
        <nav class="stb" aria-label="Your bookings">
            @foreach($allBookings as $b)
                <a href="{{ route('customer.bookings', ['booking' => $b->id]) }}" class="{{ $b->id === $booking->id ? 'on' : '' }}">
                    BKG-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }} · {{ optional($b->quote)->provider_name ?: 'Provider' }}
                </a>
            @endforeach
        </nav>
    @endif

    <h1>Booking {{ $ref }}</h1>
    <div class="m">
        @if($sr)
            {{ $sr->services_needed }} · {{ \Carbon\Carbon::parse($sr->move_date)->format('D d M Y') }} · {{ $sr->moving_from }}@if($sr->moving_to) to {{ $sr->moving_to }}@endif
        @else
            Booked with {{ $providerName }}
        @endif
    </div>

    <div class="sp" style="margin-top: 20px;">
        <!-- LEFT COLUMN -->
        <div>
            <div class="card">
                <h3>Your provider</h3>
                <div class="row" style="margin-top: 12px;">
                    <x-provider-avatar :user-id="$providerUserId" :name="$providerName" :size="64" />
                    <div>
                        <b>{{ $providerName }}</b>
                        @if($isPaid && $provider && ($provider->vehicle || $provider->plate_number))
                            <div class="m sm">Vehicle: {{ $provider->vehicle }}@if($provider->vehicle && $provider->plate_number) · @endif{{ $provider->plate_number }}</div>
                        @elseif(!$isPaid)
                            <div class="m sm">Vehicle details are shared once your booking is paid.</div>
                        @endif
                    </div>
                </div>
                <div class="m sm" style="margin-top: 12px;">If someone else arrives, don't hand anything over. <a href="{{ route('customer.problems') }}" style="color:inherit;">Report a problem</a>.</div>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Progress</h3>
                @if(!$isPaid)
                    <p class="m sm" style="margin-top: 8px;">Tracking starts once your booking has been paid.</p>
                @elseif($booking->tracking_status === 'Not started' || !$booking->tracking_status)
                    <p class="m sm" style="margin-top: 8px;">{{ $providerName }} has not started yet. You will see updates here on the day.</p>
                @else
                    <p class="sm" style="margin-top: 8px;"><b>{{ $providerName }}</b> · {{ $booking->tracking_status }}</p>
                    <div class="pl" style="margin-top: 8px;">
                        @foreach($steps as $step)
                            <span class="chip {{ $booking->tracking_status === $step ? 'ok' : '' }}">{{ $step }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('customer.tracking') }}" class="btn g" style="margin-top: 12px; text-decoration:none;">Open live map</a>
                @endif
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Questions about this booking?</h3>
                <p class="m sm" style="margin-top: 8px;">Send {{ $providerName }} a message about {{ $ref }}.</p>
                <a href="{{ $providerUserId ? route('customer.messages', ['with' => $providerUserId]) : route('customer.messages') }}" class="btn g" style="text-decoration:none;">Message {{ $providerName }}</a>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Extras on the day</h3>
                @if($booking->extras_status == 'Approved')
                    <div style="margin-top: 12px;"><span class="chip ok">Approved</span></div>
                @elseif($booking->extras_status == 'Declined')
                    <div style="margin-top: 12px;"><span class="chip wn">Declined</span></div>
                @elseif($booking->extras_status == 'Proposed')
                    <p class="m sm" style="margin-top: 8px;">Your provider has proposed an extra charge.</p>
                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <form method="POST" action="{{ route('customer.extras.approve', $booking->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn">Approve extra</button>
                        </form>
                        <form method="POST" action="{{ route('customer.extras.decline', $booking->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn g">Decline</button>
                        </form>
                    </div>
                @else
                    <p class="m sm">No extras have been proposed for this job.</p>
                @endif
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>When the job is done</h3>
                @if($booking->job_confirmed)
                    <div style="margin-top: 12px;"><span class="chip ok">Job confirmed</span></div>
                @elseif(!$isPaid)
                    <p class="m sm" style="margin-top: 8px;">You can confirm the job after you have paid.</p>
                @else
                    <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                        <form method="POST" action="{{ route('customer.bookings.confirm', $booking->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn">Confirm the job is done</button>
                        </form>
                        <a href="{{ route('customer.problems') }}" class="btn g" style="text-decoration:none;">Report a problem</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <div class="card">
                <h3>Payment</h3>
                <div class="big">R{{ number_format($booking->total_price, 2) }}</div>
                <div style="margin-bottom: 12px;">
                    <span class="chip {{ $isPaid ? 'ok' : 'wn' }}">{{ $isPaid ? 'Paid' : 'Awaiting payment' }}</span>
                </div>
                @if($isPaid)
                    <div class="m sm">Payment received. Thank you.</div>
                @else
                    <a href="{{ route('payment.checkout', ['booking' => $booking->id]) }}" class="btn w" style="text-align: center; text-decoration: none;">Pay now</a>
                    <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}" style="margin:8px 0 0;" onsubmit="return confirm('Cancel this booking? You can then choose another quote.');">
                        @csrf
                        <button type="submit" class="btn g w">Cancel booking</button>
                    </form>
                @endif
            </div>

            @if($sr)
                <div class="card" style="margin-top: 16px;">
                    <h3>The job</h3>
                    <div class="m sm" style="margin-top: 8px;"><b>From:</b> {{ $sr->moving_from }}</div>
                    @if($sr->moving_to)<div class="m sm"><b>To:</b> {{ $sr->moving_to }}</div>@endif
                    <div class="m sm"><b>Date:</b> {{ \Carbon\Carbon::parse($sr->move_date)->format('D d M Y') }}</div>
                    <div class="m sm"><b>Items:</b> {{ $sr->item_size }}</div>
                    <div class="m sm"><b>Access:</b> {{ $sr->access_pickup }} at pickup, {{ $sr->access_delivery }} at delivery</div>
                </div>
            @endif

            <div class="card" style="margin-top: 16px;">
                <h3>Review your provider</h3>
                @if(!$booking->job_confirmed)
                    <p class="m sm" style="margin-top: 8px;">You can leave a review after you confirm the job is done.</p>
                @elseif($reviewed)
                    <div style="margin-top: 12px;"><span class="chip ok">Thanks, you have reviewed this provider</span></div>
                @else
                    <form method="POST" action="{{ route('customer.review.store') }}">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <div class="pl" style="margin-bottom: 12px;">
                            @foreach([5, 4, 3, 2, 1] as $star)
                                <label><input type="radio" name="rating" value="{{ $star }}" {{ $star === 5 ? 'required' : '' }}> {{ $star }}</label>
                            @endforeach
                        </div>
                        <div class="f">
                            <label class="k">Comment<input type="text" name="comment" maxlength="1000" placeholder="How was the service?"></label>
                        </div>
                        <button type="submit" class="btn w">Submit review</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="card" style="margin-top: 20px;">
        <h3>No bookings yet</h3>
        <p class="m">When you accept a quote and pay, your booking appears here with its progress.</p>
        <a href="{{ route('customer.quotes') }}" class="btn" style="text-decoration:none;">View quotes</a>
    </div>
@endif
@endsection
