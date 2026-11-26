@extends('layouts.app')

@section('content')
    <h1>My matches</h1>
    <div class="m">Homes MrRel found for what you need.</div>

    @if($matches->isEmpty())
        <div class="card empty" style="margin-top: 20px;">
            <h3>No matches yet</h3>
            @if($need)
                <p class="m" style="margin-bottom: 12px;">MrRel is looking for homes that fit your request. You will see them here.</p>
                <a href="{{ route('customer.needs.edit') }}" class="btn g" style="text-decoration: none;">See or change my request</a>
            @else
                <p class="m" style="margin-bottom: 12px;">Tell us what you need and MrRel will find homes for you.</p>
                <a href="{{ route('customer.needs.create') }}" class="btn" style="text-decoration: none;">Find a home</a>
            @endif
        </div>
    @else
        @if(!$paid)
            <div class="card" style="margin-top: 20px;">
                <h3>Pay the once-off R{{ number_format($fee, 0) }} matching fee to see the full details</h3>
                <p class="m" style="margin-bottom: 12px;">One payment unlocks the photos, deposit and description of every home MrRel matches to you, and lets you ask for viewings.</p>
                <form method="POST" action="{{ route('tenant.pay.matching') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn">Pay R{{ number_format($fee, 0) }}</button>
                </form>
            </div>
        @endif

        <div class="g3" style="margin-top: 20px;">
            @foreach($matches as $match)
                @include('customer._property_tenant', ['property' => $match->property, 'viewings' => $viewings])
            @endforeach
        </div>
    @endif

    @include('customer._tenant_steps')
@endsection
