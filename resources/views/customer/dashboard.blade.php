@extends('layouts.app')

@section('content')
    <h1>Welcome back, Lerato</h1>
    <div class="m">Everything that needs you, in one place.</div>
    
    <div class="g4">
        <div class="card">
            <div class="m sm">Active requests</div>
            <div class="big">{{ $requestsCount }}</div>
            <div class="m sm">Saved in database</div>
        </div>
        <div class="card">
            <div class="m sm">Quotes to review</div>
            <div class="big">{{ $quotesCount }}</div>
            <div class="m sm">Available to compare</div>
        </div>
        <div class="card">
            <div class="m sm">Next booking</div>
            <div class="big">Sat 31 Oct</div>
            <div class="m sm">Provider arrives 08:00</div>
        </div>
        <div class="card">
            <div class="m sm">Shortlist</div>
            <div class="big">{{ $propertiesCount }}</div>
            <div class="m sm">Properties available</div>
        </div>
    </div>

    <div class="g2">
        <div class="card">
            <h3>Needs your attention</h3>
            <ul>
                <li>Compare quotes for your recent requests</li>
                <li>Check your shortlist for new properties</li>
                <li>Review your active bookings</li>
            </ul>
        </div>
        <div class="card">
            <h3>Start something new</h3>
            <p class="m">Choose what you need.</p>
            <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">New move request</a>
            <a href="{{ route('customer.shortlist') }}" class="btn g" style="text-decoration:none;">View my shortlist</a>
        </div>
    </div>

    <!-- NEW: YOUR NEXT PROVIDER CARD -->
    <div class="card" style="margin-top:16px;">
        <h3>Your next provider</h3>
        <div class="row" style="margin-top: 12px;">
            <div style="display:flex; gap:12px; align-items:center;">
                <div class="av" style="width:60px; height:60px; font-size:24px;">P</div>
                <div style="width:100px; height:60px; background:#E3F1F2; border:1px dashed #B9C6D2; border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--m); font-size:12px;">[Vehicle]</div>
            </div>
            <div>
                <b>Pieter · Sample Movers B</b>
                <div class="m sm">Sat 31 Oct, 08:00 to 10:00 · 1-ton bakkie · CA 000 000</div>
                <button type="button" class="btn g" style="margin-top: 8px; padding: 6px 14px; font-size: 13px;">Track and chat</button>
            </div>
        </div>
    </div>
@endsection