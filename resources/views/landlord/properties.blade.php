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
        <a href="{{ route('landlord.add-property') }}" class="btn" style="text-decoration:none;">+ Add property</a>
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
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>{{ $property->title }}, {{ $property->suburb }}</td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td><span class="chip wn">{{ $property->status }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Viewing Requests</h3>
        @if($viewingRequests->isEmpty())
            <p class="m">No viewing requests yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Tenant Name</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($viewingRequests as $req)
                <tr>
                    <td>{{ $req->property->title }}</td>
                    <td>{{ $req->customer_name }}</td>
                    <td>{{ $req->message ?? '—' }}</td>
                    <td><span class="chip {{ $req->status == 'Completed' ? 'ok' : 'wn' }}">{{ $req->status }}</span></td>
                    <td>
                        @if($req->status !== 'Completed')
                            <form method="POST" action="{{ route('landlord.viewing.resolve', $req->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px; margin:0;">Mark as Resolved</button>
                            </form>
                        @else
                            <span class="m sm">Done</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection