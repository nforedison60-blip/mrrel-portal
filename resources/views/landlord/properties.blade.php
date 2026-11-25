@extends('layouts.landlord')

@section('content')
@php
    // Properties only ever have these three states. Draft means "sent to MrRel, waiting for review".
    $labels = [
        'Draft'       => ['Waiting for MrRel', 'wn'],
        'Approved'    => ['Live', 'ok'],
        'Under Offer' => ['Tenant accepted', 'ok'],
    ];
@endphp

    <div class="row" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <h1>My properties</h1>
            <div class="m">Properties stay private. Only MrRel decides which tenants see them.</div>
        </div>
        <a href="{{ route('landlord.add-property') }}" class="btn" style="text-decoration:none;">+ Add property</a>
    </div>

    <div class="g3">
        <div class="card"><div class="m sm">Live</div><div class="big">{{ $properties->where('status', 'Approved')->count() }}</div><div class="m sm">Visible to tenants</div></div>
        <div class="card"><div class="m sm">Waiting for MrRel</div><div class="big">{{ $properties->where('status', 'Draft')->count() }}</div><div class="m sm">Being reviewed</div></div>
        <div class="card"><div class="m sm">Tenant accepted</div><div class="big">{{ $properties->where('status', 'Under Offer')->count() }}</div><div class="m sm">MrRel will finalise the lease</div></div>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>Your properties</h3>
        @if($properties->isEmpty())
            <div class="empty">
                <h3>No properties yet</h3>
                <p class="m" style="margin-bottom: 12px;">Add a property and MrRel will review it. Once it is approved, matching tenants can ask to view it.</p>
                <a href="{{ route('landlord.add-property') }}" class="btn" style="text-decoration:none;">Add your first property</a>
            </div>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Action</th>
                </tr>
                @foreach($properties as $property)
                    @php $label = $labels[$property->status] ?? [$property->status, 'wn']; @endphp
                    <tr>
                        <td>
                            <b>{{ $property->title }}</b><br>
                            <span class="m sm">{{ collect([$property->suburb, $property->city])->filter()->implode(', ') }} · {{ $property->property_type }}</span>
                        </td>
                        <td>R{{ number_format($property->rent, 0) }}</td>
                        <td><span class="chip {{ $label[1] }}">{{ $label[0] }}</span></td>
                        <td class="m sm">{{ $property->updated_at->format('d M Y') }}</td>
                        <td>
                            @if($property->status === 'Under Offer')
                                <span class="m sm">Locked</span>
                            @else
                                <a href="{{ route('landlord.properties.edit', $property) }}" class="btn g" style="padding:6px 14px;font-size:13px;margin:0;text-decoration:none;">Edit</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
