@extends('layouts.admin')

@section('content')
    <h1>Approvals & Requests</h1>
    <div class="m">Review pending properties and monitor tenant viewing requests.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <!-- PROPERTIES SECTION -->
    <div class="card" style="margin-top: 20px;">
        <h3>Pending Properties</h3>
        
        @if($properties->isEmpty())
            <p class="m">No pending properties to approve.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Landlord Rent</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>
                        <b>{{ $property->title }}</b><br>
                        <span class="m sm">{{ $property->suburb }}, {{ $property->city }}</span>
                    </td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td><span class="chip wn">{{ $property->status }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.approvals.approve', $property->id) }}">
                            @csrf
                            <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px;">Approve</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <!-- VIEWING REQUESTS SECTION -->
    <div class="card" style="margin-top: 20px;">
        <h3>All Viewing Requests</h3>
        
        @if($viewingRequests->isEmpty())
            <p class="m">No viewing requests have been made yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Tenant Name</th>
                    <th>Status</th>
                </tr>
                @foreach($viewingRequests as $req)
                <tr>
                    <td>{{ $req->property->title }}</td>
                    <td>{{ $req->customer_name }}</td>
                    <td><span class="chip {{ $req->status == 'Completed' ? 'ok' : 'wn' }}">{{ $req->status }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection