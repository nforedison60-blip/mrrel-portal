@extends('layouts.app')

@section('content')
    <h1>New move or service request</h1>
    <div class="m">Providers in your area see this and send quotes. Small jobs get instant prices.</div>

    <div class="sp">
        <div class="card">
            <h3>Request details</h3>
            
            <!-- SUBMIT FORM -->
            <form method="POST" action="{{ route('customer.requests.store') }}">
                @csrf
                <div class="f">
                    <label class="k">Services needed (Packing, Movers, Cleaning, Storage, Assembly, Handyman, Painting, Gardening, Other)
                        <input type="text" name="services_needed" required>
                    </label>
                    <label>Moving from or service address (city and suburb)
                        <input type="text" name="moving_from" required>
                    </label>
                    <label>Moving to (if moving)
                        <input type="text" name="moving_to">
                    </label>
                    <label>Move or service date
                        <input type="date" name="move_date" required>
                    </label>
                    <label>Date flexibility
                        <select name="date_flexibility">
                            <option>Exact date</option>
                            <option>Flexible by a few days</option>
                            <option>Flexible by a week or more</option>
                        </select>
                    </label>
                    <label>What are you moving?
                        <select name="item_size">
                            <option>Small items</option>
                            <option>Medium item</option>
                            <option>Large item</option>
                            <option>Extra large or special item</option>
                            <option>A few items</option>
                            <option>Studio or 1-bedroom</option>
                            <option>2-bedroom home</option>
                            <option>3-bedroom home</option>
                            <option>House</option>
                            <option>Not sure</option>
                        </select>
                    </label>
                    <label>How many items or bags?
                        <input type="number" name="items_count">
                    </label>
                    <label>Budget in mind (optional)
                        <input type="text" name="budget">
                    </label>
                    <label>Access at pickup
                        <select name="access_pickup">
                            <option>Ground floor</option>
                            <option>Stairs</option>
                            <option>Lift</option>
                            <option>Not sure</option>
                        </select>
                    </label>
                    <label>Access at delivery
                        <select name="access_delivery">
                            <option>Ground floor</option>
                            <option>Stairs</option>
                            <option>Lift</option>
                            <option>Not sure</option>
                        </select>
                    </label>
                    <label class="k">Details (large items, packing, anything else)
                        <input type="text" name="details">
                    </label>
                </div>
                <button type="submit" class="btn">Submit request</button>
                <button type="button" class="btn g" onclick="document.getElementById('draftForm').submit()">Save draft</button>
            </form>

            <!-- HIDDEN DRAFT FORM -->
            <form id="draftForm" method="POST" action="{{ route('customer.requests.draft') }}" style="display:none;">
                @csrf
                <input type="hidden" name="is_draft" value="1">
            </form>

        </div>

        <div class="card">
            <h3>What happens next</h3>
            <ul>
                <li>Matching providers see your request</li>
                <li>Small jobs: instant prices from provider rate cards</li>
                <li>Other jobs: providers send quotes in one standard layout</li>
                <li>You compare and accept online</li>
            </ul>
        </div>
    </div>
@endsection