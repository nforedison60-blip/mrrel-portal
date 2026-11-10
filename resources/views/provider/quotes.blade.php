@extends('layouts.provider')

@section('content')
    <div class="m">Pages not shown elsewhere.</div>
    <h1>My quotes</h1>

    <!-- QUOTES SECTION -->
    <div class="card" style="margin-top: 20px;">
        <h3>Quotes</h3>
        @if($quotes->isEmpty())
            <p class="m">No quotes sent yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Quote</th>
                    <th>Request</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                @foreach($quotes as $quote)
                <tr>
                    <td>QTN-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }} Rev 01</td>
                    <td>1 bed, Parklands to Bellville</td> <!-- Placeholder -->
                    <td>R{{ number_format($quote->total_price, 0) }}</td>
                    <td><span class="chip {{ $quote->validity == 'Accepted' ? 'ok' : ($quote->validity == 'Pending' ? 'wn' : '') }}">{{ $quote->validity ?? 'Sent' }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <!-- REVIEWS SECTION -->
    <div class="card" style="margin-top: 20px;">
        <h3>Reviews</h3>
        <ul style="margin-bottom: 12px;">
            <li>5 stars: On time and careful. Example.</li>
            <li>4 stars: Good price. Example.</li>
        </ul>
        <button type="button" class="btn g">Respond</button>
    </div>

    <!-- PROBLEMS SECTION -->
    <div class="card" style="margin-top: 20px;">
        <h3>Problems</h3>
        @if($problems->isEmpty())
            <p class="m">No problems reported.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Ticket</th>
                    <th>Booking</th>
                    <th>Status</th>
                </tr>
                @foreach($problems as $problem)
                <tr>
                    <td>D-{{ str_pad($problem->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>BKG-{{ str_pad($problem->id, 4, '0', STR_PAD_LEFT) }}</td> <!-- Placeholder -->
                    <td><span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">{{ $problem->status == 'Resolved' ? 'Resolved' : 'In progress' }}</span></td>
                </tr>
                @endforeach
            </table>
            <button type="button" class="btn g" style="margin-top: 16px;">Add evidence</button>
        @endif
    </div>
@endsection