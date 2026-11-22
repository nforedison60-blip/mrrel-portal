@extends('layouts.landlord')

@section('content')
    <h1>Profile</h1>
    <div class="m">Your details, so MrRel can reach you about viewings and leases.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Your details</h3>
        <form method="POST" action="{{ route('landlord.profile.update') }}">
            @csrf
            <div class="f">
                <label>Full name
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required>
                </label>
                <label>Email (your login)
                    <input type="email" value="{{ $user->email }}" disabled>
                </label>
                <label>Phone or WhatsApp
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30" placeholder="e.g. 082 000 0000">
                </label>
                <label>You are the
                    <select name="landlord_type">
                        @foreach(['Owner', 'Agent'] as $option)
                            <option value="{{ $option }}" @selected(old('landlord_type', $user->landlord_type ?: 'Owner') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Agency or company (if you are an agent)
                    <input type="text" name="agency_name" value="{{ old('agency_name', $user->agency_name) }}" maxlength="150">
                </label>
                <label>PPRA registration number (agents)
                    <input type="text" name="ppra_number" value="{{ old('ppra_number', $user->ppra_number) }}" maxlength="50">
                </label>
            </div>
            <button type="submit" class="btn">Save profile</button>
        </form>
    </div>
@endsection
