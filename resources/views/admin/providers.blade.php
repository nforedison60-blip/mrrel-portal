@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <h1>Provider Approvals</h1>
    <div class="m">Review and verify new service providers before they can quote.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>All Providers</h3>
        @if($providers->isEmpty())
            <p class="m">No providers registered yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Profile</th>
                    <th>Provider / Company</th>
                    <th>Vehicle Details</th>
                    <th>Documents</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($providers as $provider)
                <tr>
                    <td>
                        <!-- SAFE IMAGE CHECK -->
                        @if($provider->image_path && Storage::disk('public')->exists($provider->image_path))
                            <img src="{{ asset('storage/' . $provider->image_path) }}" alt="Profile Photo of {{ $provider->name }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div class="av" style="width: 72px; height: 72px; font-size: 24px;">{{ substr($provider->name, 0, 1) }}</div>
                        @endif
                    </td>
                    <td>
                        <b>{{ $provider->name }}</b><br>
                        <span class="m sm">{{ $provider->company_name }}</span>
                    </td>
                    <td>
                        {{ $provider->vehicle ?? 'Not provided' }}<br>
                        <span class="m sm">{{ $provider->plate_number ?? 'Not provided' }}</span>
                        
                        @if($provider->vehicle_image_path && Storage::disk('public')->exists($provider->vehicle_image_path))
                            <div style="margin-top: 8px;">
                                <a href="{{ asset('storage/' . $provider->vehicle_image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $provider->vehicle_image_path) }}" alt="Vehicle Photo" style="width: 150px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid var(--l);">
                                </a>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($provider->id_image_path && Storage::disk('public')->exists($provider->id_image_path))
                            <a href="{{ asset('storage/' . $provider->id_image_path) }}" target="_blank" style="color: var(--t); text-decoration: underline;">View ID Document</a>
                        @else
                            <span class="m sm">No ID uploaded</span>
                        @endif
                    </td>
                    <td>
                        <span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">
                            {{ $provider->verification_status }}
                        </span>
                    </td>
                    <td>
                        @if($provider->verification_status !== 'Verified')
                            <form method="POST" action="{{ route('admin.providers.verify', $provider->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px;">Verify Provider</button>
                            </form>
                        @else
                            <span class="m sm">Verified</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection