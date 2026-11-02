@extends('layouts.app')

@section('content')
    <h1>Booking BKG-0101</h1>
    <div class="m">Moving one bed · Sat 31 Oct · Parklands to Bellville</div>

    <div class="sp">
        <div>
            <!-- Provider Card -->
            <div class="card">
                <h3>Your provider</h3>
                <div class="row">
                    <span class="av">P</span>
                    <div>
                        <b>Pieter</b> <span class="chip ok">Verified</span>
                        <div class="m sm">Sample Movers B · 1-ton bakkie</div>
                        <div class="m sm">4.8 rating</div>
                    </div>
                </div>
                <p class="sm" style="margin-top: 12px;">
                    <b>Vehicle:</b> White 1-ton bakkie · <b>Plate:</b> CA 000 000<br>
                    <b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00
                </p>
                <div class="m sm">If someone else arrives, don’t hand anything over and call us.</div>
            </div>

            <!-- Progress -->
            <div class="card" style="margin-top: 16px;">
                <h3>Progress</h3>
                <div style="margin-bottom: 8px;">
                    <span class="chip ok">Requested</span>
                    <span class="chip ok">Quote accepted</span>
                    <span class="chip ok">Paid</span>
                    <span class="chip">Job done</span>
                    <span class="chip">Confirmed</span>
                    <span class="chip">Reviewed</span>
                </div>
                <p class="m sm">Current step: Paid</p>
            </div>

            <!-- Extras -->
            <div class="card" style="margin-top: 16px;">
                <h3>Extras on the day</h3>
                <p>Pieter proposes an extra: <b>Extra stairs, R150</b></p>
                <button type="button" class="btn">Approve and pay</button>
                <button type="button" class="btn g">Decline</button>
                <div class="m sm" style="margin-top: 12px;">Extras are always approved and paid through MrRel, not in cash.</div>
            </div>

            <!-- When job is done -->
            <div class="card" style="margin-top: 16px;">
                <h3>When the job is done</h3>
                <button type="button" class="btn">Confirm the job is done</button>
                <button type="button" class="btn g">Report a problem</button>
            </div>
        </div>

        <div>
            <!-- Payment -->
            <div class="card">
                <h3>Payment</h3>
                <div class="big">R600.00</div>
                <div style="margin-bottom: 12px;"><span class="chip ok">Paid</span></div>
                <div class="m sm" style="margin-bottom: 12px;">Receipt BKG-0101-R1</div>
                <button type="button" class="btn g w">Download receipt</button>
            </div>

            <!-- Documents -->
            <div class="card" style="margin-top: 16px;">
                <h3>Documents</h3>
                <ul>
                    <li>Accepted quote QTN-0001 Rev 01 (PDF)</li>
                    <li>Receipt</li>
                </ul>
            </div>

            <!-- Review -->
            <div class="card" style="margin-top: 16px;">
                <h3>Review your provider</h3>
                <div style="margin-bottom: 12px;">
                    <span class="chip star">1</span>
                    <span class="chip star">2</span>
                    <span class="chip star">3</span>
                    <span class="chip star">4</span>
                    <span class="chip star">5</span>
                </div>
                <div class="f">
                    <label class="k">Comment<input type="text"></label>
                </div>
                <button type="button" class="btn w">Submit review</button>
            </div>
        </div>
    </div>
@endsection