@extends('layouts.admin')

@section('content')
    <h1>Disputes</h1>
    <div class="m">Handle disputes and reported problems between customers and providers.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Reported Problems</h3>
        @if($problems->isEmpty())
            <p class="m">No disputes reported yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Report ID</th>
                    <th>Customer</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($problems as $problem)
                <tr>
                    <td>#{{ str_pad($problem->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $problem->customer_name }}</td>
                    <td>{{ $problem->description }}</td>
                    <td>{{ $problem->created_at->format('d M Y') }}</td>
                    <td><span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">{{ $problem->status }}</span></td>
                    <td>
                        @if($problem->status !== 'Resolved')
                            <form method="POST" action="{{ route('admin.disputes.resolve', $problem->id) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px;">Resolve</button>
                            </form>
                        @else
                            <span class="m sm">Resolved</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection