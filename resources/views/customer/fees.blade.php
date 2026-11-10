@extends('layouts.app')

@section('content')
    <h1>Rental fees</h1>
    <div class="m">Your upcoming payments and invoices.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Next Payment Due</div>
            <div class="big">R{{ number_format($properties->sum('rent'), 0) }}</div>
            <div class="m sm">Due on 01 Nov 2026</div>
        </div>
        <div class="card">
            <div class="m sm">Deposit Paid</div>
            <div class="big">R{{ number_format($properties->sum('deposit'), 0) }}</div>
            <div class="m sm"><span class="chip ok">Paid</span></div>
        </div>
        <div class="card">
            <div class="m sm">Service Fee</div>
            <div class="big">R1,500</div>
            <div class="m sm"><span class="chip ok">Paid</span></div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Payment Schedule</h3>
        @if($properties->isEmpty())
            <p class="m">You have no accepted properties yet, so there are no rental fees.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Description</th>
                    <th>Property</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>First Month's Rent</td>
                    <td>{{ $property->title }}</td>
                    <td>01 Nov 2026</td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td><span class="chip wn">Upcoming</span></td>
                </tr>
                <tr>
                    <td>Security Deposit</td>
                    <td>{{ $property->title }}</td>
                    <td>25 Oct 2026</td>
                    <td>R{{ number_format($property->deposit, 0) }}</td>
                    <td><span class="chip ok">Paid</span></td>
                </tr>
                @endforeach
                <tr>
                    <td>MrRel Service Fee</td>
                    <td>—</td>
                    <td>25 Oct 2026</td>
                    <td>R1,500.00</td>
                    <td><span class="chip ok">Paid</span></td>
                </tr>
            </table>
        @endif
    </div>
@endsection