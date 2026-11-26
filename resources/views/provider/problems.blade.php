@extends('layouts.provider')

@section('content')
    <h1>Issues</h1>
    <div class="m">Manage issues reported by customers on your jobs.</div>

    @if(session('success'))
        <div style="background:#E6EEF8; color:#003068; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0A4C9A;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Reported issues</h3>
        @if($problems->isEmpty())
            <p class="m">No issues have been reported on your jobs.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Ticket</th>
                    <th>Customer</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($problems as $problem)
                <tr>
                    <td>D-{{ str_pad($problem->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $problem->customer_name }}</td>
                    <td>{{ $problem->description }}</td>
                    <td>
                        <span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">
                            {{ $problem->status == 'Resolved' ? 'Resolved' : 'In progress' }}
                        </span>
                    </td>
                    <td>
                        @if($problem->status !== 'Resolved')
                            <button type="button" class="btn g" style="padding: 6px 14px; font-size: 13px;">Add evidence</button>
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