@extends('layouts.app')

@section('content')
    <h1>Find a home</h1>
    <div class="m">Tell us what you need and MrRel will look for homes that fit.</div>

    @include('customer._tenant_steps')

    <div class="card" style="margin-top: 16px;">
        <h3>{{ $need ? 'Your request' : 'Step 1: tell us what you need' }}</h3>
        @if($need)
            <div class="chip wn" style="margin-bottom: 8px;">Open: MrRel is looking for a match</div>
            <div class="m sm" style="margin-bottom: 8px;">You can change this until MrRel sends you a match. <a href="{{ route('customer.matches') }}" style="color: var(--t);">See my matches</a></div>
        @endif

        <form method="POST" action="{{ $need ? route('customer.needs.update') : route('customer.needs.store') }}">
            @csrf
            @if($need) @method('PUT') @endif
            <div class="f">
                <label class="k">Preferred areas (suburbs or cities, separated by commas)
                    <input type="text" name="areas" required maxlength="255" value="{{ old('areas', optional($need)->areas) }}" placeholder="e.g. Claremont, Rondebosch, Newlands">
                </label>
                <label>Lowest budget per month (R, optional)
                    <input type="number" name="budget_min" min="0" value="{{ old('budget_min', optional($need)->budget_min) }}">
                </label>
                <label>Highest budget per month (R)
                    <input type="number" name="budget_max" min="1" required value="{{ old('budget_max', optional($need)->budget_max) }}">
                </label>
                <label>Bedrooms (at least)
                    <select name="bedrooms" required>
                        <option value="">Choose</option>
                        @foreach(['Studio', '1', '2', '3', '4', '5+'] as $b)
                            <option value="{{ $b }}" {{ (string) old('bedrooms', optional($need)->bedrooms) === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Move-in date (optional)
                    <input type="date" name="move_in_date" min="{{ now()->toDateString() }}" value="{{ old('move_in_date', optional(optional($need)->move_in_date)->format('Y-m-d')) }}">
                </label>
                <label class="k">Other requirements (optional)
                    <textarea name="notes" rows="3" maxlength="2000" placeholder="Pets, parking, safety, close to public transport...">{{ old('notes', optional($need)->notes) }}</textarea>
                </label>
            </div>
            @if($errors->any())
                <div class="m sm" style="color: #8A1C1C; margin-top: 8px;">
                    @foreach($errors->all() as $message)<div>{{ $message }}</div>@endforeach
                </div>
            @endif
            <button type="submit" class="btn" style="margin-top: 12px;">{{ $need ? 'Save changes' : 'Send my request' }}</button>
        </form>
    </div>
@endsection
