@extends('layouts.app')

@section('content')
    <h1>My Profile</h1>
    <div class="m">Manage your personal details.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-top:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px; max-width: 600px;">
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            <div class="f">
                <label class="k">Full Name
                    <input type="text" name="name" value="{{ $customer->name }}" required>
                </label>
                <label class="k">Email Address
                    <input type="email" name="email" value="{{ $customer->email }}">
                </label>
                <label class="k">Phone Number
                    <input type="text" name="phone" value="{{ $customer->phone }}">
                </label>
            </div>
            <button type="submit" class="btn">Save Details</button>
        </form>
    </div>
@endsection