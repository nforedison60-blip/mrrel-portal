@extends('layouts.provider')

@section('content')
    <div class="m sm">Jobs / New request / Send a quote</div>
    <h1>Send a quote</h1>
    <div class="m">Request REQ-0042 · Moving · Parklands to Bellville · Example data</div>

    <form method="POST" action="{{ route('provider.send.store') }}">
        @csrf
        <input type="hidden" name="service_request_id" value="{{ $serviceRequest->id }}">
        
        <div class="sp" style="margin-top: 20px;">
            
            <!-- LEFT COLUMN -->
            <div>
                <!-- 1. The Job -->
                <div class="card">
                    <h3>1. The job</h3>
                    <p class="m sm">From the customer’s request. You can’t change this part.</p>
                    <div class="g2" style="margin:8px 0 0">
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">From:</span> {{ $serviceRequest->moving_from }}</div>
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">To:</span> {{ $serviceRequest->moving_to }}</div>
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">Date:</span> {{ $serviceRequest->move_date }}</div>
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">Distance:</span> 18 km (calculated)</div>
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">Items:</span> 1 double bed frame, 1 double mattress, 1 bedside table, 3 boxes</div>
                        <div style="background: #F5F8FA; padding: 8px 12px; border-radius: 8px;"><span class="m sm">Access:</span> Ground floor, no stairs. Delivery 2nd floor, lift. Parking within 20 m.</div>
                    </div>
                </div>

                <!-- 2. Your Price -->
                <div class="card" style="margin-top: 16px;">
                    <h3>2. Your price</h3>
                    <p class="m sm">Filled from your rate card. You can adjust the amounts.</p>
                    
                    <div class="pl">
                        <label><input type="radio" name="price_type" value="Fixed price" checked> Fixed price</label>
                        <label><input type="radio" name="price_type" value="Maximum price"> Maximum price</label>
                    </div>

                    <div class="pr">
                        <span>Base trip (bakkie and driver)</span>
                        <input type="number" name="base_trip" value="350" style="margin:0">
                    </div>
                    <div class="pr">
                        <span>Distance (18 km)</span>
                        <input type="number" name="distance_fee" value="150" style="margin:0">
                    </div>
                    <div class="pr">
                        <span>Extra mover (1)</span>
                        <input type="number" name="extra_mover" value="100" style="margin:0">
                    </div>
                    <div class="pr">
                        <span>Stairs surcharge</span>
                        <input type="number" name="stairs_surcharge" value="0" style="margin:0">
                    </div>
                    <div class="pr">
                        <span>Other charge (add a short description)</span>
                        <input type="number" name="other_charge" value="0" style="margin:0">
                    </div>
                    <div class="pr" style="border:0;font-weight:700;font-size:18px">
                        <span>Total (your minimum charge R400 applies)</span>
                        <span style="text-align:right">R600.00</span>
                    </div>
                </div>

                <!-- 3. Crew & Vehicle -->
                <div class="card" style="margin-top: 16px;">
                    <h3>3. Crew, vehicle and time</h3>
                    <div class="f">
                        <label>Vehicle (from your profile)
                            <select><option>1-ton bakkie · CA 000 000</option></select>
                        </label>
                        <label>Crew
                            <select>
                                <option>Driver and 1 mover</option>
                                <option>Driver only</option>
                                <option>Driver and 2 movers</option>
                            </select>
                        </label>
                        <label>Arrival window
                            <select>
                                <option>08:00 to 10:00</option>
                                <option>10:00 to 12:00</option>
                                <option>12:00 to 14:00</option>
                            </select>
                        </label>
                        <label>Estimated duration
                            <select>
                                <option>1 to 2 hours</option>
                                <option>2 to 4 hours</option>
                                <option>Half a day</option>
                            </select>
                        </label>
                    </div>
                </div>

                <!-- 4. Included / Not Included -->
                <div class="card" style="margin-top: 16px;">
                    <h3>4. Included and not included</h3>
                    <p class="m sm">Pick from the standard list, so every quote reads the same.</p>
                    
                    <b class="sm">Included</b>
                    <div class="pl">
                        <label><input type="checkbox" name="included[]" value="Fuel" checked> Fuel</label>
                        <label><input type="checkbox" name="included[]" value="Loading" checked> Loading</label>
                        <label><input type="checkbox" name="included[]" value="Unloading" checked> Unloading</label>
                        <label><input type="checkbox" name="included[]" value="Blankets and straps" checked> Blankets and straps</label>
                        <label><input type="checkbox" name="included[]" value="Dismantling and reassembly"> Dismantling and reassembly</label>
                        <label><input type="checkbox" name="included[]" value="Packing materials"> Packing materials</label>
                        <label><input type="checkbox" name="included[]" value="15 minutes waiting" checked> 15 minutes’ waiting time</label>
                    </div>

                    <b class="sm">Not included</b>
                    <div class="pl">
                        <label><input type="checkbox" name="not_included[]" value="Extra stairs" checked> Extra stairs</label>
                        <label><input type="checkbox" name="not_included[]" value="Furniture assembly" checked> Furniture assembly</label>
                        <label><input type="checkbox" name="not_included[]" value="Packing" checked> Packing</label>
                        <label><input type="checkbox" name="not_included[]" value="Disposal of items"> Disposal of items</label>
                    </div>
                </div>

                <!-- 5. Validity -->
                <div class="card" style="margin-top: 16px;">
                    <h3>5. Cover, validity and note</h3>
                    <div class="f">
                        <label>Quote valid for
                            <select name="validity">
                                <option>48 hours</option>
                                <option>24 hours</option>
                                <option>72 hours</option>
                            </select>
                        </label>
                        <span></span>
                        <label class="k">Short note to the customer (optional, 300 characters)
                            <textarea name="note" rows="3" maxlength="300"></textarea>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn">Send quote to customer</button>
                    <button type="button" class="btn g">Save draft</button>
                </div>
            </div>

            <!-- RIGHT SIDEBAR PREVIEW -->
            <div class="st">
                <div class="card" style="border:2px solid var(--t);">
                    <div class="m sm" style="margin-bottom: 12px;">What the customer will see</div>
                    
                    <div class="row" style="margin-bottom: 12px;">
                        <span class="av" style="width: 40px; height: 40px; font-size: 16px;">P</span>
                        <div>
                            <b>Pieter</b> <span class="chip ok">Verified</span><br>
                            <span class="m sm">Sample Movers B · 1-ton bakkie</span>
                        </div>
                    </div>

                    <div class="big" style="font-size:34px">R600.00</div>
                    <span class="chip">Fixed price</span>
                    
                    <p class="sm" style="margin-top: 12px;">
                        <b>Included:</b> fuel, loading, unloading, blankets and straps, 15 minutes’ waiting<br>
                        <b>Not included:</b> extra stairs, assembly, packing<br>
                        <b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00<br>
                        <b>Valid for:</b> 48 hours
                    </p>
                </div>
            </div>
        </div>
    </form>
@endsection