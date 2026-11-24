@extends('layouts.provider')

@section('content')
    <h1>Send a Quote</h1>
    <div class="m" style="margin-bottom: 20px;">Request #{{ $serviceRequest->id }} · {{ $serviceRequest->services_needed }}</div>

    <div class="sp">
        <div>
            <form method="POST" action="{{ route('provider.send.store') }}">
                @csrf
                <input type="hidden" name="service_request_id" value="{{ $serviceRequest->id }}">
                
                <div class="card">
                    <h3>Quote Details</h3>
                    <div class="f">
                        <!-- THE ONLY PRICE FIELD -->
                        <label class="k" style="font-size: 16px; font-weight: bold; color: var(--n);">Your Total Quote Price (R)
                            <input type="number" name="total_price" step="0.01" required placeholder="e.g. 1500.00" style="font-size: 18px; padding: 12px; margin-top: 8px; border: 2px solid var(--a);">
                        </label>
                    </div>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>What's Included?</h3>
                    <div class="pl">
                        <label><input type="checkbox" name="included[]" value="Fuel"> Fuel</label>
                        <label><input type="checkbox" name="included[]" value="Loading"> Loading</label>
                        <label><input type="checkbox" name="included[]" value="Unloading"> Unloading</label>
                        <label><input type="checkbox" name="included[]" value="Blankets"> Blankets</label>
                    </div>
                    
                    <h3 style="margin-top: 16px;">What's Not Included?</h3>
                    <div class="pl">
                        <label><input type="checkbox" name="not_included[]" value="Extra stairs"> Extra stairs</label>
                        <label><input type="checkbox" name="not_included[]" value="Assembly"> Assembly</label>
                        <label><input type="checkbox" name="not_included[]" value="Packing"> Packing</label>
                    </div>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Validity & Note</h3>
                    <div class="f">
                        <label class="k">Valid Until
                            <input type="date" name="validity" required>
                        </label>
                        <label class="k">Note to Customer (Optional)
                            <textarea name="note" rows="3" placeholder="Any extra details..."></textarea>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn" style="font-size: 16px; padding: 15px; width: 100%;">Send Quote to Customer</button>
                    <a href="{{ route('provider.requests') }}" class="btn g" style="text-decoration:none; display: block; text-align: center; margin-top: 10px;">Cancel</a>
                </div>
            </form>
        </div>

        <div>
            <div class="card st">
                <h3>Request Details</h3>
                <p class="sm"><b>Services:</b> {{ $serviceRequest->services_needed }}</p>
                <p class="sm"><b>From:</b> {{ $serviceRequest->moving_from }}</p>
                <p class="sm"><b>To:</b> {{ $serviceRequest->moving_to }}</p>
                <p class="sm"><b>Date:</b> {{ $serviceRequest->move_date }}</p>
                <p class="sm"><b>Size:</b> {{ $serviceRequest->item_size }}</p>
                @php $photoUrls = $serviceRequest->photoUrls(); @endphp
                @if($photoUrls)
                    <p class="sm"><b>Photos from the customer:</b></p>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        @foreach($photoUrls as $photoUrl)
                            <a href="{{ $photoUrl }}" target="_blank" rel="noopener"><img src="{{ $photoUrl }}" alt="Customer photo of items" loading="lazy" style="width:88px; height:88px; object-fit:cover; border-radius:8px; border:1px solid #D5DEE6;"></a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection