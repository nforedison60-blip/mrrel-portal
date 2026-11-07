@extends('layouts.provider')

@section('content')
    <h1>Provider Profile</h1>
    <div class="m">Manage your details and verification status.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-top:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="sp" style="margin-top: 20px;">
        <div class="card">
            <h3>Edit Details</h3>
            <form method="POST" action="{{ route('provider.profile.update') }}">
                @csrf
                <div class="f">
                    <label>Name<input type="text" name="name" value="{{ $provider->name }}" required></label>
                    <label>Company Name<input type="text" name="company_name" value="{{ $provider->company_name }}"></label>
                    <label>Vehicle<input type="text" name="vehicle" value="{{ $provider->vehicle }}"></label>
                    <label>Plate Number<input type="text" name="plate_number" value="{{ $provider->plate_number }}"></label>
                </div>
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>

        <div class="card">
            <h3>Verification Status</h3>
            <div style="margin-bottom: 12px;">
                <span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">
                    {{ $provider->verification_status }}
                </span>
            </div>
            <p class="sm">Your rating is currently <b>{{ $provider->rating }} stars</b>.</p>
            <p class="m sm">Once verified by an Admin, you will get a "Verified" badge on all your quotes.</p>
        </div>
    </div>
@endsection