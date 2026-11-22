@extends('layouts.app')

@section('content')
    <h1>Find me a place</h1>
    <div class="m">Search for available properties to rent.</div>

    <div class="card" style="margin-top: 20px;">
        <form method="GET" action="{{ route('customer.search') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label class="sm" style="font-weight: 600;">Suburb or city</label>
                <input type="text" name="location" placeholder="e.g. Sea Point" value="{{ request('location') }}" style="margin-top: 4px;">
            </div>
            <div style="width: 160px;">
                <label class="sm" style="font-weight: 600;">Max rent (R)</label>
                <input type="number" name="max_price" min="0" placeholder="e.g. 15000" value="{{ request('max_price') }}" style="margin-top: 4px;">
            </div>
            <div style="width: 140px;">
                <label class="sm" style="font-weight: 600;">Bedrooms</label>
                <select name="bedrooms" style="margin-top: 4px;">
                    <option value="">Any</option>
                    @foreach([1, 2, 3, 4] as $n)
                        <option value="{{ $n }}" @selected((string) request('bedrooms') === (string) $n)>{{ $n }}+</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn" style="margin-bottom: 0;">Search</button>
            @if(request()->hasAny(['location', 'max_price', 'bedrooms']))
                <a href="{{ route('customer.search') }}" class="btn g" style="margin-bottom: 0; text-decoration:none;">Clear</a>
            @endif
        </form>
    </div>

    @if($properties->isEmpty())
        <div class="card empty" style="margin-top: 20px;">
            <h3>No places found</h3>
            <p class="m">Try a different suburb, a higher rent or fewer bedrooms.</p>
        </div>
    @else
        <div class="m sm" style="margin-top: 16px;">{{ $properties->count() }} {{ \Illuminate\Support\Str::plural('place', $properties->count()) }} available</div>
        <div class="g3" style="margin-top: 8px;">
            @foreach($properties as $property)
                @include('customer._property', ['property' => $property, 'viewings' => $viewings])
            @endforeach
        </div>
    @endif
@endsection
