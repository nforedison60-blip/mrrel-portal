@extends('layouts.provider')

@section('content')
    <h1>Bookings, payouts and reviews</h1>
    <div class="m">Expected earnings are not a cash balance.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Bookings</h3>
        <table class="tb">
            <tr>
                <th>Booking</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>BKG-0101</td>
                <td>Lerato</td>
                <td>Sat 31 Oct</td>
                <td><span class="chip ok">Paid</span></td>
            </tr>
            <tr>
                <td>BKG-0102</td>
                <td>Sipho</td>
                <td>Sun 25 Oct</td>
                <td>Job complete</td>
            </tr>
            <tr>
                <td>BKG-0103</td>
                <td>Naledi</td>
                <td>Wed 21 Oct</td>
                <td><span class="chip ok">Resolved</span></td>
            </tr>
        </table>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Payout ledger</h3>
        <table class="tb">
            <tr>
                <th>Booking</th>
                <th>Customer paid</th>
                <th>Job complete</th>
                <th>Ready for payout</th>
                <th>Paid out</th>
            </tr>
            <tr>
                <td>BKG-0101</td>
                <td>Yes</td>
                <td>No</td>
                <td>No</td>
                <td>—</td>
            </tr>
            <tr>
                <td>BKG-0102</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Waiting</td>
            </tr>
            <tr>
                <td>BKG-0103</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>R600 on [date]</td>
            </tr>
        </table>
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Reviews</h3>
            <ul>
                <li>5 stars: On time and careful. Example.</li>
                <li>4 stars: Good price. Example.</li>
            </ul>
            <button type="button" class="btn g">Respond</button>
        </div>
        <div class="card">
            <h3>Problems</h3>
            <ul>
                <li>BKG-0105: damaged item reported. In progress</li>
            </ul>
            <button type="button" class="btn g">Add evidence</button>
        </div>
    </div>
@endsection