@extends('layouts.provider')

@section('content')
    <h1>Welcome back, Pieter</h1>
    <div class="m">Sample Movers B</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Subscription</div><div class="big">Pro</div><div class="m sm">Active · next billing [date]</div></div>
        <div class="card"><div class="m sm">Verification</div><div class="big">Verified</div><div class="m sm">Photo, vehicle, plate, documents</div></div>
        <div class="card"><div class="m sm">Quotes Sent</div><div class="big">{{ $quotesCount }}</div><div class="m sm">All time</div></div>
        <div class="card"><div class="m sm">Active Bookings</div><div class="big">{{ $bookingsCount }}</div><div class="m sm">Confirmed jobs</div></div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>New Requests (Needs Quote)</h3>
        @if($requests->isEmpty())
            <p class="m">No new requests at the moment.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Request Details</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                @foreach($requests as $req)
                <tr>
                    <td>
                        <b>{{ $req->services_needed }}</b><br>
                        <span class="m sm">{{ $req->moving_from }} to {{ $req->moving_to }}</span>
                    </td>
                    <td>{{ $req->move_date }}</td>
                    <td>
                        <a href="{{ route('provider.send', $req->id) }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration:none;">Send quote</a>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Next jobs</h3>
            <ul>
                <li>BKG-0101 · Sat 31 Oct · Parklands to Bellville</li>
                <li>BKG-0104 · Mon 2 Nov · Claremont to Wynberg</li>
            </ul>
        </div>
        <div class="card">
            <h3>Payout status</h3>
            <ul>
                <li>Ready for payout: 1</li>
                <li>Waiting for customer confirmation: 1</li>
                <li>Paid out this month: R3,200</li>
            </ul>
        </div>
    </div>
@endsection