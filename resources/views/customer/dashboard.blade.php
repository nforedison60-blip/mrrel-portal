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
            <div class="m sm">Active bookings</div>
            <div class="big">{{ $bookingsCount }}</div>
            <div class="m sm">Jobs confirmed</div>
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

    <div class="card" style="margin-top:16px;">
        <h3>Your recent requests</h3>
        @if($latestRequests->isEmpty())
            <p class="m">You haven't submitted any requests yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Service</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                </tr>
                @foreach($latestRequests as $req)
                <tr>
                    <td>{{ $req->services_needed }}</td>
                    <td>{{ $req->moving_from }}</td>
                    <td>{{ $req->moving_to ?? '—' }}</td>
                    <td>{{ $req->move_date }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection