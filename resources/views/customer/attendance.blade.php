@extends('layouts.app')

@section('content')
    <h1>Have MrRel attend your viewing</h1>
    <div class="m">Optional. Someone from MrRel views the place for you and reports back.</div>

    <div class="card" style="margin-top: 20px; max-width: 560px;">
        <h3>{{ $property->title }}</h3>
        <div class="m sm" style="margin-bottom: 12px;">{{ $property->suburb }}, {{ $property->city }}</div>
        <table class="tb">
            <tr><td>Estimated distance (one way)</td><td style="text-align:right;">{{ $estimate['one_way_km'] }} km</td></tr>
            <tr><td>Round trip, rounded up</td><td style="text-align:right;">{{ $estimate['round_trip_km'] }} km</td></tr>
            <tr><td>First {{ config('tenant.attendance.included_km') }} km round trip</td><td style="text-align:right;">R{{ number_format(config('tenant.attendance.base_fee'), 2) }}</td></tr>
            <tr><td>Extra {{ $estimate['extra_km'] }} km at R{{ number_format(config('tenant.attendance.per_km'), 2) }}</td><td style="text-align:right;">R{{ number_format($estimate['extra_km'] * config('tenant.attendance.per_km'), 2) }}</td></tr>
            <tr><td><b>Total</b></td><td style="text-align:right;"><b>R{{ number_format($estimate['fee'], 2) }}</b></td></tr>
        </table>
        <p class="m sm" style="margin-top: 10px;">This is an estimate based on the suburb, using the straight-line distance with an allowance for roads. It is not a measured driving distance.</p>

        <form method="POST" action="{{ route('tenant.attendance.pay', $viewing->id) }}" style="margin-top: 12px;">
            @csrf
            <button type="submit" class="btn">Pay R{{ number_format($estimate['fee'], 2) }}</button>
            <a href="{{ route('customer.shortlist') }}" class="btn g" style="text-decoration: none;">No thanks, I will attend myself</a>
        </form>
    </div>
@endsection
