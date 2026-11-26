@extends('layouts.admin')

@section('content')
    <h1>Tenant payments</h1>
    <div class="m">Matching fees and viewing attendance fees paid by tenants. "Mark refunded" records a refund you made in Ozow.</div>

    <div class="g3" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Collected</div><div class="big">R{{ number_format($collected, 2) }}</div></div>
        <div class="card"><div class="m sm">Pending (not paid yet)</div><div class="big">R{{ number_format($pending, 2) }}</div></div>
        <div class="card"><div class="m sm">Payments</div><div class="big">{{ $payments->count() }}</div></div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>All tenant payments</h3>
        @if($payments->isEmpty())
            <p class="m">No tenant payments yet.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr><th>Reference</th><th>Tenant</th><th>What</th><th>Amount</th><th>Status</th><th>Paid</th><th>Action</th></tr>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->reference() }}</td>
                        <td>{{ optional($payment->user)->name ?? 'Removed user' }}</td>
                        <td>{{ $payment->label() }}@if(($payment->meta['round_trip_km'] ?? null)) <div class="m sm">{{ $payment->meta['round_trip_km'] }} km round trip (estimate)</div>@endif</td>
                        <td>R{{ number_format($payment->amount, 2) }}</td>
                        <td><span class="chip {{ $payment->status === 'Paid' ? 'ok' : ($payment->status === 'Refunded' ? 'gr' : 'wn') }}">{{ $payment->status }}</span></td>
                        <td class="m sm">{{ optional($payment->paid_at)->format('d M Y H:i') ?: '-' }}</td>
                        <td>
                            @if($payment->status === 'Paid')
                                <form method="POST" action="{{ route('admin.tenant-payments.refund', $payment->id) }}" style="margin:0;" onsubmit="return confirm({{ \Illuminate\Support\Js::from('Mark ' . $payment->reference() . ' as refunded? Return the money in your Ozow dashboard first. The tenant will lose what this payment unlocked.') }});">
                                    @csrf
                                    <button type="submit" class="btn g" style="padding:4px 12px;font-size:12px;margin:0;">Mark refunded</button>
                                </form>
                            @else
                                <span class="m sm">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
