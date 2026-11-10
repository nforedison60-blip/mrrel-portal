@extends('layouts.provider')

@section('content')
    <h1>Job day: location sharing and chat</h1>
    <div class="m">Update the customer as you go. The customer sees your progress.</div>

    <div class="sp" style="margin-top: 20px;">
        <!-- LEFT COLUMN -->
        <div>
            <div class="card">
                <h3>Today's job · BKG-0101</h3>
                <p class="m sm" style="margin-bottom: 16px;">1 bed, Parklands to Bellville · Sat 31 Oct · Arrival 08:00 to 10:00 · Customer: Lerato</p>
                
                <b class="sm">Update status</b>
                <div class="pl" style="margin-top: 8px;">
                    <button type="button" class="btn" style="margin:0;">On my way</button>
                    <button type="button" class="btn g" style="margin:0;">Arrived</button>
                    <button type="button" class="btn g" style="margin:0;">Start loading</button>
                    <button type="button" class="btn g" style="margin:0;">In transit</button>
                    <button type="button" class="btn g" style="margin:0;">Delivered</button>
                    <button type="button" class="btn g" style="margin:0;">Mark job complete (add photos)</button>
                </div>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Share my live location</h3>
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; margin-bottom: 8px;">
                    <input type="checkbox" style="width: auto; margin: 0;"> Share my location with the customer for this job only
                </label>
                <p class="m sm">You choose to share. Sharing stops when you mark the job complete. Keep this page open on your phone. Customers see your position and an estimated arrival time.</p>
                
                <div style="width: 100%; height: 200px; background-color: #E3F1F2; border-radius: 8px; margin-top: 16px; display: flex; align-items: center; justify-content: center;">
                    <svg width="300" height="150" viewBox="0 0 400 150">
                        <path d="M 50 100 Q 150 50 250 100 T 350 50" stroke="#0E7C86" stroke-width="4" stroke-dasharray="8 8" fill="none"/>
                        <circle cx="50" cy="100" r="8" fill="#F4B731" stroke="#10243A" stroke-width="2"/>
                        <circle cx="350" cy="50" r="8" fill="#0E7C86"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <div class="card">
                <h3>Chat with Lerato</h3>
                <div class="row" style="margin-bottom: 12px;">
                    <span class="av" style="width: 40px; height: 40px; font-size: 16px;">P</span>
                    <div>
                        <b>Pieter</b> <span class="chip ok">Verified</span>
                        <div class="m sm">Booking BKG-0101</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    <div style="background: #F5F8FA; padding: 10px 16px; border-radius: 8px; border-bottom-left-radius: 0; align-self: flex-start; max-width: 80%; border: 1px solid var(--l);">
                        <div class="sm">Hi, I'm on my way. I should arrive around 08:25.</div>
                    </div>
                    <div style="background: #E3F1F2; padding: 10px 16px; border-radius: 8px; border-bottom-right-radius: 0; align-self: flex-end; max-width: 80%; border: 1px solid #D5DEE6;">
                        <div class="sm">Great, the lift is booked for 08:30.</div>
                    </div>
                     <div style="background: #F5F8FA; padding: 10px 16px; border-radius: 8px; border-bottom-left-radius: 0; align-self: flex-start; max-width: 80%; border: 1px solid var(--l);">
                        <div class="sm">Thanks. I'll ring the intercom.</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('provider.messages.store') }}">
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
                <p class="m sm" style="margin-top: 12px;">Phone numbers stay private. Messages are saved with the booking, and MrRel can read them if there is a problem.</p>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Conversations</h3>
                <table class="tb">
                    <tr>
                        <th>With</th>
                        <th>About</th>
                    </tr>
                    <tr>
                        <td>Lerato</td>
                        <td>BKG-0101</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection