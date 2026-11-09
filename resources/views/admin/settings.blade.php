@extends('layouts.admin')

@section('content')
    <h1>Settings and Reports</h1>
    <div class="m">Manage platform configuration and download reports.</div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Platform Settings</h3>
            <div class="f">
                <label class="k">Platform Name<input type="text" value="MrRel"></label>
                <label class="k">Support Email<input type="text" value="support@mrrel.co.za"></label>
                <label class="k">Service Fee (%)<input type="number" value="10"></label>
            </div>
            <button type="button" class="btn">Save Settings</button>
        </div>

        <div class="card">
            <h3>Download Reports</h3>
            <p class="m sm">Export platform data for accounting.</p>
            <button type="button" class="btn g w">Download Bookings CSV</button>
            <button type="button" class="btn g w">Download Users CSV</button>
            <button type="button" class="btn g w">Download Revenue Report</button>
        </div>
    </div>
@endsection