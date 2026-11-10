@extends('layouts.app')

@section('content')
    <h1>Profile</h1>
    <div class="m">Pages not shown elsewhere.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>My details</h3>
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            <div class="f">
                <label>Full name
                    <input type="text" name="name" value="{{ $customer->name }}" required>
                </label>
                <label>Phone or WhatsApp
                    <input type="text" name="phone" value="{{ $customer->phone }}">
                </label>
                <label class="k">Email
                    <input type="email" name="email" value="{{ $customer->email }}">
                </label>
                <label class="k" style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                    <input type="checkbox" style="width: auto; margin: 0;"> Marketing messages
                </label>
            </div>
            
            <div style="display: flex; gap: 8px; margin-top: 16px;">
                <button type="submit" class="btn">Save</button>
                <button type="button" class="btn g">Download my data</button>
                <button type="button" class="btn g">Delete my account</button>
            </div>
            <p class="m sm" style="margin-top: 12px;">Consent to the Terms and Privacy Policy is recorded.</p>
        </form>
    </div>
@endsection