@php
    $tenantPaid = \App\Models\TenantPayment::where('user_id', auth()->id())->where('status', 'Paid')->orderBy('paid_at')->get();
@endphp
@if($tenantPaid->isNotEmpty())
    <div class="card" style="margin-top: 20px;">
        <h3>MrRel fees you have paid</h3>
        <table class="tb">
            <tr><th>Date</th><th>What</th><th style="text-align:right;">Amount</th></tr>
            @foreach($tenantPaid as $payment)
                <tr>
                    <td class="m sm">{{ optional($payment->paid_at)->format('d M Y') }}</td>
                    <td>{{ $payment->label() }}</td>
                    <td style="text-align:right;">R{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr><td></td><td><b>Total</b></td><td style="text-align:right;"><b>R{{ number_format($tenantPaid->sum('amount'), 2) }}</b></td></tr>
        </table>
    </div>
@endif
