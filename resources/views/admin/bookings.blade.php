@extends('layouts.admin')

@section('content')
    <!-- BOOKINGS SECTION -->
    <h1>Bookings</h1>
    <div class="card" style="margin-top: 20px;">
        <h3>Bookings</h3>
        <table class="tb">
            <tr>
                <th>Booking</th>
                <th>Provider</th>
                <th>Job day</th>
                <th>Chat and GPS log</th>
            </tr>
            @foreach($bookings as $booking)
            <tr>
                <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $booking->quote->provider_name ?? 'Unknown' }}</td>
                <td>{{ $booking->tracking_status }}</td>
                <td><button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">View</button></td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- PROPERTIES SECTION -->
    <h1 style="margin-top: 30px;">Properties</h1>
    <div class="card">
        <h3>Private catalogue</h3>
        <table class="tb">
            <tr>
                <th>Property</th>
                <th>City</th>
                <th>Status</th>
            </tr>
            @foreach($properties as $property)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 32px; height: 32px; background: #E3F1F2; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><svg width="20" height="20" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg></div>
                        {{ $property->title }}
                    </div>
                </td>
                <td>{{ $property->city }}</td>
                <td><span class="chip {{ $property->status == 'Approved' ? 'ok' : 'wn' }}">{{ $property->status }}</span></td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- SUBSCRIPTIONS SECTION -->
    <h1 style="margin-top: 30px;">Subscriptions</h1>
    <div class="card">
        <h3>Providers</h3>
        <table class="tb">
            <tr>
                <th>Provider</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Next billing</th>
            </tr>
            @foreach($providers as $provider)
            <tr>
                <td>{{ $provider->name }} · {{ $provider->company_name }}</td>
                <td>{{ $provider->verification_status == 'Verified' ? 'Pro' : 'Basic' }}</td>
                <td><span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">{{ $provider->verification_status == 'Verified' ? 'Active' : 'Waiting' }}</span></td>
                <td>[date]</td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection