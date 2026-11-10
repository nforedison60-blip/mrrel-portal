@extends('layouts.provider')

@section('content')
    <h1>Problems</h1>
    <div class="m">Manage issues reported by customers on your jobs.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-top: 20px;">
        <h3>Reported Problems</h3>
        @if($problems->isEmpty())
            <p class="m">No problems have been reported on your jobs.</p>
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