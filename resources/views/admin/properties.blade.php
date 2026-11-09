@extends('layouts.admin')

@section('content')
    <h1>All Properties</h1>
    <div class="m">A complete list of all properties on the MrRel platform.</div>

    <div class="card" style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="margin: 0;">Property Database</h3>
        </div>

        @if($properties->isEmpty())
            <p class="m">No properties exist in the database yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Location</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>
                        <b>{{ $property->title }}</b><br>
                        <span class="m sm">{{ $property->bedrooms }} bed · {{ $property->bathrooms }} bath</span>
                    </td>
                    <td>{{ $property->suburb }}, {{ $property->city }}</td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td>
                        <span class="chip {{ $property->status == 'Approved' ? 'ok' : 'wn' }}">
                            {{ $property->status }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn g" style="padding: 6px 14px; font-size: 13px;">View Details</button>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection