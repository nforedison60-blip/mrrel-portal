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
                            <div class="m sm">Sample Movers · Bakkie</div>
                            <div class="m sm">4.8 rating</div>
                        </div>
                    </div>
                    <div class="big" style="margin-top: 16px;">R{{ number_format($quote->total_price, 2) }}</div>
                    <span class="chip">{{ $quote->price_type }}</span>
                    
                    <p class="sm" style="margin-top: 12px;">
                        <b>Included:</b> {{ $quote->included }}<br>
                        <b>Not included:</b> {{ $quote->not_included }}<br>
                        @if($quote->note)
                            <b>Note:</b> {{ $quote->note }}<br>
                        @endif
                        <b>Valid for:</b> {{ $quote->validity }}
                    </p>
                    
                    <button type="button" class="btn">Accept</button>
                    <button type="button" class="btn g">Ask for changes</button>
                </div>
            @endforeach
        </div>
    @endif
    
    <p class="m sm" style="margin-top: 20px;">Quotes expire after their validity. Accepting is online: no signature, and we record the date and time.</p>
@endsection