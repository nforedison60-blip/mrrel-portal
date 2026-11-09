@extends('layouts.admin')

@section('content')
    <h1>Users</h1>
    <div class="m">Manage all registered Customers and Providers on the platform.</div>

    <div class="g2" style="margin-top: 20px;">
        <!-- CUSTOMERS TABLE -->
        <div class="card">
            <h3>Customers / Tenants</h3>
            @if($customers->isEmpty())
                <p class="m">No customers registered yet.</p>
            @else
                <table class="tb">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                    </tr>
                    @foreach($customers as $customer)
                    <tr>
                        <td><b>{{ $customer->name }}</b></td>
                        <td>
                            {{ $customer->email }}<br>
                            <span class="m sm">{{ $customer->phone }}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <!-- PROVIDERS TABLE -->
        <div class="card">
            <h3>Service Providers</h3>
            @if($providers->isEmpty())
                <p class="m">No providers registered yet.</p>
            @else
                <table class="tb">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                    </tr>
                    @foreach($providers as $provider)
                    <tr>
                        <td>
                            <b>{{ $provider->name }}</b><br>
                            <span class="m sm">{{ $provider->company_name }}</span>
                        </td>
                        <td>
                            <span class="chip {{ $provider->verification_status == 'Verified' ? 'ok' : 'wn' }}">
                                {{ $provider->verification_status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@endsection