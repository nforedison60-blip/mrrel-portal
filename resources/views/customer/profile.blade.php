@extends('layouts.app')

@section('content')
    <h1>Profile</h1>
    <div class="m">Your details. Providers and landlords see your name and phone number.</div>

    <div class="card" style="margin-top: 20px; max-width: 640px;">
        <h3>My details</h3>
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            <div class="f">
                <label>Full name
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" maxlength="100" required>
                </label>
                <label>Phone or WhatsApp
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="30" placeholder="e.g. 082 123 4567">
                </label>
                <label class="k">Email (the one you log in with)
                    <input type="email" value="{{ auth()->user()->email }}" disabled>
                </label>
            </div>
            <div style="display: flex; gap: 8px; margin-top: 16px; flex-wrap: wrap;">
                <button type="submit" class="btn" style="margin:0;">Save</button>
                <a href="{{ route('profile.edit') }}" class="btn g" style="margin:0; text-decoration:none;">Change password</a>
            </div>
        </form>
    </div>
@endsection
