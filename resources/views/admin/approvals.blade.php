@extends('layouts.admin')

@section('content')
    <h1>Approvals</h1>
    <div class="m">Review pending properties and providers.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Pending Properties</h3>
        
        @if(session('success'))
            <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
                {{ session('success') }}
            </div>
        @endif

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
@endsection