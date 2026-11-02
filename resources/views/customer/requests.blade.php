@extends('layouts.app')

@section('content')
    <div class="row" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <h1>My requests</h1>
            <div class="m">All your moving and service requests in one place.</div>
        </div>
        <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">+ New request</a>
    </div>

    @if($requests->isEmpty())
        <div class="card">
            <p class="m">You haven't submitted any requests yet.</p>
            <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">Create your first request</a>
        </div>
    @else
        <div class="g2">
            @foreach($requests as $req)
                <div class="card">
                    <div class="row" style="justify-content: space-between; margin-bottom: 12px;">
                        <h3 style="margin:0;">{{ $req->services_needed }}</h3>
                        <span class="chip ok">Active</span>
                    </div>
                    
                    <div style="margin-bottom: 12px;">
                        <div class="m sm"><b>From:</b> {{ $req->moving_from }}</div>
                        @if($req->moving_to)
                            <div class="m sm"><b>To:</b> {{ $req->moving_to }}</div>
                        @endif
                        <div class="m sm"><b>Date:</b> {{ $req->move_date }} ({{ $req->date_flexibility }})</div>
                        <div class="m sm"><b>Size:</b> {{ $req->item_size }}</div>
                        
                        @if($req->items_count)
                            <div class="m sm"><b>Items:</b> {{ $req->items_count }}</div>
                        @endif
                        
                        @if($req->budget)
                            <div class="m sm"><b>Budget:</b> {{ $req->budget }}</div>
                        @endif
                        
                        <div class="m sm"><b>Pickup Access:</b> {{ $req->access_pickup }}</div>
                        <div class="m sm"><b>Delivery Access:</b> {{ $req->access_delivery }}</div>
                        
                        @if($req->details)
                            <div class="m sm" style="margin-top:8px;"><b>Details:</b> {{ $req->details }}</div>
                        @endif
                    </div>
                    
                    <div style="border-top:1px solid #EEF2F5; padding-top:12px; display:flex; gap:8px;">
                        <button type="button" class="btn g" style="margin:0;">View quotes</button>
                        <button type="button" class="btn g" style="margin:0;">Edit</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection