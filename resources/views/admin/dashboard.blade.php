@extends('layouts.admin')

@section('content')
    <h1>Admin dashboard</h1>
    <div class="m">Everything the MrRel team manages. Not shown on the public site.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card">
            <div class="m sm">Provider applications</div>
            <div class="big">{{ $providerApplications }}</div>
            <div class="m sm">waiting</div>
        </div>
        <div class="card">
            <div class="m sm">Properties to review</div>
            <div class="big">{{ $propertiesToReview }}</div>
            <div class="m sm">waiting</div>
        </div>
        <div class="card">
            <div class="m sm">Tenants to match</div>
            <div class="big">5</div>
            <div class="m sm">new requirements</div>
        </div>
        <div class="card">
            <div class="m sm">Open disputes</div>
            <div class="big">{{ $openDisputes }}</div>
            <div class="m sm">need a decision</div>
        </div>
    </div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>To do today</h3>
            <ul>
                <li>Verify {{ $providerApplications }} providers</li>
                <li>Build 3 shortlists</li>
                <li>Record 4 payouts</li>
                <li>Resolve {{ $openDisputes }} disputes</li>
            </ul>
        </div>
        <div class="card">
            <h3>This week</h3>
            <ul>
                <li>Tenants served: 9</li>
                <li>Average time to first options: 1.5 days</li>
                <li>Refund rate: 3%</li>
                <li>Provider renewals: 8 of 10</li>
            </ul>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Live jobs today</h3>
        <div class="sp" style="margin-top: 16px;">
            <!-- Map Placeholder -->
            <div style="width: 100%; height: 200px; background-color: #E3F1F2; border-radius: 8px; position: relative; overflow: hidden;">
                <div style="position: absolute; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                     <svg width="400" height="150" viewBox="0 0 400 150">
                        <path d="M 50 100 Q 150 50 250 100 T 350 50" stroke="#0E7C86" stroke-width="4" stroke-dasharray="8 8" fill="none"/>
                        <circle cx="50" cy="100" r="8" fill="#F4B731" stroke="#10243A" stroke-width="2"/>
                        <circle cx="350" cy="50" r="8" fill="#0E7C86"/>
                     </svg>
                </div>
            </div>

            <!-- Live Jobs Table -->
            <div>
                <table class="tb">
                    <tr>
                        <th>Booking</th>
                        <th>Provider</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>BKG-0101</td>
                        <td>Pieter</td>
                        <td><span class="chip wn">In progress</span></td>
                    </tr>
                    <tr>
                        <td>BKG-0104</td>
                        <td>Thandi</td>
                        <td><span class="chip wn">Waiting</span></td>
                    </tr>
                    <tr>
                        <td>BKG-0107</td>
                        <td>Lwazi</td>
                        <td><span class="chip ok">Paid</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection