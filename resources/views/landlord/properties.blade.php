@extends('layouts.landlord')

@section('content')
    <h1>My properties</h1>
    <div class="m">Properties stay private. Only MrRel decides which tenants see them.</div>

    <div class="g4">
        <div class="card"><div class="m sm">Approved</div><div class="big">1</div><div class="m sm">Visible to tenants</div></div>
        <div class="card"><div class="m sm">Submitted</div><div class="big">1</div><div class="m sm">Waiting for MrRel</div></div>
        <div class="card"><div class="m sm">Changes requested</div><div class="big">1</div><div class="m sm">Needs your attention</div></div>
        <div class="card"><div class="m sm">Draft</div><div class="big">1</div><div class="m sm">Not submitted yet</div></div>
    </div>

    <div style="margin-bottom: 16px;">
        <a href="{{ route('landlord.add-property') }}" class="btn" style="text-decoration:none;">Add property</a>
    </div>

    <div class="card">
        <h3>Your properties</h3>
        <table class="tb">
            <tr>
                <th>Property</th>
                <th>Rent</th>
                <th>Status</th>
                <th>Updated</th>
            </tr>
            <tr>
                <td>3-bed house, Umhlanga</td>
                <td>R22,000</td>
                <td><span class="chip ok">Approved</span></td>
                <td>20 Oct</td>
            </tr>
            <tr>
                <td>1-bed flat, Rosebank</td>
                <td>R9,800</td>
                <td><span class="chip wn">Submitted</span></td>
                <td>25 Oct</td>
            </tr>
            <tr>
                <td>2-bed apartment, Sea Point</td>
                <td>R14,500</td>
                <td><span class="chip wn">Changes requested</span></td>
                <td>26 Oct</td>
            </tr>
            <tr>
                <td>Studio, Gardens</td>
                <td>R7,500</td>
                <td><span class="chip">Draft</span></td>
                <td>27 Oct</td>
            </tr>
        </table>
    </div>
@endsection