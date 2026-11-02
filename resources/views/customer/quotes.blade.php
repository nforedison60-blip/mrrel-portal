@extends('layouts.app')

@section('content')
    <h1>Compare your quotes</h1>
    <div class="m">REQ-0042 · 1 bed, Parklands to Bellville · Sat 31 Oct. Every quote uses the same MrRel layout.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Instant prices (small jobs)</h3>
        <p class="m sm">Based on each provider’s rate card.</p>
        
        <div class="g2">
            <div class="card">
                <div class="row">
                    <span class="av">L</span>
                    <div>
                        <b>Lwazi</b> <span class="chip ok">Verified</span>
                        <div class="m sm">Sample Assembly · Bakkie</div>
                        <div class="m sm">4.8 rating</div>
                    </div>
                </div>
                <div class="big" style="margin-top: 16px;">R450</div>
                <div class="m sm" style="margin-bottom: 12px;">Minimum charge applies. Includes fuel.</div>
                <button type="button" class="btn">Book this price</button>
                <button type="button" class="btn g">Request a quote instead</button>
            </div>

            <div class="card">
                <div class="row">
                    <span class="av">T</span>
                    <div>
                        <b>Thandi</b> <span class="chip ok">Verified</span>
                        <div class="m sm">Sample Movers A · Bakkie</div>
                        <div class="m sm">4.7 rating</div>
                    </div>
                </div>
                <div class="big" style="margin-top: 16px;">R520</div>
                <div class="m sm" style="margin-bottom: 12px;">Includes driver, bakkie and fuel.</div>
                <button type="button" class="btn">Book this price</button>
                <button type="button" class="btn g">Request a quote instead</button>
            </div>
        </div>
    </div>

    <h2 style="margin-top: 30px;">Quotes</h2>
    
    <div class="g3">
        <!-- Quote 1 -->
        <div class="card">
            <div class="row">
                <span class="av">T</span>
                <div>
                    <b>Thandi</b> <span class="chip ok">Verified</span>
                    <div class="m sm">Sample Movers A · Bakkie and driver</div>
                    <div class="m sm">4.8 rating</div>
                </div>
            </div>
            <div class="big" style="margin-top: 16px;">R450</div>
            <span class="chip">Fixed price</span>
            <div class="m sm" style="margin-bottom: 12px;">Base R300 · Distance R150</div>
            <p class="sm"><b>Included:</b> Driver, bakkie, fuel<br><b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00<br><b>Valid for:</b> 48 hours</p>
            <button type="button" class="btn">Accept</button>
            <button type="button" class="btn g">Ask for changes</button>
        </div>

        <!-- Quote 2 -->
        <div class="card">
            <div class="row">
                <span class="av">P</span>
                <div>
                    <b>Pieter</b> <span class="chip ok">Verified</span>
                    <div class="m sm">Sample Movers B · Bakkie and two movers</div>
                    <div class="m sm">4.8 rating</div>
                </div>
            </div>
            <div class="big" style="margin-top: 16px;">R600</div>
            <span class="chip">Fixed price</span>
            <div class="m sm" style="margin-bottom: 12px;">Base R350 · Distance R150 · Extra mover R100</div>
            <p class="sm"><b>Included:</b> Fuel, loading, unloading<br><b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00<br><b>Valid for:</b> 48 hours</p>
            <button type="button" class="btn">Accept</button>
            <button type="button" class="btn g">Ask for changes</button>
        </div>

        <!-- Quote 3 -->
        <div class="card">
            <div class="row">
                <span class="av">A</span>
                <div>
                    <b>Ayesha</b> <span class="chip ok">Verified</span>
                    <div class="m sm">Sample Movers C · Small truck and two movers</div>
                    <div class="m sm">4.8 rating</div>
                </div>
            </div>
            <div class="big" style="margin-top: 16px;">R750</div>
            <span class="chip">Fixed price</span>
            <div class="m sm" style="margin-bottom: 12px;">Base R450 · Distance R150 · Extra mover R150</div>
            <p class="sm"><b>Included:</b> Truck, two movers, wrapping<br><b>Arrival:</b> Sat 31 Oct, 08:00 to 10:00<br><b>Valid for:</b> 48 hours</p>
            <button type="button" class="btn">Accept</button>
            <button type="button" class="btn g">Ask for changes</button>
        </div>
    </div>
    
    <p class="m sm">Quotes expire after their validity. Accepting is online: no signature, and we record the date and time.</p>
@endsection