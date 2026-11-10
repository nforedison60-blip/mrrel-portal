@extends('layouts.app')

@section('content')
    <h1>Find me a place</h1>
    <div class="m">Search for available properties across South Africa.</div>

    <div class="card" style="margin-top: 20px;">
        <form method="GET" action="{{ route('customer.search') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="sm" style="font-weight: 600;">Suburb or City</label>
                <input type="text" name="location" placeholder="e.g. Sea Point" value="{{ request('location') }}" style="margin-top: 4px;">
            </div>
            <div style="width: 200px;">
                <label class="sm" style="font-weight: 600;">Max Rent (R)</label>
                <input type="number" name="max_price" placeholder="e.g. 15000" value="{{ request('max_price') }}" style="margin-top: 4px;">
            </div>
            <button type="submit" class="btn" style="margin-bottom: 0;">Search</button>
        </form>
    </div>

    @if($properties->isEmpty())
        <div class="card" style="margin-top: 20px;">
            <p class="m">No properties found matching your search. Try adjusting your filters.</p>
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
                        R{{ number_format($property->rent, 0) }} a month · {{ $property->bedrooms }} bed, {{ $property->bathrooms }} bath
                    </div>
                    <p class="sm" style="margin-top: 8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
                    
                    <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin-top: 12px;">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <button type="submit" class="btn" style="width: 100%;">Request viewing</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection