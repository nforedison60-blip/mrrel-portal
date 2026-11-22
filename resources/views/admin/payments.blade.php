@extends('layouts.admin')

@section('content')
@php
    $bookings->load('quote');
    $paid = $bookings->where('status', 'Paid');
    $thisMonth = $paid->filter(fn($b) => $b->created_at && $b->created_at->isCurrentMonth());
    $confirmed = $paid->where('job_confirmed', true);
@endphp

    <h1>Payments</h1>
    <div class="m">Payments made by customers, and which jobs they have confirmed. Payouts to providers are made by hand for now.</div>

    <div class="g4" style="margin-top:20px;">
        <div class="card">
            <div class="m sm">Received this month</div>
            <div class="big">R{{ number_format($thisMonth->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $thisMonth->count() }} {{ \Illuminate\Support\Str::plural('booking', $thisMonth->count()) }}</div>
        </div>
        <div class="card">
            <div class="m sm">Received in total</div>
            <div class="big">R{{ number_format($paid->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $paid->count() }} {{ \Illuminate\Support\Str::plural('booking', $paid->count()) }}</div>
        </div>
        <div class="card">
            <div class="m sm">Confirmed by customers</div>
            <div class="big">R{{ number_format($confirmed->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $confirmed->count() }} {{ \Illuminate\Support\Str::plural('job', $confirmed->count()) }} done</div>
        </div>
        <div class="card">
            <div class="m sm">Waiting for confirmation</div>
            <div class="big">R{{ number_format($paid->sum('total_price') - $confirmed->sum('total_price'), 2) }}</div>
            <div class="m sm">{{ $paid->count() - $confirmed->count() }} {{ \Illuminate\Support\Str::plural('job', $paid->count() - $confirmed->count()) }} open</div>
        </div>
    </div>

    @php $rate = config('services.mrrel.commission_rate'); @endphp
    <div class="g2">
        <div class="card">
            <div class="m sm">MrRel commission ({{ round($rate * 100) }}%)</div>
            <div class="big">R{{ number_format($paid->sum('total_price') * $rate, 2) }}</div>
            <div class="m sm">on money received so far</div>
        </div>
        <div class="card">
            <div class="m sm">Owed to providers ({{ 100 - round($rate * 100) }}%)</div>
            <div class="big">R{{ number_format($paid->sum('total_price') * (1 - $rate), 2) }}</div>
            <div class="m sm">before any payouts you have already made</div>
        </div>
    </div>

    @php
        // What each provider is owed, with their bank details
        $byProvider = $paid->groupBy(fn($b) => optional($b->quote)->provider_user_id);
    @endphp
    <div class="card" style="margin-top:20px;">
        <h3>Provider payouts</h3>
        @if($byProvider->isEmpty())
            <p class="m">Nothing to pay out yet. Providers appear here once a customer has paid for their job.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr><th>Provider</th><th>Still to pay</th><th>Already paid out</th><th>Bank details</th></tr>
                @foreach($byProvider as $providerUserId => $jobs)
                    @php
                        $prov = $providerUserId ? (\App\Models\Provider::where('user_id', $providerUserId)->first()
                                ?: optional(\App\Models\User::find($providerUserId), fn($u) => \App\Models\Provider::where('email', $u->email)->first())) : null;
                        $open = $jobs->whereNull('paid_out_at');
                        $done = $jobs->whereNotNull('paid_out_at');
                    @endphp
                    <tr>
                        <td><b>{{ optional($jobs->first()->quote)->provider_name ?: 'Provider' }}</b></td>
                        <td>R{{ number_format($open->sum('total_price') * (1 - $rate), 2) }}<div class="m sm">{{ $open->count() }} {{ \Illuminate\Support\Str::plural('job', $open->count()) }}</div></td>
                        <td>R{{ number_format($done->sum('total_price') * (1 - $rate), 2) }}</td>
                        <td class="sm">
                            @if($prov && $prov->hasBankDetails())
                                {{ $prov->bank_name }} · {{ $prov->bank_account_type }}<br>
                                {{ $prov->bank_account_holder }}<br>
                                Account {{ $prov->bank_account_number }}@if($prov->bank_branch_code) · Branch {{ $prov->bank_branch_code }}@endif
                            @else
                                <span class="chip wn">Not added yet</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>

    <div class="card" style="margin-top:20px;">
        <h3>Bookings</h3>
        @if($bookings->isEmpty())
            <p class="m">No bookings yet. They appear here when a customer books a quote.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Booking</th>
                    <th>Provider</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Job confirmed</th>
                    <th>Pay provider</th>
                    <th>Date</th>
                </tr>
                @foreach($bookings as $b)
                    <tr>
                        <td>BKG-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($b->quote)->provider_name ?: '—' }}</td>
                        <td>{{ $b->customer_name }}</td>
                        <td>R{{ number_format($b->total_price, 2) }}</td>
                        <td><span class="chip {{ $b->status === 'Paid' ? 'ok' : 'wn' }}">{{ $b->status }}</span></td>
                        <td><span class="chip {{ $b->job_confirmed ? 'ok' : '' }}">{{ $b->job_confirmed ? 'Yes' : 'Not yet' }}</span></td>
                        <td>
                            @if($b->status === 'Paid')
                                <div class="sm">R{{ number_format($b->total_price * (1 - $rate), 2) }}</div>
                                <form method="POST" action="{{ route('admin.bookings.payout', $b->id) }}" style="margin-top:4px;">
                                    @csrf
                                    <button type="submit" class="btn {{ $b->paid_out_at ? 'g' : '' }}" style="padding:4px 12px;font-size:12px;margin:0;">{{ $b->paid_out_at ? 'Paid out ' . $b->paid_out_at->format('d M') . ' (undo)' : 'Mark paid out' }}</button>
                                </form>
                            @else
                                <span class="m sm">—</span>
                            @endif
                        </td>
                        <td class="m sm">{{ optional($b->created_at)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection