@extends('layouts.landlord')

@section('content')
    <h1>Viewing requests</h1>
    <div class="m">Tenant details are shared only after a tenant asks for a viewing.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Viewing requests</h3>
        @if($viewingRequests->isEmpty())
            <p class="m">No viewing requests yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Tenant</th>
                    <th>Property</th>
                    <th>Preferred times</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($viewingRequests as $req)
                <tr>
                    <td>{{ $req->customer_name }}</td>
                    <td>{{ $req->property->title ?? 'Unknown Property' }}</td>
                    <td>Sat morning</td> <!-- Placeholder for now -->
                    <td>In person</td> <!-- Placeholder for now -->
                    <td><span class="chip {{ $req->status == 'Completed' ? 'ok' : 'wn' }}">{{ $req->status == 'Completed' ? 'Resolved' : 'Waiting' }}</span></td>
                    <td>
                        @if($req->status !== 'Completed')
                            <form method="POST" action="{{ route('landlord.viewing.resolve', $req->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px; margin:0;">Approve</button>
                            </form>
                        @else
                            <span class="m sm">Done</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            <p class="m sm" style="margin-top: 16px;">MrRel arranges viewings with you.</p>
        @endif
    </div>
@endsection