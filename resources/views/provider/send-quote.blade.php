@extends('layouts.provider')

@section('content')
    <div class="m sm">Jobs / New request / Send a quote</div>
    <h1>Send a quote</h1>
    <div class="m">Request REQ-0042 · Moving · Parklands to Bellville</div>

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
                    <div>Items: 1 double bed frame (dismantled), 1 double mattress, 1 bedside table, 3 boxes</div>
                    <div>Access: Ground floor. Delivery 2nd floor, lift.</div>
                </div>
            </div>

            <!-- 2. Your Price -->
            <div class="card" style="margin-top: 16px;">
                <h3>2. Your price</h3>
                <p class="m sm">Filled from your rate card. You can adjust the amounts.</p>
                
                <div class="pl">
                    <label><input type="radio" name="pt" checked> Fixed price</label>
                    <label><input type="radio" name="pt"> Maximum price</label>
                </div>

                <div class="pr">
                    <span>Base trip (bakkie and driver)</span>
                    <input type="number" value="350" style="margin:0">
                </div>
                <div class="pr">
                    <span>Distance (18 km)</span>
                    <input type="number" value="150" style="margin:0">
                </div>
                <div class="pr">
                    <span>Extra mover (1)</span>
                    <input type="number" value="100" style="margin:0">
                </div>
                <div class="pr">
                    <span>Stairs surcharge</span>
                    <input type="number" value="0" style="margin:0">
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
                    <label><input type="checkbox" checked> Fuel</label>
                    <label><input type="checkbox" checked> Loading</label>
                    <label><input type="checkbox" checked> Unloading</label>
                    <label><input type="checkbox" checked> Blankets and straps</label>
                    <label><input type="checkbox"> Dismantling and reassembly</label>
                    <label><input type="checkbox"> Packing materials</label>
                    <label><input type="checkbox" checked> 15 minutes’ waiting time</label>
                </div>

                <b class="sm">Not included</b>
                <div class="pl">
                    <label><input type="checkbox" checked> Extra stairs</label>
                    <label><input type="checkbox" checked> Furniture assembly</label>
                    <label><input type="checkbox" checked> Packing</label>
                    <label><input type="checkbox"> Disposal of items</label>
                </div>
            </div>

            <!-- 5. Validity -->
            <div class="card" style="margin-top: 16px;">
                <h3>5. Cover, validity and note</h3>
                <div class="f">
                    <label>Quote valid for
                        <select>
                            <option>48 hours</option>
                            <option>24 hours</option>
                            <option>72 hours</option>
                        </select>
                    </label>
                    <span></span>
                    <label class="k">Short note to the customer (optional, 300 characters)
                        <textarea rows="3" maxlength="300"></textarea>
                    </label>
                </div>
                <p class="m sm">Your name, photo, vehicle and verified badge are added automatically.</p>
            </div>

            <div style="margin-top: 20px;">
                <button type="button" class="btn">Send quote to customer</button>
                <button type="button" class="btn g">Save draft</button>
            </div>
            <p class="m sm">By sending, you agree to keep this price for the items and conditions listed.</p>
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
                <p class="sm">
                    <b>Included:</b> fuel, loading, unloading, blankets and straps, 15 minutes’ waiting<br>
                    <b>Not included:</b> extra stairs, assembly, packing<br>
                    <b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00<br>
                    <b>Valid for:</b> 48 hours
                </p>
            </div>
        </div>
    </div>
@endsection