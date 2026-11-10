@extends('layouts.admin')

@section('content')
    <h1>Tenant matching and shortlists</h1>
    <div class="m">Pick properties for a tenant and send a private shortlist.</div>

    <div class="g3" style="margin-top: 20px;">
        <!-- Tenant Requirement -->
        <div class="card">
            <h3>Tenant requirement</h3>
            <ul>
                <li>Lerato · Cape Town</li>
                <li>Up to R15,000 · 2 bedrooms</li>
                <li>Move-in 1 Dec · 12-month lease</li>
                <li>Needs: parking, fibre</li>
                <li>Viewings: video</li>
                <li>Service fee: [Paid]</li>
            </ul>
        </div>

        <!-- Private Catalogue -->
        <div class="card">
            <h3>Private catalogue</h3>
            <div class="f" style="grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                <label>City
                    <select><option>Cape Town</option></select>
                </label>
                <label>Max rent
                    <input type="number" placeholder="e.g. 15000">
                </label>
            </div>
            <div class="f" style="grid-template-columns: 1fr; margin-bottom: 12px;">
                <label>Bedrooms
                    <select><option>Any</option></select>
                </label>
            </div>

            <table class="tb">
                <tr>
                    <th>Select</th>
                    <th>Property</th>
                    <th>Rent</th>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; background: #E3F1F2; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><svg width="20" height="20" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg></div>
                            2-bed, Sea Point
                        </div>
                    </td>
                    <td>R14,500</td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; background: #E3F1F2; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><svg width="20" height="20" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg></div>
                            2-bed, Gardens
                        </div>
                    </td>
                    <td>R12,900</td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; background: #E3F1F2; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><svg width="20" height="20" viewBox="0 0 100 100"><path d="M10 50L50 10L90 50V90H10V50Z" fill="#D5DEE6" stroke="#51657A" stroke-width="4"/></svg></div>
                            2-bed, Claremont
                        </div>
                    </td>
                    <td>R15,000</td>
                </tr>
            </table>
        </div>

        <!-- Shortlist Builder -->
        <div class="card">
            <h3>Shortlist builder</h3>
            <ul style="margin-bottom: 12px;">
                <li>2-bed, Sea Point: note</li>
                <li>2-bed, Gardens: note</li>
            </ul>
            <div class="f" style="grid-template-columns: 1fr; margin-bottom: 12px;">
                <label>Note to the tenant
                    <input type="text">
                </label>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn">Send shortlist</button>
                <button type="button" class="btn g">Save</button>
            </div>
        </div>
    </div>

    <!-- Tenant Responses -->
    <div class="card" style="margin-top: 20px;">
        <h3>Tenant responses</h3>
        <table class="tb">
            <tr>
                <th>Property</th>
                <th>Response</th>
                <th>Viewing</th>
            </tr>
            <tr>
                <td>2-bed, Sea Point</td>
                <td>Accepted</td>
                <td>Requested: Sat, video</td>
            </tr>
            <tr>
                <td>2-bed, Gardens</td>
                <td>Declined</td>
                <td>—</td>
            </tr>
        </table>
    </div>
@endsection