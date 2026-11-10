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

    <div class="card" style="margin-top: 20px;">
        <h3>Next jobs (Update Status)</h3>
        @if($nextJobs->isEmpty())
            <p class="m">No upcoming jobs.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Customer</th>
                    <th>Current Status</th>
                    <th>Update</th>
                </tr>
                @foreach($nextJobs as $job)
                <tr>
                    <td>BKG-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $job->customer_name }}</td>
                    <td><span class="chip ok">{{ $job->tracking_status }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('provider.booking.status', $job->id) }}" style="display: flex; gap: 8px;">
                            @csrf
                            <select name="status" style="margin:0; padding: 4px 8px; font-size: 13px;">
                                <option value="On my way" {{ $job->tracking_status == 'On my way' ? 'selected' : '' }}>On my way</option>
                                <option value="Arrived" {{ $job->tracking_status == 'Arrived' ? 'selected' : '' }}>Arrived</option>
                                <option value="Loading" {{ $job->tracking_status == 'Loading' ? 'selected' : '' }}>Loading</option>
                                <option value="In transit" {{ $job->tracking_status == 'In transit' ? 'selected' : '' }}>In transit</option>
                                <option value="Delivered" {{ $job->tracking_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="Complete" {{ $job->tracking_status == 'Complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                            <button type="submit" class="btn" style="padding: 4px 10px; font-size: 12px; margin:0;">Update</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection