@extends('layouts.app')

@section('content')
    <h1>Compare your quotes</h1>
    <div class="m" style="margin-bottom: 20px;">Review the quotes providers have sent you.</div>

    <h3>Your Quotes</h3>
    
    @forelse($quotes as $quote)
        <div class="card" style="margin-top: 16px;">
            <div class="row" style="justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="row" style="margin-bottom: 8px;">
                        <div class="av">{{ substr($quote->provider_name, 0, 1) }}</div>
                        <div>
                            <b>{{ $quote->provider_name }}</b>
                            <div class="m sm">Provider</div>
                        </div>
                    </div>
                    <div class="big">R{{ number_format($quote->total_price, 2) }}</div>
                    <div class="m sm">{{ $quote->price_type }} price</div>
                </div>
                <div style="text-align: right;">
                    <span class="chip ok">Valid until {{ $quote->validity }}</span>
                </div>
            </div>
            
            <div class="g2" style="margin-top: 16px; margin-bottom: 16px;">
                <div>
                    <div class="m sm"><b>Included:</b> {{ $quote->included ?: 'None specified' }}</div>
                </div>
                <div>
                    <div class="m sm"><b>Not included:</b> {{ $quote->not_included ?: 'None specified' }}</div>
                </div>
            </div>

            @if($quote->note)
                <div class="m sm" style="background: var(--bg); padding: 12px; border-radius: 8px; border: 1px solid var(--l);">
                    <b>Provider note:</b> "{{ $quote->note }}"
                </div>
            @endif

            <div style="margin-top: 16px; display: flex; gap: 8px;">
                <form method="POST" action="{{ route('customer.bookings.store') }}">
                    @csrf
                    <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                    <input type="hidden" name="total_price" value="{{ $quote->total_price }}">
                    <button type="submit" class="btn">Accept Quote & Book</button>
                </form>
                <button type="button" class="btn g">Decline</button>
            </div>
        </div>
    @empty
        <div class="card" style="margin-top: 20px; text-align: center; padding: 40px;">
            <h3>No quotes yet</h3>
            <p class="m">Providers will send them soon.</p>
        </div>
    @endforelse

@endsection