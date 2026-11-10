@extends('layouts.admin')

@section('content')
    <h1>Admin: other screens</h1>
    <div class="m">Pages not shown elsewhere.</div>

    <!-- USERS SECTION -->
    <h2 style="margin-top: 20px;">Users</h2>
    <div class="card">
        <h3>Accounts</h3>
        <table class="tb">
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>Customer</td>
                <td><span class="chip ok">Active</span></td>
                <td>
                    <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0 4px 0 0;">Export</button>
                    <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Suspend</button>
                </td>
            </tr>
            @endforeach
            @foreach($providers as $provider)
            <tr>
                <td>{{ $provider->name }}</td>
                <td>Provider</td>
                <td><span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">{{ $provider->verification_status == 'Verified' ? 'Active' : 'Pending' }}</span></td>
                <td>
                    @if($provider->verification_status == 'Verified')
                        <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0 4px 0 0;">Export</button>
                        <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Suspend</button>
                    @else
                        <button type="button" class="btn" style="padding: 4px 12px; font-size: 12px; margin:0;">Approve</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <p class="m sm" style="margin-top: 16px;">Includes data export and deletion requests.</p>
    </div>

    <!-- REQUESTS & QUOTES SECTION -->
    <h2 style="margin-top: 30px;">Requests and quotes</h2>
    <div class="card">
        <h3>Requests</h3>
        <table class="tb">
            <tr>
                <th>Request</th>
                <th>Services</th>
                <th>Quotes</th>
                <th>Action</th>
            </tr>
            @foreach($requests as $req)
            <tr>
                <td>REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $req->services_needed }}</td>
                <td>{{ $quotes->where('service_request_id', $req->id)->count() }}</td>
                <td>
                    <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Invite providers</button>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection