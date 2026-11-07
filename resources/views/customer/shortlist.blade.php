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
                    <div class="ph">[Property photo]</div>
                    <h3>{{ $property->title }}</h3>
                    <div class="m sm">
                        R{{ number_format($property->rent, 0) }} a month · Deposit R{{ number_format($property->deposit, 0) }} · {{ $property->bedrooms }} bed, {{ $property->bathrooms }} bath
                    </div>
                    <p class="sm" style="margin-top: 8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
                    <p class="sm"><b>MrRel note:</b> {{ $property->description ?? 'No description provided.' }}</p>
                    
                    <div style="margin-top: 12px; display:flex; gap:8px; flex-wrap:wrap;">
                        <!-- FIXED: This is now a proper form that submits the request -->
                        <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            <button type="submit" class="btn" style="margin:0;">Request viewing</button>
                        </form>
                        
                        <button type="button" class="btn g" style="margin:0;">Accept</button>
                        <button type="button" class="btn g" style="margin:0;">Decline</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Notes</h3>
        <p class="sm">Accepting a place does not reserve it, create a lease or guarantee availability. Rent and deposit are paid directly to the landlord. Viewing requests can be video or in person.</p>
    </div>
@endsection