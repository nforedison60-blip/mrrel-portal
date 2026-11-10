@extends('layouts.provider')

@section('content')
    <h1>Subscription</h1>
    <div class="m">You pay one monthly fee. MrRel takes no commission on your jobs.</div>

    <div class="g3" style="margin-top: 20px;">
        <!-- Basic -->
        <div class="card">
            <h3>Basic</h3>
            <div class="big">R0</div>
            <div class="m sm" style="margin-bottom: 12px;">a month</div>
            <ul style="margin-bottom: 16px; padding-left: 16px;">
                <li>Listing and service areas</li>
                <li>Contact button</li>
            </ul>
            <button type="button" class="btn g">Choose Basic</button>
        </div>

        <!-- Pro -->
        <div class="card" style="border: 2px solid var(--t); background: #F5F8FA;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin:0;">Pro</h3>
                <span class="chip ok">Active</span>
            </div>
            <div class="big">R299</div>
            <div class="m sm" style="margin-bottom: 12px;">a month</div>
            <ul style="margin-bottom: 16px; padding-left: 16px;">
                <li>Verified badge, photos, reviews</li>
                <li>Higher placement</li>
            </ul>
            <button type="button" class="btn g">Current plan</button>
        </div>

        <!-- Premium -->
        <div class="card">
            <h3>Premium</h3>
            <div class="big">R599</div>
            <div class="m sm" style="margin-bottom: 12px;">a month</div>
            <ul style="margin-bottom: 16px; padding-left: 16px;">
                <li>Featured placement</li>
                <li>Priority in move bundles</li>
            </ul>
            <button type="button" class="btn g">Choose Premium</button>
        </div>
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Status</h3>
            <ul>
                <li>Plan: Pro</li>
                <li>Status: Active</li>
                <li>Next billing: [date]</li>
                <li>Founding member free period ends: [date]</li>
            </ul>
            <button type="button" class="btn g" style="margin-top: 12px;">Cancel subscription</button>
        </div>

        <div class="card">
            <h3>Payment history</h3>
            <table class="tb">
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>01 Oct 2026</td>
                    <td>R299.00</td>
                    <td><span class="chip ok">Paid</span></td>
                </tr>
                <tr>
                    <td>01 Sep 2026</td>
                    <td>R299.00</td>
                    <td><span class="chip ok">Paid</span></td>
                </tr>
            </table>
        </div>
    </div>
@endsection