@extends('layouts.admin')

@section('content')
    <h1>Provider Approvals</h1>
    <div class="m">Review and verify new service providers before they can quote.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>All Providers</h3>
        @if($providers->isEmpty())
            <p class="m">No providers registered yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Provider / Company</th>
                    <th>Vehicle</th>
                    <th>Plate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($providers as $provider)
                <tr>
                    <td>
                        <b>{{ $provider->name }}</b><br>
                        <span class="m sm">{{ $provider->company_name }}</span>
                    </td>
                    <td>{{ $provider->vehicle ?? 'Not provided' }}</td>
                    <td>{{ $provider->plate_number ?? 'Not provided' }}</td>
                    <td>
                        <span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">
                            {{ $provider->verification_status }}
                        </span>
                    </td>
                    <td>
                        @if($provider->verification_status !== 'Verified')
                            <form method="POST" action="{{ route('admin.providers.verify', $provider->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px;">Verify Provider</button>
                            </form>
                        @else
                            <span class="m sm">Verified</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection