@extends('layouts.provider')

@section('content')
    <h1>My rate card</h1>
    <div class="m">Used to give customers instant prices on small jobs.</div>

    <div class="sp" style="margin-top: 20px;">
        <div class="card">
            <h3>Rates (rand)</h3>
            <div class="f">
                <label>Minimum charge<input type="number" value="400"></label>
                <label>Base trip fee: small items<input type="number" value="250"></label>
                <label>Base trip fee: medium item<input type="number" value="350"></label>
                <label>Base trip fee: large item<input type="number" value="600"></label>
                <label>Kilometres included in base trip<input type="number" value="10"></label>
                <label>Fee per extra kilometre<input type="number" value="10"></label>
                <label>Stairs surcharge<input type="number" value="80"></label>
                <label class="k">Other prices (cleaning, packing, hourly)<input type="text"></label>
            </div>
            <button type="button" class="btn">Save rate card</button>
        </div>

        <div class="card">
            <h3>Example price</h3>
            <ul>
                <li>Large item, 18 km, with stairs</li>
                <li>Minimum charge applies if the total is lower</li>
            </ul>
            <div class="big" style="margin-top: 8px">R740</div>
            <div class="m sm">Illustration only. Your rates decide the real price.</div>
        </div>
    </div>
@endsection