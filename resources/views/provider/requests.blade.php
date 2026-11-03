@extends('layouts.provider')

@section('content')
    <h1>Requests</h1>
    <div class="m">All incoming jobs in your area.</div>

    <div class="card" style="margin-top: 20px;">
        <table class="tb">
            <tr>
                <th>Job Details</th>
                <th>Date</th>
                <th>Distance</th>
                <th>Action</th>
            </tr>
            <tr>
                <td><b>1 bed move</b><br><span class="m sm">Parklands to Bellville</span></td>
                <td>Sat 31 Oct</td>
                <td>18 km</td>
                <td><a href="{{ route('provider.send') }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration:none;">Send quote</a></td>
            </tr>
            <tr>
                <td><b>2-bedroom flat</b><br><span class="m sm">Sea Point to Rondebosch</span></td>
                <td>Sun 1 Nov</td>
                <td>22 km</td>
                <td><a href="{{ route('provider.send') }}" class="btn" style="padding: 6px 14px; font-size: 13px; text-decoration:none;">Send quote</a></td>
            </tr>
            <tr>
                <td><b>Small items</b><br><span class="m sm">Claremont to Wynberg</span></td>
                <td>Mon 2 Nov</td>
                <td>8 km</td>
                <td><span class="chip ok">Instant price sent</span></td>
            </tr>
        </table>
    </div>
@endsection