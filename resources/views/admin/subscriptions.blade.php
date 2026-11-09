@extends('layouts.admin')

@section('content')
    <h1>Subscriptions</h1>
    <div class="m">Manage provider subscription plans.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Active Plans</h3>
        <table class="tb">
            <tr>
                <th>Plan Name</th>
                <th>Price</th>
                <th>Features</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><b>Free</b></td>
                <td>R0 / month</td>
                <td>Maximum 2 quotes per month</td>
                <td><span class="chip ok">Active</span></td>
            </tr>
            <tr>
                <td><b>Pro</b></td>
                <td>R299 / month</td>
                <td>Unlimited quotes, priority support, featured badge</td>
                <td><span class="chip ok">Active</span></td>
            </tr>
        </table>
    </div>
@endsection