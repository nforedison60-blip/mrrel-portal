@extends('layouts.admin')

@section('content')
@php
    $pendingProviders = \App\Models\Provider::where('verification_status', 'Pending')->latest()->get();
    $yes = '<span class="chip ok">Yes</span>';
    $missing = '<span class="chip wn">Missing</span>';
@endphp

    <h1>Approvals</h1>
    <div class="m">Providers and properties wait here until you verify or approve them.</div>

    <div class="row" style="margin:16px 0;">
        <span class="chip {{ $pendingProviders->count() ? 'wn' : '' }}">Providers ({{ $pendingProviders->count() }})</span>
        <span class="chip {{ $properties->count() ? 'wn' : '' }}">Properties ({{ $properties->count() }})</span>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Provider applications</h3>
        @if($pendingProviders->isEmpty())
            <p class="m">No provider applications are waiting. New applications appear here after a provider registers.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Provider</th>
                    <th>Photo</th>
                    <th>ID</th>
                    <th>Vehicle and plate</th>
                    <th>Documents</th>
                    <th>Action</th>
                </tr>
                @foreach($pendingProviders as $p)
                    <tr>
                        <td>
                            <b>{{ $p->name }}</b>
                            @if($p->company_name)<div class="m sm">{{ $p->company_name }}</div>@endif
                        </td>
                        <td>{!! $p->image_path ? $yes : $missing !!}</td>
                        <td>{!! ($p->id_image_path || $p->id_document) ? $yes : $missing !!}</td>
                        <td>
                            @if($p->vehicle || $p->plate_number)
                                {{ $p->vehicle }}@if($p->vehicle && $p->plate_number) · @endif{{ $p->plate_number }}
                            @else
                                {!! $missing !!}
                            @endif
                        </td>
                        <td>{!! $p->vehicle_document ? $yes : $missing !!}</td>
                        <td>
                            <a href="{{ route('admin.providers') }}" class="btn g" style="padding:6px 14px;font-size:13px;margin:0 6px 6px 0;text-decoration:none;">Review documents</a>
                            <form method="POST" action="{{ route('admin.providers.verify', $p->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn" style="padding:6px 14px;font-size:13px;margin:0 0 6px;">Verify</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Properties waiting for approval</h3>
        @if($properties->isEmpty())
            <p class="m">No properties are waiting. New listings from landlords appear here for approval.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Place</th>
                    <th>Type</th>
                    <th>Rent</th>
                    <th>Action</th>
                </tr>
                @foreach($properties as $property)
                    <tr>
                        <td><b>{{ $property->title }}</b></td>
                        <td>{{ collect([$property->suburb, $property->city])->filter()->implode(', ') }}</td>
                        <td>{{ $property->property_type }}</td>
                        <td>R{{ number_format($property->rent, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.approvals.approve', $property->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding:6px 14px;font-size:13px;margin:0;">Approve</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection