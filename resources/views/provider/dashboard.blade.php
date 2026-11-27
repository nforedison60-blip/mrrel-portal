@extends('layouts.provider')

@section('content')
    <!-- Profile Header -->
    <div class="hero">
        <i class="hs a"></i><i class="hs b"></i><i class="hs c"></i>
        @php
            // Profile photo from the provider's profile (falls back to the first letter)
            $bannerPhoto = null;
            if ($provider->image_path) {
                try { $bannerPhoto = route('provider.photo', [$provider->id, 'profile']); } catch (\Throwable $e) { $bannerPhoto = null; }
            }
        @endphp
        <a href="{{ route('provider.profile') }}" class="hc" title="Edit your profile" style="text-decoration:none; display:block; line-height:0;">
            @if($bannerPhoto)
                <img src="{{ $bannerPhoto }}" alt="Your profile photo" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.9); box-shadow:0 6px 14px rgba(0,0,0,.25);" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            @endif
            <div class="av" style="width:80px; height:80px; font-size:32px; color:#003068; line-height:1; {{ $bannerPhoto ? 'display:none;' : '' }}">{{ strtoupper(substr($provider->name, 0, 1)) }}</div>
        </a>
        <div class="hc" style="flex:1;min-width:220px;">
            @php
                // First visit = no quotes sent yet, so say "Welcome" rather than "Welcome back"
                $isNewProvider = !\App\Models\Quote::where('provider_user_id', auth()->id())->exists();
            @endphp
            <h1>{{ $isNewProvider ? 'Welcome' : 'Welcome back' }}, {{ $provider->name }}</h1>
            <div class="m">
                @if($provider->company_name)
                    {{ $provider->company_name }}
                @else
                    <a href="{{ route('provider.profile') }}" style="color:#fff; text-decoration:underline;">Add your business name</a> so customers know who you are
                @endif
            </div>
            <div style="margin-top:10px;">
                @if($provider->verification_status === 'Verified')
                    <span class="chip ok">&#10003; Verified</span>
                @else
                    <span class="chip wn">Verification: {{ $provider->verification_status ?: 'Pending' }}</span>
                @endif
                @if($provider->base_label)
                    <span class="chip">Based in {{ $provider->base_label }}@if($provider->service_radius_km) &middot; {{ $provider->service_radius_km }} km @endif</span>
                @endif
                @if($provider->vehicle || $provider->plate_number)
                    <span class="chip">{{ trim($provider->vehicle . ' ' . $provider->plate_number) }}</span>
                @endif
                @if((float) $provider->rating > 0)
                    <span class="chip">&#9733; {{ number_format((float) $provider->rating, 1) }}</span>
                @endif
            </div>
        </div>
        <div class="hc"><a class="btn" href="{{ route('provider.requests') }}">See open requests</a></div>
    </div>

    @if($needsLocation)
        <div class="card" style="margin-bottom: 16px;">
            <h3>Set your service area</h3>
            <p class="m">You only see requests near you. Add where you are based under My business so they can appear.</p>
            <a href="{{ route('provider.profile') }}" class="btn" style="text-decoration: none;">Set my area</a>
        </div>
    @endif

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
                <li>Already paid to you: R{{ number_format($paidOutTotal * (1 - $commissionRate), 2) }}</li>
            </ul>
            <div class="m sm" style="margin-top:8px;">Payouts are made by MrRel.</div>
        </div>
    </div>
@endsection