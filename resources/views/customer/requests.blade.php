@extends('layouts.app')

@section('content')
@php
    $quoteCounts = \App\Models\Quote::whereIn('service_request_id', $requests->pluck('id'))
        ->selectRaw('service_request_id, count(*) as total')
        ->groupBy('service_request_id')
        ->pluck('total', 'service_request_id');
    $bookedRequestIds = \App\Models\Booking::where('user_id', auth()->id())->with('quote')->get()
        ->map(fn($b) => optional($b->quote)->service_request_id)
        ->filter()->all();
@endphp

    <div class="row" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <h1>My requests</h1>
            <div class="m">All your moving and service requests in one place.</div>
        </div>
        <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">+ New request</a>
    </div>

    @if($requests->isEmpty())
        <div class="card">
            <h3>No requests yet</h3>
            <p class="m">Tell us what you need moved or done, and providers will send you quotes to compare.</p>
            <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">Make your first request</a>
        </div>
    @else
        <div class="g2">
            @foreach($requests as $req)
                @php
                    $isDraft = ($req->status ?? 'Active') === 'Draft';
                    $isBooked = in_array($req->id, $bookedRequestIds);
                    $quoteCount = (int) ($quoteCounts[$req->id] ?? 0);
                @endphp
                <div class="card">
                    <div class="row" style="justify-content: space-between; margin-bottom: 12px;">
                        <h3 style="margin:0;">{{ $req->services_needed }}</h3>
                        @if($isDraft)
                            <span class="chip wn">Draft, not sent</span>
                        @elseif($isBooked)
                            <span class="chip ok">Booked</span>
                        @elseif($quoteCount > 0)
                            <span class="chip ok">{{ $quoteCount }} {{ \Illuminate\Support\Str::plural('quote', $quoteCount) }} received</span>
                        @else
                            <span class="chip">Waiting for quotes</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 12px;">
                        <div class="m sm"><b>From:</b> {{ $req->moving_from }}</div>
                        @if($req->moving_to)
                            <div class="m sm"><b>To:</b> {{ $req->moving_to }}</div>
                        @endif
                        <div class="m sm"><b>Date:</b> {{ \Carbon\Carbon::parse($req->move_date)->format('D d M Y') }} ({{ $req->date_flexibility }})</div>
                        <div class="m sm"><b>Items:</b> {{ $req->item_size }}@if($req->items_count), {{ $req->items_count }} {{ \Illuminate\Support\Str::plural('item', $req->items_count) }}@endif</div>
                    </div>

                    <div style="border-top:1px solid #EEF2F5; padding-top:12px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                        @if($isDraft)
                            <form method="POST" action="{{ route('customer.requests.publish', $req->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn" style="margin:0;">Send to providers</button>
                            </form>
                            <form method="POST" action="{{ route('customer.requests.delete', $req->id) }}" style="margin:0;" onsubmit="return confirm('Delete this draft?');">
                                @csrf
                                <button type="submit" class="btn g" style="margin:0;">Delete draft</button>
                            </form>
                        @elseif($isBooked)
                            <a href="{{ route('customer.bookings') }}" class="btn g" style="margin:0; text-decoration:none;">View booking</a>
                        @elseif($quoteCount > 0)
                            <a href="{{ route('customer.quotes') }}" class="btn" style="margin:0; text-decoration:none;">Compare quotes</a>
                        @else
                            <span class="m sm">Providers can see your request. Quotes will appear under Quotes.</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
