@extends('layouts.app')

@section('content')
    @if($booking)
        <h1>Booking BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</h1>
        <div class="m">Moving one bed · Sat 31 Oct · Parklands to Bellville</div>

        <div class="sp" style="margin-top: 20px;">
            <!-- LEFT COLUMN -->
            <div>
                <!-- Provider Card -->
                <div class="card">
                    <h3>Your provider</h3>
                    <div class="row" style="margin-top: 12px; align-items: flex-start;">
                        <div style="text-align: center;">
                            <div class="av" style="width: 120px; height: 120px; font-size: 40px; border-radius: 50%;">P</div>
                            <div class="m sm" style="margin-top: 8px;">The person who will arrive</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="width: 200px; height: 120px; background-color: #E3F1F2; border: 1px solid var(--l); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <svg width="100" height="50" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                                    <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                                    <circle cx="20" cy="32" r="5" fill="#51657A"/>
                                    <circle cx="60" cy="32" r="5" fill="#51657A"/>
                                </svg>
                            </div>
                            <div class="m sm" style="margin-top: 8px;">Your vehicle · CA 000 000</div>
                        </div>
                    </div>
                    
                    <div class="row" style="margin-top: 16px;">
                        <span class="av" style="width: 48px; height: 48px; font-size: 18px;">P</span>
                        <div>
                            <b>Pieter</b> <span class="chip ok">Verified</span>
                            <div class="m sm">Sample Movers B · 1-ton bakkie</div>
                            <div class="m sm">4.8 rating</div>
                        </div>
                    </div>
                    <p class="sm" style="margin-top: 12px;">
                        <b>Vehicle:</b> White 1-ton bakkie · <b>Number plate:</b> CA 000 000<br>
                        <b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00
                    </p>
                    <div class="m sm">If someone else arrives, don’t hand anything over and call us.</div>
                </div>

                <!-- Track your driver (REAL MAP) -->
                <div class="card" style="margin-top: 16px;">
                    <h3>Track your driver</h3>
                    
                    <div id="map" style="width: 100%; height: 250px; border-radius: 8px; margin-bottom: 16px; background: #E3F1F2;"></div>

                    <p class="sm"><b>Pieter is on his way.</b> Status: {{ $booking->tracking_status }}</p>
                    <div class="pl" style="margin-top: 8px;">
                        <span class="chip {{ $booking->tracking_status == 'On my way' ? 'ok' : '' }}">On my way</span>
                        <span class="chip {{ $booking->tracking_status == 'Arrived' ? 'ok' : '' }}">Arrived</span>
                        <span class="chip {{ $booking->tracking_status == 'Loading' ? 'ok' : '' }}">Loading</span>
                        <span class="chip {{ $booking->tracking_status == 'In transit' ? 'ok' : '' }}">In transit</span>
                        <span class="chip {{ $booking->tracking_status == 'Delivered' ? 'ok' : '' }}">Delivered</span>
                        <span class="chip {{ $booking->tracking_status == 'Complete' ? 'ok' : '' }}">Complete</span>
                    </div>
                    <p class="m sm" style="margin-top: 12px;">Location is shared only while the job is active, and stops when the job is complete.</p>
                </div>

                <!-- Chat with Pieter -->
                <div class="card" style="margin-top: 16px;">
                    <h3>Chat with Pieter</h3>
                    <div class="row" style="margin-bottom: 12px;">
                        <span class="av" style="width: 40px; height: 40px; font-size: 16px;">P</span>
                        <div>
                            <b>Pieter</b> <span class="chip ok">Verified</span>
                            <div class="m sm">Booking BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                        <div style="background: #F5F8FA; padding: 10px 16px; border-radius: 8px; border-bottom-left-radius: 0; align-self: flex-start; max-width: 80%; border: 1px solid var(--l);">
                            <div class="sm">Hi Lerato, I'm on my way. I should arrive around 08:25.</div>
                        </div>
                        <div style="background: #E3F1F2; padding: 10px 16px; border-radius: 8px; border-bottom-right-radius: 0; align-self: flex-end; max-width: 80%; border: 1px solid #D5DEE6;">
                            <div class="sm">Great, the lift is booked for 08:30.</div>
                        </div>
                         <div style="background: #F5F8FA; padding: 10px 16px; border-radius: 8px; border-bottom-left-radius: 0; align-self: flex-start; max-width: 80%; border: 1px solid var(--l);">
                            <div class="sm">Thanks. I'll ring the intercom.</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('customer.messages.store') }}">
                        @csrf
                        <div class="f" style="margin:0;">
                            <label class="k">Write a message
                                <input type="text" name="message" required style="margin-bottom: 8px;">
                            </label>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn">Send</button>
                            <button type="button" class="btn g">Attach photo</button>
                        </div>
                    </form>
                </div>

                <!-- Extras on the day -->
                <div class="card" style="margin-top: 16px;">
                    <h3>Extras on the day</h3>
                    <p class="sm">Pieter proposes an extra: <b>Extra stairs, R150</b></p>
                    
                    @if($booking->extras_status == 'Approved')
                        <div style="margin-top: 12px;"><span class="chip ok">Approved and paid</span></div>
                    @elseif($booking->extras_status == 'Declined')
                        <div style="margin-top: 12px;"><span class="chip wn">Declined</span></div>
                    @else
                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                            <form method="POST" action="{{ route('customer.extras.approve', $booking->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn">Approve and pay</button>
                            </form>
                            <form method="POST" action="{{ route('customer.extras.decline', $booking->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn g">Decline</button>
                            </form>
                        </div>
                    @endif
                    <p class="m sm" style="margin-top: 12px;">Extras are always approved and paid through MrRel, not in cash.</p>
                </div>

                <!-- When the job is done -->
                <div class="card" style="margin-top: 16px;">
                    <h3>When the job is done</h3>
                    @if($booking->job_confirmed)
                        <div style="margin-top: 12px;"><span class="chip ok">Job Confirmed</span></div>
                    @else
                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                            <form method="POST" action="{{ route('customer.bookings.confirm', $booking->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn">Confirm the job is done</button>
                            </form>
                            <a href="{{ route('customer.problems') }}" class="btn g" style="text-decoration:none;">Report a problem</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <div class="card">
                    <h3>Payment</h3>
                    <div class="big">R{{ number_format($booking->total_price, 2) }}</div>
                    <div style="margin-bottom: 12px;"><span class="chip ok">{{ $booking->status }}</span></div>
                    <div class="m sm" style="margin-bottom: 12px;">Receipt BKG-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}-R1</div>
                    <button type="button" class="btn g w">Download receipt</button>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Documents</h3>
                    <ul>
                        <li>Accepted quote QTN-0001 Rev 01 (PDF)</li>
                        <li>Receipt</li>
                    </ul>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Review your provider</h3>
                    <form method="POST" action="{{ route('customer.review.store') }}">
                        @csrf
                        <div class="pl" style="margin-bottom: 12px;">
                            <label><input type="radio" name="rating" value="5" required> 5</label>
                            <label><input type="radio" name="rating" value="4"> 4</label>
                            <label><input type="radio" name="rating" value="3"> 3</label>
                            <label><input type="radio" name="rating" value="2"> 2</label>
                            <label><input type="radio" name="rating" value="1"> 1</label>
                        </div>
                        <div class="f">
                            <label class="k">Comment<input type="text" name="comment" placeholder="How was the service?"></label>
                        </div>
                        <button type="submit" class="btn w">Submit review</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="card" style="margin-top: 20px;">
            <h3>No active bookings</h3>
            <p class="m">When you accept a quote, your booking details will appear here.</p>
            <a href="{{ route('customer.quotes') }}" class="btn" style="text-decoration:none;">View Quotes</a>
        </div>
    @endif

    <!-- GOOGLE MAPS SCRIPT (USES CONFIG) -->
    @if($booking)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}"></script>
    <script>
        function initMap() {
            // Center map on Cape Town (replace with dynamic coords later)
            const location = { lat: -33.9249, lng: 18.4241 };
            
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: location,
                disableDefaultUI: true,
            });

            // Add a marker for the driver
            new google.maps.Marker({
                position: location,
                map: map,
                title: "Pieter is here",
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                }
            });

            // Add a marker for the customer address
            new google.maps.Marker({
                position: { lat: -33.8949, lng: 18.6241 },
                map: map,
                title: "Your address",
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                }
            });
        }
        window.onload = initMap;
    </script>
    @endif
@endsection