@extends('layouts.app')

@section('content')
    <h1>Rental fees</h1>
    <div class="m">Pages not shown elsewhere.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Fees</h3>
        <table class="tb">
            <tr>
                <th>Fee</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Rental service fee</td>
                <td>[R amount]</td>
                <td><span class="chip wn">Waiting</span></td>
            </tr>
            <tr>
                <td>Viewing fee (video)</td>
                <td>[R amount]</td>
                <td><span class="chip ok">Paid</span></td>
            </tr>
        </table>
        <button type="button" class="btn" style="margin-top: 16px;">Pay now</button>
        <p class="m sm" style="margin-top: 12px;">Fees are shown before you pay. MrRel never takes rent or deposits.</p>
    </div>
@endsection