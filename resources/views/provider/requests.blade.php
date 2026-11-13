@extends('layouts.provider')

@section('content')
    <h1>Available Requests</h1>
    <div class="m" style="margin-bottom: 20px;">Browse open moving requests and send your quotes.</div>

    <div class="card">
        <table class="tb">
            <tr>
                <th>Date</th>
                <th>Services</th>
                <th>From</th>
                <th>To</th>
                <th>Action</th>
            </tr>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->created_at->format('d M Y') }}</td>
                    <td><b>{{ $request->services_needed }}</b></td>
                    <td>{{ $request->moving_from }}</td>
                    <td>{{ $request->moving_to }}</td>
                    <td>
                        <a href="{{ route('provider.send', $request->id) }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration: none;">
                            Send Quote
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="m sm" style="text-align: center; padding: 20px;">
                        No active requests available right now.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection