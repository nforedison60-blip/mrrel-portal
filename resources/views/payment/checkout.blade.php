@extends('layouts.app')

@section('content')
<h1>Checkout</h1>
<div class="card" style="max-width: 500px; margin-top: 20px;">
    <h3>Job Payment</h3>
    <p class="m">You are about to pay for your most recent MrRel booking. The exact amount will be shown on the payment screen.</p>
    
    <!-- Show error if there is no booking -->
    @if(session('error'))
        <div style="background:#FDF1CC; color:#5C4300; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #F4B731; margin-top: 16px;">
            {{ session('error') }}
        </div>
    @endif

    <form id="paymentForm" action="{{ route('payment.process') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <button type="button" id="payBtn" onclick="startPayment()" class="btn" style="position: relative; transition: all 0.3s ease;">
            <span id="btnText">Pay Now</span>
            <span id="btnSpinner" style="display: none; margin-left: 8px;">
                <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;">
                    <line x1="12" y1="2" x2="12" y2="6"></line>
                    <line x1="12" y1="18" x2="12" y2="22"></line>
                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                    <line x1="2" y1="12" x2="6" y2="12"></line>
                    <line x1="18" y1="12" x2="22" y2="12"></line>
                    <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                    <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                </svg>
            </span>
        </button>
    </form>
</div>

<style>
    /* Hover effect for the button */
    #payBtn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(244, 183, 49, 0.5);
    }
    #payBtn:active {
        transform: translateY(0);
        box-shadow: 0 4px 10px rgba(244, 183, 49, 0.3);
    }
    /* Spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    function startPayment() {
        const btn = document.getElementById('payBtn');
        const text = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');

        // Disable button to prevent double clicks
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.8';
        
        // Change text and show spinner
        text.innerText = 'Processing...';
        spinner.style.display = 'inline-block';

        // Submit the form after a tiny delay so the user sees the animation
        setTimeout(function() {
            document.getElementById('paymentForm').submit();
        }, 500);
    }
</script>
@endsection