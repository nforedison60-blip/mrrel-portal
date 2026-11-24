@extends('layouts.provider')

@section('content')
    <h1>Available Requests</h1>
    <div class="m" style="margin-bottom: 20px;">Open moves that start within {{ $radius }} km of {{ $areaLabel ?: 'your base' }}.</div>

    @if($needsLocation)
        <div class="card" style="margin-bottom: 16px;">
            <h3>Set your service area</h3>
            <p class="m">You only see requests near you. Add where you are based so they can appear.</p>
            <a href="{{ route('provider.profile') }}" class="btn" style="text-decoration: none;">Set my area</a>
        </div>
    @endif

    <div class="card">
        <table class="tb">
            <tr>
                <th>Date</th>
                <th>Services</th>
                <th>From</th>
                <th>To</th>
                <th>Distance</th>
                <th>Action</th>
            </tr>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->created_at->format('d M Y') }}</td>
                    <td><b>{{ $request->services_needed }}</b></td>
                    <td>{{ $request->moving_from }}</td>
                    <td>{{ $request->moving_to }}</td>
                    <td>{{ round($request->distance_km) }} km</td>
                    <td>
                        <a href="{{ route('provider.send', $request->id) }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration: none;">
                            Send Quote
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="m sm" style="text-align: center; padding: 20px;">
                        No requests near you right now.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection