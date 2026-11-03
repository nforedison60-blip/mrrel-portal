@extends('layouts.provider')

@section('content')
    <div class="m sm">Jobs / New request / Send a quote</div>
    <h1>Send a quote</h1>
    <div class="m">Request REQ-0042 · Moving · Parklands to Bellville</div>

    <form method="POST" action="{{ route('provider.send.store') }}">
        @csrf
        <div class="sp" style="margin-top: 20px;">
            <div>
                <!-- 1. The Job -->
                <div class="card">
                    <h3>1. The job</h3>
                    <p class="m sm">From the customer’s request. You can’t change this part.</p>
                    <div class="g2" style="margin:8px 0 0">
                        <div>From: Parklands, Cape Town</div>
                        <div>To: Bellville, Cape Town</div>
                        <div>Date: Sat 31 Oct 2026</div>
                        <div>Distance: 18 km (calculated)</div>
                    </div>
                </div>

                <!-- 2. Your Price -->
                <div class="card" style="margin-top: 16px;">
                    <h3>2. Your price</h3>
                    <div class="pl">
                        <label><input type="radio" name="price_type" value="Fixed price" checked> Fixed price</label>
                        <label><input type="radio" name="price_type" value="Maximum price"> Maximum price</label>
                    </div>
                    <div class="pr">
                        <span>Total Price (R)</span>
                        <input type="number" name="total_price" value="600" style="margin:0" required>
                    </div>
                </div>

                <!-- 3. Included / Not Included -->
                <div class="card" style="margin-top: 16px;">
                    <h3>3. Included and not included</h3>
                    
                    <b class="sm">Included</b>
                    <div class="pl">
                        <label><input type="checkbox" name="included[]" value="Fuel" checked> Fuel</label>
                        <label><input type="checkbox" name="included[]" value="Loading" checked> Loading</label>
                        <label><input type="checkbox" name="included[]" value="Unloading" checked> Unloading</label>
                        <label><input type="checkbox" name="included[]" value="Blankets" checked> Blankets and straps</label>
                    </div>

                    <b class="sm">Not included</b>
                    <div class="pl">
                        <label><input type="checkbox" name="not_included[]" value="Extra stairs" checked> Extra stairs</label>
                        <label><input type="checkbox" name="not_included[]" value="Assembly" checked> Furniture assembly</label>
                        <label><input type="checkbox" name="not_included[]" value="Packing" checked> Packing</label>
                    </div>
                </div>

                <!-- 4. Validity -->
                <div class="card" style="margin-top: 16px;">
                    <h3>4. Cover, validity and note</h3>
                    <div class="f">
                        <label>Quote valid for
                            <select name="validity">
                                <option>48 hours</option>
                                <option>24 hours</option>
                                <option>72 hours</option>
                            </select>
                        </label>
                        <span></span>
                        <label class="k">Short note to the customer
                            <textarea name="note" rows="3" maxlength="300"></textarea>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn">Send quote to customer</button>
                </div>
            </div>

            <!-- Right Sidebar Preview -->
            <div class="st">
                <div class="card" style="border:2px solid var(--t);">
                    <h3>What the customer will see</h3>
                    <div class="row" style="margin:10px 0">
                        <span class="av">P</span>
                        <div>
                            <b>Pieter</b> <span class="chip ok">Verified</span><br>
                            <span class="m sm">Sample Movers B · 1-ton bakkie</span>
                        </div>
                    </div>
                    <div class="big" style="font-size:34px">R600.00</div>
                    <span class="chip">Fixed price</span>
                </div>
            </div>
        </div>
    </form>
@endsection