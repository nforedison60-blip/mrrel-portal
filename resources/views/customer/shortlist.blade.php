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
                    @if($property->image_path)
                        <img src="{{ Storage::disk('s3')->url($property->image_path) }}" alt="Property Photo" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                    @else
                        <div class="ph">[No photo uploaded]</div>
                    @endif
                    
                    <h3>{{ $property->title }}</h3>
                    <div class="m sm">
                        R{{ number_format($property->rent, 0) }} a month · Deposit R{{ number_format($property->deposit, 0) }} · {{ $property->bedrooms }} bed, {{ $property->bathrooms }} bath
                    </div>
                    <p class="sm" style="margin-top: 8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
                    <p class="sm"><b>MrRel note:</b> {{ $property->description ?? 'No description provided.' }}</p>
                    
                    <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin-top: 12px;">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <div class="f" style="margin-bottom: 8px;">
                            <label class="k">Message to landlord (optional)
                                <input type="text" name="message" placeholder="e.g. Can we do Saturday morning?">
                            </label>
                        </div>
                        <button type="submit" class="btn" style="margin:0 8px 8px 0;">Request viewing</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Notes</h3>
        <p class="sm">Accepting a place does not reserve it, create a lease or guarantee availability. Rent and deposit are paid directly to the landlord. Viewing requests can be video or in person.</p>
    </div>
@endsection