@extends('layouts.provider')

@section('content')
    <h1>Welcome back, Pieter</h1>
    <div class="m">Sample Movers B</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Subscription</div><div class="big">Pro</div><div class="m sm">Active · next billing [date]</div></div>
        <div class="card"><div class="m sm">Verification</div><div class="big">Verified</div><div class="m sm">Photo, vehicle, plate, documents</div></div>
        <div class="card"><div class="m sm">Requests this month</div><div class="big">12</div><div class="m sm">4 quotes sent</div></div>
        <div class="card"><div class="m sm">Rating</div><div class="big">4.8</div><div class="m sm">120 jobs · 94% on time</div></div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>New requests</h3>
        <table class="tb">
            <tr>
                <th>Request</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>1 bed, Parklands to Bellville · 18 km</td>
                <td>Sat 31 Oct</td>
                <td><button type="button" class="btn" style="padding: 6px 14px; font-size: 13px;">Send quote</button></td>
            </tr>
            <tr>
                <td>2-bedroom flat, Sea Point to Rondebosch</td>
                <td>Sun 1 Nov</td>
                <td><button type="button" class="btn" style="padding: 6px 14px; font-size: 13px;">Send quote</button></td>
            </tr>
            <tr>
                <td>Small items, Claremont to Wynberg</td>
                <td>Mon 2 Nov</td>
                <td>Instant price from your rate card</td>
            </tr>
        </table>
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