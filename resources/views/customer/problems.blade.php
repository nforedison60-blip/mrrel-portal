@extends('layouts.app')

@section('content')
    <h1>Report a Problem</h1>
    <div class="m">If something went wrong with your move, let us know and our team will step in.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-top:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Report a new problem</h3>
            <form method="POST" action="{{ route('customer.problems.store') }}">
                @csrf
                <div class="f">
                    <label class="k">Describe the issue
                        <textarea name="description" rows="4" placeholder="e.g. The mover was late / An item was damaged..." required></textarea>
                    </label>
                </div>
                <button type="submit" class="btn">Submit Report</button>
            </form>
        </div>

        <div class="card">
            <h3>Your previous reports</h3>
            @if($problems->isEmpty())
                <p class="m sm">You have no open problems.</p>
            @else
                @foreach($problems as $problem)
                    <div style="border-bottom: 1px solid var(--l); padding: 12px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <b class="sm">Report #{{ $problem->id }}</b>
                            <span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">{{ $problem->status }}</span>
                        </div>
                        <p class="sm m">{{ $problem->description }}</p>
                        <div class="m sm" style="font-size: 11px; margin-top: 8px;">Reported on {{ $problem->created_at->format('d M Y') }}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection