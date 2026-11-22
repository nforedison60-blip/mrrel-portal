@extends('layouts.app')

@section('content')
    <h1>Rental fees</h1>
    <div class="m">What it costs to move into the places you accepted.</div>

    @if($properties->isEmpty())
        <div class="card empty" style="margin-top: 20px;">
            <h3>No accepted places yet</h3>
            <p class="m" style="margin-bottom: 12px;">When you accept a place, the rent and deposit show here.</p>
            <a href="{{ route('customer.search') }}" class="btn">Find a place</a>
        </div>
    @else
        <div class="g2" style="margin-top: 20px;">
            @foreach($properties as $property)
                <div class="card">
                    <h3 style="margin-bottom: 2px;">{{ $property->title }}</h3>
                    <div class="m sm" style="margin-bottom: 12px;">{{ $property->suburb }}, {{ $property->city }}</div>
                    <table class="tb">
                        <tr><td>Monthly rent</td><td style="text-align:right;">R{{ number_format($property->rent, 2) }}</td></tr>
                        <tr><td>Deposit</td><td style="text-align:right;">R{{ number_format($property->deposit, 2) }}</td></tr>
                        <tr><td><b>To move in</b> <span class="m sm">(first month + deposit)</span></td><td style="text-align:right;"><b>R{{ number_format($property->rent + $property->deposit, 2) }}</b></td></tr>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>How you pay</h3>
            <p class="sm">Rent and deposit are paid directly to the landlord. MrRel will tell you how and when once the lease is finalised. Nothing is charged here.</p>
        </div>
    @endif
@endsection
