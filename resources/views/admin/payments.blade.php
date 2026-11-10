@extends('layouts.admin')

@section('content')
    <h1>Payments and payouts</h1>
    <div class="m">Manual payouts at launch. Record each one.</div>

    <!-- Stat Cards -->
    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Received this month</div><div class="big">R48,200</div></div>
        <div class="card"><div class="m sm">Ready for payout</div><div class="big">R12,600</div></div>
        <div class="card"><div class="m sm">Paid out</div><div class="big">R30,400</div></div>
        <div class="card"><div class="m sm">Subscriptions and rental fees</div><div class="big">R9,800</div></div>
    </div>

    <!-- Ledger -->
    <div class="card" style="margin-top: 20px;">
        <h3>Ledger</h3>
        
        <div class="pl" style="margin-bottom: 16px;">
            <label><input type="radio" name="filter" checked> All</label>
            <label><input type="radio" name="filter"> Ready for payout</label>
            <label><input type="radio" name="filter"> Waiting for customer</label>
            <label><input type="radio" name="filter"> Paid out</label>
            <label><input type="radio" name="filter"> Disputed</label>
        </div>

        <table class="tb">
            <tr>
                <th>Booking</th>
                <th>Provider</th>
                <th>Customer paid</th>
                <th>Job complete</th>
                <th>Ready</th>
                <th>Paid out</th>
                <th>Amount</th>
                <th>Reference</th>
            </tr>
            <tr>
                <td>BKG-0101</td>
                <td>Pieter</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>No</td>
                <td>R600</td>
                <td>[ref]</td>
            </tr>
            <tr>
                <td>BKG-0102</td>
                <td>Thandi</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>R450</td>
                <td>[ref]</td>
            </tr>
            <tr>
                <td>BKG-0103</td>
                <td>Lwazi</td>
                <td>Yes</td>
                <td>No</td>
                <td>No</td>
                <td>No</td>
                <td>R1,200</td>
                <td>[ref]</td>
            </tr>
        </table>
    </div>

    <!-- Record a payout -->
    <div class="card" style="margin-top: 20px; max-width: 800px;">
        <h3>Record a payout</h3>
        <form method="POST" action="#">
            @csrf
            <div class="f">
                <label>Booking
                    <select>
                        <option>BKG-0101</option>
                    </select>
                </label>
                <label>Amount (R)
                    <input type="number" placeholder="e.g. 600">
                </label>
                <label>Date
                    <input type="date">
                </label>
                <label>Reference
                    <input type="text" placeholder="e.g. EFT-12345">
                </label>
            </div>
            <button type="submit" class="btn">Record payout</button>
        </form>
    </div>
@endsection