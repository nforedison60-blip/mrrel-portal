@extends('layouts.admin')

@section('content')
    <h1>Bank payments</h1>
    <div class="m">People who chose to pay by bank transfer email their proof of payment to <b>{{ $popEmail }}</b> with the reference in the subject. When the money is in the account, press "Confirm payment received". This does what an online payment does: it marks the item paid, releases the details and sends the emails.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Waiting for payment</h3>
        @if($rows->isEmpty())
            <p class="m">Nothing is waiting for payment.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr><th>Reference</th><th>Who</th><th>What</th><th>Amount</th><th>Started</th><th>Action</th></tr>
                @foreach($rows as $row)
                    <tr>
                        <td><b>{{ $row['ref'] }}</b></td>
                        <td>{{ $row['who'] ?: 'Removed user' }}</td>
                        <td>{{ $row['what'] }}</td>
                        <td>R{{ number_format($row['amount'], 2) }}</td>
                        <td class="m sm">{{ optional($row['at'])->format('d M Y H:i') }}</td>
                        <td>
                            @if($row['confirmable'])
                                <form method="POST" action="{{ route('admin.bank-payments.confirm') }}" style="margin:0;" onsubmit="return confirm('Confirm that the money for {{ $row['ref'] }} (R{{ number_format($row['amount'], 2) }}) is in the bank account?');">
                                    @csrf
                                    <input type="hidden" name="reference" value="{{ $row['ref'] }}">
                                    <button type="submit" class="btn" style="margin:0; padding:6px 14px; font-size:13px;">Confirm payment received</button>
                                </form>
                            @else
                                <a href="{{ route('admin.invoices') }}" class="m sm">Mark paid under Invoices</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
