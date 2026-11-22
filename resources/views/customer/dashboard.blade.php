@extends('layouts.app')

@section('content')
@php
    $uid = auth()->id();
    $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
    $isNew = $requestsCount == 0 && $bookingsCount == 0;
    $plural = fn($n, $word) => $n . ' ' . \Illuminate\Support\Str::plural($word, $n);

    // Requests: drafts have not been sent to providers yet
    $draftCount  = \App\Models\ServiceRequest::where('user_id', $uid)->where('status', 'Draft')->count();
    $activeCount = max($requestsCount - $draftCount, 0);

    // The customer's latest booking, and the move it belongs to
    $bookedQuote  = optional($nextBooking)->quote;
    $providerName = optional($bookedQuote)->provider_name ?: 'Your provider';
    $bookedMove   = optional($bookedQuote)->serviceRequest;
    $moveDate     = ($bookedMove && $bookedMove->move_date) ? \Carbon\Carbon::parse($bookedMove->move_date) : null;

    // What needs the customer's attention right now
    $todo = [];
    if ($draftCount > 0) {
        $todo[] = ['text' => $plural($draftCount, 'draft request') . ' not sent yet. Finish and send it to get quotes.', 'url' => route('customer.requests'), 'cta' => 'Open my requests'];
    }
    if ($activeCount > 0 && $quotesCount == 0) {
        $todo[] = ['text' => 'Waiting for quotes on your ' . $plural($activeCount, 'active request') . '.', 'url' => route('customer.requests'), 'cta' => 'View my requests'];
    }
    if ($quotesCount > 0 && !$nextBooking) {
        $todo[] = ['text' => 'You have ' . $plural($quotesCount, 'quote') . ' to compare. Pick one to book your move.', 'url' => route('customer.quotes'), 'cta' => 'Compare quotes'];
    }
    if ($nextBooking && $nextBooking->status !== 'Paid') {
        $todo[] = ['text' => 'Your booking with ' . $providerName . ' is waiting for payment.', 'url' => route('payment.checkout', ['booking' => $nextBooking->id]), 'cta' => 'Pay now'];
    }
    if ($nextBooking && $nextBooking->status === 'Paid' && !$nextBooking->job_confirmed) {
        $todo[] = ['text' => 'Your move with ' . $providerName . ' is booked. Follow it live or send a message.', 'url' => route('customer.tracking'), 'cta' => 'Track my move'];
    }

    // Recent activity, built only from things that really happened
    $events = collect();
    foreach ($latestRequests as $r) {
        $where = ($r->moving_from && $r->moving_to) ? ' from ' . $r->moving_from . ' to ' . $r->moving_to : '';
        $events->push(['at' => $r->created_at, 'text' => (($r->status ?? 'Active') === 'Draft' ? 'Saved a draft request' : 'Sent a move request') . $where]);
    }
    $recentQuotes = \App\Models\Quote::whereHas('serviceRequest', fn($q) => $q->where('user_id', $uid))->latest()->take(3)->get();
    foreach ($recentQuotes as $qt) {
        $events->push(['at' => $qt->created_at, 'text' => ($qt->provider_name ?: 'A provider') . ' sent a quote of R' . number_format($qt->total_price, 2)]);
    }
    $recentBookings = \App\Models\Booking::where('user_id', $uid)->with('quote')->latest()->take(2)->get();
    foreach ($recentBookings as $bk) {
        $events->push(['at' => $bk->created_at, 'text' => 'Booked ' . (optional($bk->quote)->provider_name ?: 'a provider') . ' for R' . number_format($bk->total_price, 2)]);
    }
    $events = $events->filter(fn($e) => $e['at'])->sortByDesc('at')->take(5);
@endphp

    <h1>{{ $isNew ? 'Welcome' : 'Welcome back' }}, {{ $firstName }}</h1>
    <div class="m">Everything that needs you, in one place.</div>

    <div class="g4">
        <a href="{{ route('customer.requests') }}" class="card" style="display:block;text-decoration:none;color:inherit;">
            <div class="m sm">Active requests</div>
            <div class="big">{{ $activeCount }}</div>
            <div class="m sm">
                @if($draftCount > 0) {{ $plural($draftCount, 'draft') }} not sent
                @elseif($activeCount > 0) Sent to providers
                @else None yet @endif
            </div>
        </a>
        <a href="{{ route('customer.quotes') }}" class="card" style="display:block;text-decoration:none;color:inherit;">
            <div class="m sm">Quotes received</div>
            <div class="big">{{ $quotesCount }}</div>
            <div class="m sm">{{ $quotesCount > 0 ? 'Compare and choose one' : 'Quotes will appear here' }}</div>
        </a>
        <a href="{{ route('customer.bookings') }}" class="card" style="display:block;text-decoration:none;color:inherit;">
            <div class="m sm">Next move</div>
            <div class="big">{{ $moveDate ? $moveDate->format('D d M') : 'None' }}</div>
            <div class="m sm">{{ $nextBooking ? $providerName : 'No booking yet' }}</div>
        </a>
        <a href="{{ route('customer.search') }}" class="card" style="display:block;text-decoration:none;color:inherit;">
            <div class="m sm">Places to view</div>
            <div class="big">{{ $propertiesCount }}</div>
            <div class="m sm">{{ $propertiesCount > 0 ? 'Browse available places' : 'No places listed yet' }}</div>
        </a>
    </div>

    <div class="g2">
        <div class="card">
            @if($isNew)
                <h3>Get started</h3>
                <p class="m">Tell us what you are moving, where from and where to. Providers will send you quotes to compare.</p>
                <a href="{{ route('customer.new-request') }}" class="btn" style="margin-top:8px;text-decoration:none;">Make my first request</a>
            @else
                <h3>Needs your attention</h3>
                @forelse($todo as $item)
                    <div style="padding:10px 0;border-bottom:1px solid #EEF2F5;">
                        <div>{{ $item['text'] }}</div>
                        <a href="{{ $item['url'] }}" class="btn g" style="margin:8px 0 0;padding:6px 14px;font-size:13px;text-decoration:none;">{{ $item['cta'] }}</a>
                    </div>
                @empty
                    <p class="m">You are all caught up. Nothing needs your attention right now.</p>
                @endforelse
            @endif
        </div>
        <div class="card">
            <h3>Start something new</h3>
            <p class="m">Choose what you need.</p>
            <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">New move request</a>
            <a href="{{ route('customer.search') }}" class="btn g" style="text-decoration:none;">Find a place</a>
        </div>
    </div>

    @if($nextBooking)
    <div class="card" style="margin-top:16px;">
        <h3>Your next provider</h3>
        <div class="row" style="margin-top:12px;">
            <div class="av" style="width:60px;height:60px;font-size:24px;">{{ strtoupper(substr($providerName, 0, 1)) }}</div>
            <div>
                <b>{{ $providerName }}</b>
                <div class="m sm">{{ $moveDate ? 'Move date: ' . $moveDate->format('D d M Y') : 'Booked ' . $nextBooking->created_at->format('D d M, H:i') }}</div>
                @if($nextBooking->status === 'Paid')
                    <a href="{{ route('customer.tracking') }}" class="btn" style="margin-top:8px;padding:6px 14px;font-size:13px;text-decoration:none;">Track my move</a>
                @else
                    <a href="{{ route('payment.checkout', ['booking' => $nextBooking->id]) }}" class="btn" style="margin-top:8px;padding:6px 14px;font-size:13px;text-decoration:none;">Pay now</a>
                @endif
                <a href="{{ route('customer.bookings') }}" class="btn g" style="margin-top:8px;padding:6px 14px;font-size:13px;text-decoration:none;">Open booking and chat</a>
            </div>
        </div>
    </div>
    @endif

    <div class="card" style="margin-top:16px;">
        <h3>Recent activity</h3>
        @forelse($events as $event)
            <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid #EEF2F5;">
                <span>{{ $event['text'] }}</span>
                <span class="m sm" style="white-space:nowrap;">{{ $event['at']->format('d M, H:i') }}</span>
            </div>
        @empty
            <p class="m">Nothing yet. Your requests, quotes and bookings will show up here.</p>
        @endforelse
    </div>
@endsection
