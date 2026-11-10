@extends('layouts.app')

@section('content')
    <h1>My shortlist</h1>
    <div class="m">Cape Town · up to R15,000 · 2 bedrooms · move-in 1 December. Only places MrRel shares with you.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-top:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    @if($properties->isEmpty())
        <div class="card" style="margin-top: 20px;">
            <p class="m">There are no properties on your shortlist yet. Check back soon!</p>
        </div>
    @else
        <div class="g3" style="margin-top: 20px;">
            @foreach($properties as $property)
                <div class="card">
                    <!-- House SVG / Real Image -->
                    @if($property->image_path)
                        <img src="{{ Storage::url($property->image_path) }}" alt="Property Photo" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                    @else
                        <div style="width: 100%; height: 200px; background-color: #E3F1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/>
                                <rect x="40" y="60" width="20" height="30" fill="#51657A"/>
                                <rect x="20" y="55" width="15" height="15" fill="#51657A"/>
                                <rect x="65" y="55" width="15" height="15" fill="#51657A"/>
                            </svg>
                        </div>
                    @endif

                    <!-- Thumbnails -->
                    <div class="row" style="gap: 4px; margin-bottom: 12px;">
                        <div style="width: 30px; height: 20px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 2px; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#51657A"/></svg>
                        </div>
                        <div style="width: 30px; height: 20px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 2px; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#51657A"/></svg>
                        </div>
                         <div style="width: 30px; height: 20px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 2px; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#51657A"/></svg>
                        </div>
                         <div style="width: 30px; height: 20px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 2px; display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#51657A"/></svg>
                        </div>
                        <div class="m sm" style="margin-left: 8px;">1 of 8 photos</div>
                    </div>

                    <h3>{{ $property->title }}</h3>
                    <div class="m sm">
                        R{{ number_format($property->rent, 0) }} a month · Deposit R{{ number_format($property->deposit, 0) }} · Available 1 Dec · Parking, fibre
                    </div>
                    <p class="sm" style="margin-top: 8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
                    <p class="sm"><b>MrRel note:</b> {{ $property->description ?? 'No description provided.' }}</p>
                    
                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <button type="button" class="btn" style="margin:0;">Accept</button>
                        <button type="button" class="btn g" style="margin:0;">Decline</button>
                    </div>
                    
                    <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin-top: 8px;">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <div class="f" style="margin-bottom: 8px;">
                            <label class="k">Message to landlord (optional)
                                <input type="text" name="message" placeholder="e.g. Can we do Saturday morning?">
                            </label>
                        </div>
                        <button type="submit" class="btn g" style="width: 100%; margin:0;">Request viewing</button>
                    </form>

                    <a href="{{ route('customer.new-request') }}" class="btn g" style="width: 100%; display: block; text-align: center; text-decoration: none; margin-top: 8px;">Plan move for this property</a>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Notes</h3>
        <p class="sm">Accepting a place does not reserve it, create a lease or guarantee availability. Rent and deposit are paid directly to the landlord. Viewing requests can be video or in person.</p>
    </div>
@endsection