@extends('layouts.provider')

@section('content')
    <!-- Profile Header -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="av" style="width: 80px; height: 80px; font-size: 32px;">P</div>
        <div>
            <h1 style="margin-bottom: 4px;">Welcome back, Pieter</h1>
            <div class="m">Sample Movers B</div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Subscription</div>
            <div class="big">Pro</div>
            <div class="m sm">Active · next billing [date]</div>
        </div>
        <div class="card">
            <div class="m sm">Verification</div>
            <div class="big">Verified</div>
            <div class="m sm">Photo, vehicle, plate, documents</div>
        </div>
        <div class="card">
            <div class="m sm">Requests this month</div>
            <div class="big">12</div>
            <div class="m sm">4 quotes sent</div>
        </div>
        <div class="card">
            <div class="m sm">Rating</div>
            <div class="big">4.8</div>
            <div class="m sm">120 jobs · 94% on time</div>
        </div>
    </div>

    <!-- New Requests Table -->
    <div class="card" style="margin-top: 20px;">
        <h3>New requests</h3>
        @if($requests->isEmpty())
            <p class="m">No new requests at the moment.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Request</th>
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
                        <!-- Using the route to send quote, passing the real ID -->
                        <a href="{{ route('provider.send', $req->id) }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration:none;">Send quote</a>
                    </td>
                </tr>
                @endforeach
                <!-- Adding the hardcoded "Instant price" row from the design for visual accuracy -->
                <tr>
                    <td>
                        <b>Small items</b><br>
                        <span class="m sm">Claremont to Wynberg</span>
                    </td>
                    <td>Mon 2 Nov</td>
                    <td>Instant price from your rate card</td>
                </tr>
            </table>
        @endif
    </div>

    <!-- Bottom Two Cards -->
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