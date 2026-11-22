@extends('layouts.provider')

@section('content')
    <!-- Profile Header -->
    <div class="hero">
        <i class="hs a"></i><i class="hs b"></i><i class="hs c"></i>
        <div class="av hc" style="width: 80px; height: 80px; font-size: 32px;">{{ strtoupper(substr($provider->name, 0, 1)) }}</div>
        <div class="hc" style="flex:1;min-width:220px;">
            @php
                // First visit = no quotes sent yet, so say "Welcome" rather than "Welcome back"
                $isNewProvider = !\App\Models\Quote::where('provider_user_id', auth()->id())->exists();
            @endphp
            <h1>{{ $isNewProvider ? 'Welcome' : 'Welcome back' }}, {{ $provider->name }}</h1>
            <div class="m">{{ $provider->company_name ?: 'Add your business name under Profile and verification' }}</div>
        </div>
        <div class="hc"><a class="btn" href="{{ route('provider.requests') }}">See open requests</a></div>
    </div>

    <!-- Stats Cards -->
    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Plan</div>
            <div class="big" style="font-size:22px;">Founding member</div>
            <div class="m sm">No monthly fee · {{ round($commissionRate * 100) }}% commission</div>
        </div>
        <div class="card">
            <div class="m sm">Verification</div>
            <div class="big">{{ $provider->verification_status ?: 'Pending' }}</div>
            <div class="m sm">Photo, vehicle, plate, documents</div>
        </div>
        <div class="card">
            <div class="m sm">Open requests</div>
            <div class="big">{{ $openRequestsCount }}</div>
            <div class="m sm">{{ $quotesCount }} quotes sent</div>
        </div>
        <div class="card">
            <div class="m sm">Rating</div>
            <div class="big">{{ $provider->rating > 0 ? number_format($provider->rating, 1) : '–' }}</div>
            <div class="m sm">{{ $bookingsCount }} jobs</div>
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
            </table>
        @endif
    </div>

    <!-- Bottom Two Cards -->
    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Next jobs</h3>
            @if($nextJobs->isEmpty())
                <p class="m">No paid jobs yet. They appear here once a customer has paid.</p>
            @else
                <ul>
                    @foreach($nextJobs as $job)
                        @php $move = optional($job->quote)->serviceRequest; @endphp
                        <li>BKG-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}@if($move) · {{ $move->move_date }} · {{ $move->moving_from }} to {{ $move->moving_to }}@endif</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="card">
            <h3>Your earnings</h3>
            <ul>
                <li>Paid jobs: {{ $bookingsCount }}</li>
                <li>Total paid by customers: R{{ number_format($totalRevenue, 2) }}</li>
                <li>MrRel commission ({{ round($commissionRate * 100) }}%): R{{ number_format($totalRevenue * $commissionRate, 2) }}</li>
                <li><b>Your share: R{{ number_format($totalRevenue * (1 - $commissionRate), 2) }}</b></li>
            </ul>
            <div class="m sm" style="margin-top:8px;">Payouts are made by MrRel.</div>
        </div>
    </div>
@endsection