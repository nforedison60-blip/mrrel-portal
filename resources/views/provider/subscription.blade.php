@extends('layouts.provider')

@section('content')
    <h1>Subscription</h1>
    <div class="m">MrRel takes a {{ round(config('services.mrrel.commission_rate') * 100) }}% commission on each paid job. There is no monthly fee while MrRel launches.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Your plan</h3>
        <div class="big">Founding member</div>
        <div class="m sm" style="margin-bottom: 12px;">No monthly fee while MrRel launches</div>
        <ul style="padding-left: 16px;">
            <li>Send quotes on open requests</li>
            <li>Verified badge once MrRel has checked your documents</li>
            <li>MrRel keeps {{ round(config('services.mrrel.commission_rate') * 100) }}% of each paid job. You receive the other {{ 100 - round(config('services.mrrel.commission_rate') * 100) }}%.</li>
        </ul>
        <div class="m sm" style="margin-top: 12px;">Payouts are made by MrRel. A job with an open dispute is paused until it is resolved. If a monthly fee is ever introduced, you will be told before anything is charged.</div>
    </div>
@endsection
