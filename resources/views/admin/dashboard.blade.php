@extends('layouts.admin')

@section('content')
    <h1>Admin Dashboard</h1>
    <div class="m">Overview of the MrRel platform.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Total Requests</div><div class="big">{{ $totalRequests }}</div></div>
        <div class="card"><div class="m sm">Active Bookings</div><div class="big">{{ $totalBookings }}</div></div>
        <div class="card"><div class="m sm">Quotes Sent</div><div class="big">{{ $totalQuotes }}</div></div>
        <div class="card"><div class="m sm">Revenue</div><div class="big">R{{ number_format($revenue, 2) }}</div></div>
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>To-do</h3>
            <ul>
                <li>Approve 3 new providers</li>
                <li>Review 2 property listings</li>
                <li>Resolve 1 dispute</li>
            </ul>
        </div>
        <div class="card">
            <h3>Quick Links</h3>
            <a href="{{ route('admin.map') }}" class="btn" style="text-decoration:none;">View Portal Map</a>
        </div>
    </div>
@endsection