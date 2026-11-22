@extends('layouts.app')

@section('content')
<h1>Checkout</h1>

@if(!$booking)
    <div class="card" style="max-width: 500px; margin-top: 20px;">
        <h3>Nothing to pay</h3>
        <p class="m">You have no unpaid bookings. When you accept a quote, you can pay for it here.</p>
        <a href="{{ route('customer.quotes') }}" class="btn" style="text-decoration:none;">View quotes</a>
    </div>
@elseif($booking->status === 'Paid')
    <div class="card" style="max-width: 500px; margin-top: 20px;">
        <h3>Already paid</h3>
        <p class="m">Booking BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }} has been paid. Thank you.</p>
        <a href="{{ route('customer.bookings', ['booking' => $booking->id]) }}" class="btn" style="text-decoration:none;">View booking</a>
    </div>
@else
    <div class="card" style="max-width: 500px; margin-top: 20px;">
        <h3>Pay for your booking</h3>
        <div class="m sm" style="margin-top: 8px;">BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }} · {{ optional($booking->quote)->provider_name ?: 'Your provider' }}</div>
        <div class="big" style="margin: 8px 0;">R{{ number_format($booking->total_price, 2) }}</div>
        <p class="m sm">You will be taken to our secure payment partner to pay by instant EFT.</p>

        <form id="paymentForm" action="{{ route('payment.process') }}" method="POST" style="margin-top: 20px;">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            <button type="button" id="payBtn" onclick="startPayment()" class="btn" style="position: relative; transition: all 0.3s ease;">
                <span id="btnText">Pay R{{ number_format($booking->total_price, 2) }}</span>
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
        <p class="m sm" style="margin-top: 12px;"><a href="{{ route('customer.bookings', ['booking' => $booking->id]) }}" style="color:inherit;">Back to my booking</a></p>
    </div>

    <style>
        #payBtn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(244, 183, 49, 0.5); }
        #payBtn:active { transform: translateY(0); box-shadow: 0 4px 10px rgba(244, 183, 49, 0.3); }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <script>
        function startPayment() {
            const btn = document.getElementById('payBtn');
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.8';
            document.getElementById('btnText').innerText = 'Processing...';
            document.getElementById('btnSpinner').style.display = 'inline-block';
            setTimeout(function () { document.getElementById('paymentForm').submit(); }, 500);
        }
    </script>
@endif
@endsection
