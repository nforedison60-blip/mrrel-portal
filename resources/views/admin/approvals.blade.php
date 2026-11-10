@extends('layouts.admin')

@section('content')
    <h1>Approvals</h1>
    <div class="m">Providers and properties wait here until you verify or approve them.</div>

    <div class="row" style="margin-bottom: 16px;">
        <span class="chip">Providers (4)</span>
        <span class="chip">Properties (3)</span>
    </div>

    <!-- Provider Applications -->
    <div class="card" style="margin-top: 20px;">
        <h3>Provider applications</h3>
        <table class="tb">
            <tr>
                <th>Provider</th>
                <th>Type</th>
                <th>Photo</th>
                <th>ID</th>
                <th>Vehicle and plate</th>
                <th>Documents</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>Pieter · Sample Movers B</td>
                <td>Business</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>Waiting</td>
                <td><button type="button" class="btn" style="padding: 6px 14px; font-size: 13px;">Review</button></td>
            </tr>
            <tr>
                <td>Zanele · Sample Handyman</td>
                <td>Individual</td>
                <td>Yes</td>
                <td>Waiting</td>
                <td>n/a</td>
                <td>Waiting</td>
                <td><button type="button" class="btn" style="padding: 6px 14px; font-size: 13px;">Review</button></td>
            </tr>
            <tr>
                <td>Johan · Sample Storage</td>
                <td>Business</td>
                <td>Yes</td>
                <td>Yes</td>
                <td>n/a</td>
                <td>Yes</td>
                <td><button type="button" class="btn" style="padding: 6px 14px; font-size: 13px;">Review</button></td>
            </tr>
        </table>
    </div>

    <!-- Review Section -->
    <div class="card" style="margin-top: 20px;">
        <h3>Review: Pieter</h3>
        <div class="g3" style="margin-top: 16px;">
            <div>
                <div style="width: 100%; height: 160px; background: #E3F1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <div class="av" style="width: 80px; height: 80px; font-size: 32px;">P</div>
                </div>
                <div class="sm" style="margin-top: 8px;"><b>Profile photo</b></div>
                <div class="m sm">Compare with the ID</div>
            </div>
            <div>
                <div style="width: 100%; height: 160px; background: #E3F1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg width="100" height="50" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                        <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                        <circle cx="20" cy="32" r="5" fill="#51657A"/>
                        <circle cx="60" cy="32" r="5" fill="#51657A"/>
                    </svg>
                </div>
                <div class="sm" style="margin-top: 8px;"><b>Vehicle and plate</b></div>
                <div class="m sm">CA 000 000</div>
            </div>
            <div>
                <div style="width: 100%; height: 160px; background: #E3F1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg width="60" height="80" viewBox="0 0 100 100"><rect x="20" y="10" width="60" height="80" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg>
                </div>
                <div class="sm" style="margin-top: 8px;"><b>ID and documents (private)</b></div>
                <div class="m sm">Never shown to the public</div>
            </div>
        </div>

        <ul style="margin-top: 16px; margin-bottom: 16px;">
            <li>Photo matches ID</li>
            <li>Vehicle registration and insurance checked</li>
            <li>References checked</li>
        </ul>

        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn">Verify</button>
            <button type="button" class="btn g">Request changes</button>
            <button type="button" class="btn g">Reject</button>
        </div>
    </div>
@endsection