@extends('layouts.app')

@section('content')
<h1>Checkout</h1>
<div class="card" style="max-width: 500px; margin-top: 20px;">
    <h3>Job Payment</h3>
    <p class="m">You are about to pay R150.00 for your MrRel service.</p>
    
    <form action="{{ route('payment.process') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="submit" class="btn">Pay Now with PayFast</button>
    </form>
</div>
@endsection