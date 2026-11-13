@extends('layouts.provider')

@section('content')
    <h1>Customer Reviews</h1>
    <div class="m" style="margin-bottom: 20px;">See what customers are saying about your service.</div>

    @forelse($reviews as $review)
        <div class="card" style="margin-bottom: 16px;">
            <div class="row" style="justify-content: space-between; margin-bottom: 12px;">
                <div class="row">
                    <div class="av">{{ substr($review->customer_name, 0, 1) }}</div>
                    <div>
                        <b>{{ $review->customer_name }}</b>
                        <div class="m sm">{{ $review->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                <div>
                    <span class="chip ok">{{ $review->rating }} / 5 Stars</span>
                </div>
            </div>
            <p class="sm">"{{ $review->comment }}"</p>
        </div>
    @empty
        <div class="card">
            <p class="m">You don't have any reviews yet. Once you complete a job, customers can leave feedback here.</p>
        </div>
    @endforelse
@endsection