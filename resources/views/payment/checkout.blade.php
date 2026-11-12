@extends('layouts.app')

@section('content')
<h1>Checkout</h1>
<div class="card" style="max-width: 500px; margin-top: 20px;">
    <h3>Job Payment</h3>
    <p class="m">You are about to pay for your most recent MrRel booking. The exact amount will be shown on the PayFast screen.</p>
    
    <!-- Show error if there is no booking -->
    @if(session('error'))
        <div style="background:#FDF1CC; color:#5C4300; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #F4B731; margin-top: 16px;">
            {{ session('error') }}
        </div>
    @endif

    <form id="paymentForm" action="{{ route('payment.process') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="button" onclick="document.getElementById('paymentForm').submit();" class="btn">
            Pay Now with PayFast
        </button>
    </form>
</div>
@endsection