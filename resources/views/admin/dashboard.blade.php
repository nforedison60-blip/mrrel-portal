@extends('layouts.admin')

@section('content')
    <h1>Admin Dashboard</h1>
    <div class="m">Overview of the MrRel platform.</div>

    <div class="g4" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Total Requests</div><div class="big">{{ $totalRequests }}</div></div>
        <div class="card"><div class="m sm">Active Bookings</div><div class="big">{{ $totalBookings }}</div></div>
        <div class="card"><div class="m sm">Quotes Sent</div><div class="big">{{ $totalQuotes }}</div></div>
        <div class="card"><div class="m sm">Revenue</div><div class="big">R{{ number_format($revenue, 2) }}</div></div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <h3 style="margin:0;">All Properties</h3>
            
            <!-- FILTER & SEARCH FORM -->
            <form method="GET" action="{{ route('admin.dashboard') }}" style="margin:0; display:flex; gap:10px; align-items:center; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Search title or suburb..." value="{{ request('search') }}" style="width:200px; margin:0; padding:6px 12px;">
                
                <select name="status" onchange="this.form.submit()" style="width:auto; margin:0; padding:6px 12px;">
                    <option value="All" {{ request('status') == 'All' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                </select>
                
                <button type="submit" class="btn" style="padding: 6px 14px; font-size: 13px; margin:0;">Search</button>
            </form>
        </div>

        @if($properties->isEmpty())
            <p class="m">No properties found matching your criteria.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Property</th>
                    <th>Landlord Rent</th>
                    <th>Status</th>
                </tr>
                @foreach($properties as $property)
                <tr>
                    <td>
                        <b>{{ $property->title }}</b><br>
                        <span class="m sm">{{ $property->suburb }}, {{ $property->city }}</span>
                    </td>
                    <td>R{{ number_format($property->rent, 0) }}</td>
                    <td><span class="chip {{ $property->status == 'Approved' ? 'ok' : 'wn' }}">{{ $property->status }}</span></td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection