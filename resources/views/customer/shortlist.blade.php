@extends('layouts.app')

@section('content')
    <h1>My shortlist</h1>
    <div class="m">Cape Town · up to R15,000 · 2 bedrooms · move-in 1 December. Only places MrRel shares with you.</div>

    <div class="g3" style="margin-top: 20px;">
        <!-- Property 1 -->
        <div class="card">
            <div class="ph">[Property photo]</div>
            <h3>2-bed apartment, Sea Point</h3>
            <div class="m sm">R14,500 a month · Deposit R14,500 · Available 1 Dec · Parking, fibre</div>
            <p class="sm" style="margin-top: 8px;"><b>MrRel note:</b> Close to transport.</p>
            <div style="margin-top: 12px;">
                <button type="button" class="btn">Accept</button>
                <button type="button" class="btn g">Decline</button>
                <button type="button" class="btn g">Request viewing</button>
                <a href="{{ route('customer.new-request') }}" class="btn g" style="text-decoration:none;">Plan move for this property</a>
            </div>
        </div>

        <!-- Property 2 -->
        <div class="card">
            <div class="ph">[Property photo]</div>
            <h3>2-bed flat, Gardens</h3>
            <div class="m sm">R12,900 a month · Deposit R12,900 · Available 15 Dec · Furnished</div>
            <p class="sm" style="margin-top: 8px;"><b>MrRel note:</b> Pets by request.</p>
            <div style="margin-top: 12px;">
                <button type="button" class="btn">Accept</button>
                <button type="button" class="btn g">Decline</button>
                <button type="button" class="btn g">Request viewing</button>
                <a href="{{ route('customer.new-request') }}" class="btn g" style="text-decoration:none;">Plan move for this property</a>
            </div>
        </div>

        <!-- Property 3 -->
        <div class="card">
            <div class="ph">[Property photo]</div>
            <h3>2-bed townhouse, Claremont</h3>
            <div class="m sm">R15,000 a month · Deposit R15,000 · Available 1 Jan · Garden</div>
            <p class="sm" style="margin-top: 8px;"><b>MrRel note:</b> 12-month lease wanted.</p>
            <div style="margin-top: 12px;">
                <button type="button" class="btn">Accept</button>
                <button type="button" class="btn g">Decline</button>
                <button type="button" class="btn g">Request viewing</button>
                <a href="{{ route('customer.new-request') }}" class="btn g" style="text-decoration:none;">Plan move for this property</a>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Notes</h3>
        <p class="sm">Accepting a place does not reserve it, create a lease or guarantee availability. Rent and deposit are paid directly to the landlord. Viewing requests can be video or in person.</p>
    </div>
@endsection