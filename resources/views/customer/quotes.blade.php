@extends('layouts.app')

@section('content')
    <h1>Compare your quotes</h1>
    <div class="m">REQ-0042 · 1 bed, Parklands to Bellville · Sat 31 Oct. Every quote uses the same MrRel layout.</div>

    <h2 style="margin-top: 30px;">Your Quotes</h2>
    
    @if($quotes->isEmpty())
        <div class="card" style="margin-top: 16px;">
            <p class="m">No quotes yet. Providers will send them soon.</p>
        </div>
    @else
        <div class="g3">
            @foreach($quotes as $quote)
                <div class="card">
                    <div class="row">
                        <span class="av">{{ substr($quote->provider_name, 0, 1) }}</span>
                        <div>
                            <b>{{ $quote->provider_name }}</b> <span class="chip ok">Verified</span>
                            <div class="m sm">Sample Movers · Bakkie and driver</div>
                            <div class="m sm">4.8 rating</div>
                        </div>
                    </div>

                    <div style="width: 100%; height: 120px; background-color: #E3F1F2; border-radius: 8px; margin: 16px 0; display: flex; align-items: center; justify-content: center; border: 1px solid var(--l);">
                        <svg width="80" height="40" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                            <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                            <circle cx="20" cy="32" r="5" fill="#51657A"/>
                            <circle cx="60" cy="32" r="5" fill="#51657A"/>
                        </svg>
                    </div>

                    <div class="big" style="margin-top: 8px;">R{{ number_format($quote->total_price, 2) }}</div>
                    <span class="chip">{{ $quote->price_type }}</span>
                    
                    <p class="sm" style="margin-top: 12px;">
                        <b>Included:</b> {{ $quote->included }}<br>
                        <b>Not included:</b> {{ $quote->not_included }}<br>
                        @if($quote->note)
                            <b>Note:</b> {{ $quote->note }}<br>
                        @endif
                        <b>Valid for:</b> {{ $quote->validity }}
                    </p>
                    
                    <!-- NEW: View Full Quote Button -->
                    <a href="{{ route('customer.quote.show', $quote->id) }}" class="btn g" style="width:100%; display:block; text-align:center; text-decoration:none; margin-bottom: 8px;">View Full Quote</a>

                    <form method="POST" action="{{ route('customer.bookings.store') }}" style="margin-bottom: 8px;">
                        @csrf
                        <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                        <input type="hidden" name="total_price" value="{{ $quote->total_price }}">
                        <button type="submit" class="btn" style="width:100%;">Accept</button>
                    </form>
                    
                    <button type="button" class="btn g" style="width:100%; margin-bottom: 8px;">Ask for changes</button>
                    <a href="{{ route('customer.messages') }}" class="btn g" style="width:100%; display:block; text-align:center; text-decoration:none;">Message provider</a>
                </div>
            @endforeach
        </div>
    @endif
@endsection