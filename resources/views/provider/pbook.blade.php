@extends('layouts.provider')

@section('content')
    <h1>Bookings, payouts and reviews</h1>
    <div class="m">Expected earnings are not a cash balance.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Bookings</h3>
        @if($bookings->isEmpty())
            <p class="m">No bookings yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $booking->customer_name }}</td>
                    <td>{{ $booking->created_at->format('D d M') }}</td>
                    <td>
                        @if($booking->status == 'Paid')
                            <span class="chip ok">Paid</span>
                        @elseif($booking->status == 'Resolved')
                            <span class="chip ok">Resolved</span>
                        @else
                            <span class="chip">Job complete</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Payout ledger</h3>
        @if($bookings->isEmpty())
            <p class="m">No payouts yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Customer paid</th>
                    <th>Job complete</th>
                    <th>Ready for payout</th>
                    <th>Paid out</th>
                </tr>
                @foreach($bookings as $booking)
                <tr>
                    <td>BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>Yes</td>
                    <td>{{ $loop->first ? 'No' : 'Yes' }}</td>
                    <td>{{ $loop->first ? 'No' : 'Yes' }}</td>
                    <td>{{ $loop->first ? '—' : ($loop->index == 1 ? 'Waiting' : 'R600 on 21 Oct') }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Reviews</h3>
            <div class="row" style="margin-bottom: 8px;">
                <span class="av" style="width: 32px; height: 32px; font-size: 14px; background: #D5DEE6;">P</span>
                <div class="sm">5 stars: On time and careful. Example.</div>
            </div>
            <div class="row" style="margin-bottom: 12px;">
                <span class="av" style="width: 32px; height: 32px; font-size: 14px; background: #D5DEE6;">P</span>
                <div class="sm">4 stars: Good price. Example.</div>
            </div>
            <button type="button" class="btn g">Respond</button>
        </div>
        
        <div class="card">
            <h3>Problems</h3>
            @if($problems->isEmpty())
                <p class="m">No problems reported.</p>
            @else
                <ul style="margin-bottom: 12px;">
                    @foreach($problems as $problem)
                        <li>D-{{ str_pad($problem->id, 3, '0', STR_PAD_LEFT) }}: {{ $problem->description }}. {{ $problem->status }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn g">Add evidence</button>
            @endif
        </div>
    </div>
@endsection