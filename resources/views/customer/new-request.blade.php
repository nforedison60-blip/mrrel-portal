@extends('layouts.app')

@section('content')
@php
    $flexOptions = ['Exact date', 'Flexible by a few days', 'Flexible by a week or more'];
    $sizeOptions = ['Small items', 'Medium item', 'Large item', 'Extra large or special item', 'A few items', 'Studio or 1-bedroom', '2-bedroom home', '3-bedroom home', 'House', 'Not sure'];
    $accessOptions = ['Ground floor', 'Stairs', 'Lift', 'Not sure'];
@endphp

    <h1>New move or service request</h1>
    <div class="m">Providers in your area see this and send you quotes.</div>

    @if($errors->any())
        <div style="background:#FDF1CC; color:#5C4300; padding:12px 18px; border-radius:12px; margin:16px 0; border:1px solid #F4B731;">
            <b>Please check these details:</b>
            <ul style="margin:6px 0 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sp">
        <div class="card">
            <h3>Request details</h3>

            <form method="POST" action="{{ route('customer.requests.store') }}">
                @csrf
                <div class="f">
                    <label class="k">Services needed (Packing, Movers, Cleaning, Storage, Assembly, Handyman, Painting, Gardening, Other)
                        <input type="text" name="services_needed" value="{{ old('services_needed') }}" required>
                    </label>
                    <label>Moving from or service address (city and suburb)
                        <input type="text" name="moving_from" value="{{ old('moving_from') }}" required>
                    </label>
                    <label>Moving to (if moving)
                        <input type="text" name="moving_to" value="{{ old('moving_to') }}">
                    </label>
                    <label>Move or service date
                        <input type="date" name="move_date" value="{{ old('move_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                    </label>
                    <label>Date flexibility
                        <select name="date_flexibility">
                            @foreach($flexOptions as $option)
                                <option value="{{ $option }}" @selected(old('date_flexibility') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>What are you moving?
                        <select name="item_size">
                            @foreach($sizeOptions as $option)
                                <option value="{{ $option }}" @selected(old('item_size') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>How many items or bags?
                        <input type="number" name="items_count" min="1" value="{{ old('items_count') }}">
                    </label>
                    <label>Budget in mind (optional)
                        <input type="text" name="budget" value="{{ old('budget') }}">
                    </label>
                    <label>Access at pickup
                        <select name="access_pickup">
                            @foreach($accessOptions as $option)
                                <option value="{{ $option }}" @selected(old('access_pickup') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Access at delivery
                        <select name="access_delivery">
                            @foreach($accessOptions as $option)
                                <option value="{{ $option }}" @selected(old('access_delivery') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="k">Details (large items, packing, anything else)
                        <input type="text" name="details" value="{{ old('details') }}">
                    </label>
                </div>
                <button type="submit" class="btn">Send to providers</button>
                <button type="submit" name="is_draft" value="1" class="btn g">Save draft</button>
                <p class="m sm" style="margin-top:10px;">A draft is saved but not shown to providers until you send it from My requests.</p>
            </form>
        </div>

        <div class="card">
            <h3>What happens next</h3>
            <ul>
                <li>Matching providers see your request</li>
                <li>They send you quotes, which appear under Quotes</li>
                <li>You compare them and accept the one you like</li>
                <li>You pay securely online to confirm your booking</li>
            </ul>
        </div>
    </div>
@endsection
