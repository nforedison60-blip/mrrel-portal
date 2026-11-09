@extends('layouts.admin')

@section('content')
    <h1>Requests and Quotes</h1>
    <div class="m">Monitor all customer requests and the quotes providers have sent.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Customer Requests</h3>
        @if($requests->isEmpty())
            <p class="m">No customer requests yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Route / Location</th>
                    <th>Date</th>
                </tr>
                @foreach($requests as $req)
                <tr>
                    <td>#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td><b>{{ $req->services_needed }}</b></td>
                    <td>{{ $req->moving_from }} to {{ $req->moving_to }}</td>
                    <td>{{ $req->move_date }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Provider Quotes</h3>
        @if($quotes->isEmpty())
            <p class="m">No quotes sent yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Quote ID</th>
                    <th>Provider</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
                @foreach($quotes as $quote)
                <tr>
                    <td>QTN-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $quote->provider_name }}</td>
                    <td><b>R{{ number_format($quote->total_price, 2) }}</b></td>
                    <td>{{ $quote->price_type }}</td>
                    <td><span class="chip ok">Active</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection