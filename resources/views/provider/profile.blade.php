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
        <div>
            <div class="card">
                <h3>Edit Details</h3>
                <form method="POST" action="{{ route('provider.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row" style="margin-bottom: 16px; align-items: center;">
                        <!-- ROUND CROP AS PER DESIGN -->
                        @if($provider->image_path)
                            <img src="{{ asset('storage/' . $provider->image_path) }}" alt="Profile Photo of {{ $provider->name }}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        @else
                            <div class="av" style="width: 150px; height: 150px; font-size: 48px;">{{ substr($provider->name, 0, 1) }}</div>
                        @endif
                        <div style="margin-left: 20px;">
                            <b>Profile Photo</b>
                            <div class="m sm">Upload a clear, recent photo of the person who will attend.<br>Round crop will be applied automatically.</div>
                        </div>
                    </div>

                    <div class="f">
                        <label class="k">Profile Photo (Max 5MB)
                            <input type="file" name="image" accept="image/*">
                        </label>
                        <label>Name<input type="text" name="name" value="{{ $provider->name }}" required></label>
                        <label>Company Name<input type="text" name="company_name" value="{{ $provider->company_name }}"></label>
                        <label>Vehicle<input type="text" name="vehicle" value="{{ $provider->vehicle }}"></label>
                        <label>Plate Number<input type="text" name="plate_number" value="{{ $provider->plate_number }}"></label>
                    </div>

                    <h3 style="margin-top: 20px; border-top: 1px solid var(--l); padding-top: 16px;">Verification Documents</h3>
                    <div class="f">
                        <label class="k">Vehicle Photo (Max 5MB)
                            <input type="file" name="vehicle_image" accept="image/*">
                            @if($provider->vehicle_image_path)
                                <span class="m sm" style="color: green;">✓ Uploaded</span>
                            @endif
                        </label>
                        <label class="k">ID / Document (Max 5MB)
                            <input type="file" name="id_image" accept="image/*">
                            @if($provider->id_image_path)
                                <span class="m sm" style="color: green;">✓ Uploaded</span>
                            @endif
                        </label>
                    </div>

                    <button type="submit" class="btn" style="margin-top: 10px;">Save Changes</button>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h3>Recent Reviews</h3>
                @if($reviews->isEmpty())
                    <p class="m">No reviews yet.</p>
                @else
                    @foreach($reviews as $review)
                        <div style="border-bottom: 1px solid var(--l); padding: 12px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <b>{{ $review->customer_name }}</b>
                                <span style="color: var(--a); font-weight: bold;">{{ $review->rating }} ★</span>
                            </div>
                            <p class="sm m" style="margin-top: 4px;">"{{ $review->comment }}"</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card">
            <h3>Verification Status</h3>
            <div style="margin-bottom: 12px;">
                <span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">
                    {{ $provider->verification_status }}
                </span>
            </div>
            <p class="sm">Your rating is currently <b>{{ $provider->rating }} stars</b>.</p>
            <p class="m sm">Your profile photo and vehicle photo are visible to customers once you are verified.</p>
        </div>
    </div>
@endsection