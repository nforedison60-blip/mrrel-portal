@extends('layouts.app')

@section('content')
    <div class="m sm">My requests / Moving / Quote</div>
    <h1>Moving quote</h1>
    <div class="m" style="display: flex; justify-content: space-between; align-items: center;">
        <span>QTN-{{ str_pad($quote->id, 4, '0', STR_PAD_LEFT) }} · Rev 01 · Issued {{ $quote->created_at->format('d M Y') }}</span>
        <span class="chip wn">Awaiting your acceptance</span>
    </div>

    <div class="sp" style="margin-top: 20px;">
        <!-- LEFT COLUMN -->
        <div>
            <!-- Provider Card -->
            <div class="card">
                <h3>Your provider</h3>
                <div class="row" style="align-items: flex-start; margin-top: 12px;">
                    <div style="text-align: center;">
                        <div class="av" style="width: 80px; height: 80px; font-size: 24px; border-radius: 50%;">P</div>
                    </div>
                    <div style="flex: 1; margin-left: 16px;">
                        <b>Pieter</b> <span class="chip ok">Verified</span>
                        <div class="m sm">Sample Movers B</div>
                        <div class="m sm">Registration: [number] · 4.8 rating (120 jobs)</div>
                        <div class="m sm">Contact details are shared once you book. Photo is for identification only.</div>
                    </div>
                     <div style="text-align: center;">
                        <div style="width: 100px; height: 60px; background-color: #E3F1F2; border: 1px solid var(--l); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="60" height="30" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                                <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                                <circle cx="20" cy="32" r="5" fill="#51657A"/>
                                <circle cx="60" cy="32" r="5" fill="#51657A"/>
                            </svg>
                        </div>
                        <div class="m sm" style="margin-top: 4px;">1-ton bakkie<br>CA 000 000</div>
                    </div>
                </div>
                <p class="m sm" style="margin-top: 12px;">This service is provided by Sample Movers B. MrRel provides the platform that connects you.</p>
            </div>

            <!-- The Job -->
            <div class="card" style="margin-top: 16px;">
                <h3>The job</h3>
                <div class="g2" style="margin:8px 0 0">
                    <div><span class="m">From:</span> Parklands, Cape Town</div>
                    <div><span class="m">To:</span> Bellville, Cape Town</div>
                    <div><span class="m">Date:</span> Sat 31 Oct 2026</div>
                    <div><span class="m">Distance:</span> 18 km</div>
                    <div><span class="m">Arrival window:</span> 08:00 to 10:00</div>
                    <div><span class="m">Estimated duration:</span> 1 to 2 hours</div>
                </div>
                <p class="m sm" style="margin-top: 12px;">The window is when the crew arrives, not when the move will be finished.</p>
            </div>

            <!-- What is being moved -->
            <div class="card" style="margin-top: 16px;">
                <h3>What is being moved</h3>
                <table class="tb" style="margin-top: 8px;">
                    <tr>
                        <th>Item</th>
                        <th>Notes</th>
                        <th>Qty</th>
                    </tr>
                    <tr>
                        <td>Double bed frame</td>
                        <td>Already dismantled</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Double mattress</td>
                        <td>Protective cover needed</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Bedside table</td>
                        <td>No special handling</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Boxes</td>
                        <td>Packed by you</td>
                        <td>3</td>
                    </tr>
                </table>
            </div>

            <!-- Access and Crew -->
            <div class="card" style="margin-top: 16px;">
                <h3>Access and crew</h3>
                <div class="g2" style="margin:8px 0 0">
                    <div><span class="m">Collection:</span> Ground floor, no stairs</div>
                    <div><span class="m">Delivery:</span> 2nd floor, lift available</div>
                    <div><span class="m">Parking:</span> Bakkie within 20 m</div>
                    <div><span class="m">Crew:</span> Driver and 1 mover</div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="card" style="margin-top: 16px;">
                <h3>Price</h3>
                <table class="tb" style="margin-top: 8px;">
                    <tr>
                        <th>Item</th>
                        <th>Details</th>
                        <th>Price</th>
                    </tr>
                    <tr>
                        <td>Base trip</td>
                        <td>Bakkie and driver</td>
                        <td>R350.00</td>
                    </tr>
                    <tr>
                        <td>Distance</td>
                        <td>18 km</td>
                        <td>R150.00</td>
                    </tr>
                    <tr>
                        <td>Extra mover</td>
                        <td>One person to load and unload</td>
                        <td>R100.00</td>
                    </tr>
                    <tr>
                        <td>Protection</td>
                        <td>Blankets and straps</td>
                        <td>Included</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td><b>Fixed price</b></td>
                        <td><b>R{{ number_format($quote->total_price, 2) }}</b></td>
                    </tr>
                </table>
                <p class="m sm" style="margin-top: 12px;">This price applies to the items, route and access shown above. Extra items, unexpected stairs or waiting time need a revised quote that you approve first.</p>
                <p class="m sm">VAT: [included or not applicable, depending on the provider]</p>
            </div>

            <!-- Terms -->
            <div class="card" style="margin-top: 16px;">
                <h3>Terms</h3>
                <div class="g2" style="margin:8px 0 0">
                    <div>
                        <b class="sm">Included</b>
                        <p class="sm m">Driver and bakkie, fuel, one extra mover, loading and unloading, blankets and straps</p>
                    </div>
                    <div>
                        <b class="sm">Not included</b>
                        <p class="sm m">Extra stairs, furniture assembly, packing</p>
                    </div>
                    <div>
                        <b class="sm">Goods-in-transit cover</b>
                        <p class="sm m">[Provider states: included, not included, or details]</p>
                    </div>
                    <div>
                        <b class="sm">Cancellation</b>
                        <p class="sm m">Free up to 24 hours before. After that: [refund rule]</p>
                    </div>
                    <div>
                        <b class="sm">When the job is done</b>
                        <p class="sm m">The provider marks the job complete with evidence. You confirm it or report a specific issue. If you don't respond within [set time], it counts as complete.</p>
                    </div>
                    <div>
                        <b class="sm">Damage or missing items</b>
                        <p class="sm m">Report them in your portal. Your legal rights are not affected.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR -->
        <div class="st">
            <div class="card" style="border:2px solid var(--t);">
                <div class="m sm">Total</div>
                <div class="big" style="font-size:40px">R{{ number_format($quote->total_price, 2) }}</div>
                <span class="chip">Fixed price</span>
                
                <div class="m sm" style="margin-top: 16px;">
                    <b>Pay now: R{{ number_format($quote->total_price, 2) }}</b><br>
                    Pay securely online. The provider is paid after the job is confirmed, under MrRel's completion process. Larger moves may use a deposit and a balance.
                </div>

                <form method="POST" action="{{ route('customer.bookings.store') }}" style="margin-top: 16px;">
                    @csrf
                    <input type="hidden" name="quote_id" value="{{ $quote->id }}">
                    <input type="hidden" name="total_price" value="{{ $quote->total_price }}">
                    <button type="submit" class="btn" style="width:100%;">Accept and pay</button>
                </form>
                
                <button type="button" class="btn g" style="width:100%; margin-top: 8px;">Ask for changes</button>
                
                <div style="text-align: center; margin-top: 12px;">
                    <a href="#" class="m sm" style="text-decoration: underline;">Download PDF</a>
                </div>

                <p class="m sm" style="margin-top: 16px;">No signature needed. We record your acceptance with the date and time. A booking number is created once you have paid. This quote is valid for {{ $quote->validity }}.</p>
            </div>
        </div>
    </div>
@endsection