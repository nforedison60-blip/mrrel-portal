@extends('layouts.app')

@section('content')
    <h1>Welcome back, Lerato</h1>
    <div class="m">Everything that needs you, in one place.</div>
    
    <div class="g4">
        <div class="card"><div class="m sm">Active requests</div><div class="big">{{ $requests->count() }}</div><div class="m sm">Saved in database</div></div>
        <div class="card"><div class="m sm">Quotes to review</div><div class="big">3</div><div class="m sm">Move request REQ-0042</div></div>
        <div class="card"><div class="m sm">Next booking</div><div class="big">Sat 31 Oct</div><div class="m sm">Provider arrives 08:00 to 10:00</div></div>
        <div class="card"><div class="m sm">Shortlist</div><div class="big">3 places</div><div class="m sm">Shared by MrRel</div></div>
    </div>

    <div class="g2">
        <div class="card">
            <h3>Needs your attention</h3>
            <ul>
                <li>Compare 3 quotes for your 1-bed move</li>
                <li>Confirm the job on booking BKG-0101</li>
                <li>Approve an extra charge of R150 (stairs)</li>
                <li>Pay the rental service fee</li>
            </ul>
        </div>
        <div class="card">
            <h3>Start something new</h3>
            <p class="m">Choose what you need.</p>
            <a href="{{ route('customer.new-request') }}" class="btn" style="text-decoration:none;">New move request</a>
            <button type="button" class="btn g">Find me a place</button>
            <button type="button" class="btn g">View my shortlist</button>
        </div>
    </div>

    <!-- THIS IS THE NEW SECTION SHOWING THE SAVED DATA -->
    <div class="card" style="margin-top:16px;">
        <h3>Your recent requests (Live from Database)</h3>
        @if($requests->isEmpty())
            <p class="m">You haven't submitted any requests yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Service</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                </tr>
                @foreach($requests as $req)
                <tr>
                    <td>{{ $req->services_needed }}</td>
                    <td>{{ $req->moving_from }}</td>
                    <td>{{ $req->moving_to ?? '—' }}</td>
                    <td>{{ $req->move_date }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection