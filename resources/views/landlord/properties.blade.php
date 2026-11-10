@extends('layouts.landlord')

@section('content')
    <h1>My properties</h1>
    <div class="m">Properties stay private. Only MrRel decides which tenants see them.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Approved</div><div class="big">{{ $properties->where('status', 'Approved')->count() }}</div><div class="m sm">Visible to tenants</div></div>
        <div class="card"><div class="m sm">Submitted</div><div class="big">{{ $properties->where('status', 'Submitted')->count() }}</div><div class="m sm">Waiting for MrRel</div></div>
        <div class="card"><div class="m sm">Changes requested</div><div class="big">{{ $properties->where('status', 'Changes requested')->count() }}</div><div class="m sm">Needs your attention</div></div>
        <div class="card"><div class="m sm">Draft</div><div class="big">{{ $properties->where('status', 'Draft')->count() }}</div><div class="m sm">Not submitted yet</div></div>
    </div>

    <div style="margin-bottom: 16px;">
        <a href="{{ route('landlord.add-property') }}" class="btn" style="text-decoration:none;">Add property</a>
    </div>

    <div class="card">
        <h3>Your properties</h3>
        @if($properties->isEmpty())
            <p class="m">You haven't added any properties yet. Click the button above to get started.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th>Updated</th>
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; background: #E3F1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #D5DEE6;">
                                <svg width="24" height="24" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/>
                                    <rect x="40" y="60" width="20" height="30" fill="#51657A"/>
                                    <rect x="20" y="55" width="15" height="15" fill="#51657A"/>
                                    <rect x="65" y="55" width="15" height="15" fill="#51657A"/>
                                </svg>
                            </div>
                            <div>
                                <b>{{ $property->title }}</b><br>
                                <span class="m sm">{{ $property->suburb }}</span>
                            </div>
                        </div>
                    </td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td><span class="chip {{ $property->status == 'Approved' ? 'ok' : 'wn' }}">{{ $property->status }}</span></td>
                    <td>{{ $property->updated_at->format('d M') }}</td>
                </tr>
                @endforeach
            </table>
            <p class="m sm" style="margin-top: 16px;">Statuses: Draft, Submitted, Approved, Changes requested, Rejected, Withdrawn.</p>
        @endif
    </div>
@endsection