@extends('layouts.app')

@section('content')
    <h1>My shortlist</h1>
    <div class="m">Places you asked to view or accepted.</div>

    @if($properties->isEmpty())
        <div class="card empty" style="margin-top: 20px;">
            <h3>Your shortlist is empty</h3>
            <p class="m" style="margin-bottom: 12px;">When you request a viewing or accept a place, it shows up here.</p>
            <a href="{{ route('customer.search') }}" class="btn">Find a place</a>
        </div>
    @else
        <div class="g3" style="margin-top: 20px;">
            @foreach($properties as $property)
                @include('customer._property_tenant', ['property' => $property, 'viewings' => $viewings])
            @endforeach
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Good to know</h3>
        <p class="sm">Accepting a place tells MrRel you want it. You then finalise the lease directly with the landlord. Rent and deposit are paid directly to the landlord. Viewings can be video or in person.</p>
    </div>
@endsection
