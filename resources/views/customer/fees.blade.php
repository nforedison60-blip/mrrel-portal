@extends('layouts.app')

@section('content')
    <h1>Rental fees</h1>
    <div class="m">Your upcoming payments and invoices.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Next Payment Due</div>
            <div class="big">R14,500</div>
            <div class="m sm">Due on 01 Nov 2026</div>
        </div>
        <div class="card">
            <div class="m sm">Deposit Paid</div>
            <div class="big">R14,500</div>
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
        <table class="tb">
            <tr>
                <th>Description</th>
                <th>Property</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>First Month's Rent</td>
                <td>2-bed apartment, Sea Point</td>
                <td>01 Nov 2026</td>
                <td>R14,500.00</td>
                <td><span class="chip wn">Upcoming</span></td>
            </tr>
            <tr>
                <td>Security Deposit</td>
                <td>2-bed apartment, Sea Point</td>
                <td>25 Oct 2026</td>
                <td>R14,500.00</td>
                <td><span class="chip ok">Paid</span></td>
            </tr>
            <tr>
                <td>MrRel Service Fee</td>
                <td>2-bed apartment, Sea Point</td>
                <td>25 Oct 2026</td>
                <td>R1,500.00</td>
                <td><span class="chip ok">Paid</span></td>
            </tr>
            <tr>
                <td>Second Month's Rent</td>
                <td>2-bed apartment, Sea Point</td>
                <td>01 Dec 2026</td>
                <td>R14,500.00</td>
                <td><span class="chip">Scheduled</span></td>
            </tr>
        </table>
    </div>
@endsection