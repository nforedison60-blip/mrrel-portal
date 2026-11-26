@extends('layouts.landlord')

@section('content')
    <h1>Tenant matches</h1>
    <div class="m">MrRel finds tenants whose needs match your property. You see a short profile first. If you would like to proceed, pay the once-off R{{ number_format($fee, 0) }} introduction and coordination fee and we release the tenant's contact details and arrange the viewing.</div>

    <h2 style="margin-top: 24px; font-size: 20px;">Waiting for your decision</h2>
    @if($waiting->isEmpty())
        <div class="card empty" style="margin-top: 12px;">
            <h3>No tenant matches right now</h3>
            <p class="m">When MrRel finds a tenant who fits one of your live properties, the profile appears here.</p>
            <a href="{{ route('landlord.properties') }}" class="btn g" style="text-decoration:none;">See my properties</a>
        </div>
    @else
        <div class="g2">
            @foreach($waiting as $m)
                @php($need = $m->need)
                <div class="card">
                    <div class="row" style="justify-content: space-between;">
                        <h3 style="margin:0;">{{ \App\Support\LandlordIntro::firstName(optional($m->user)->name) }}</h3>
                        @if($m->intro_status === 'Pending')<span class="chip wn">Payment not finished</span>@else<span class="chip">New</span>@endif
                    </div>
                    <p class="m sm" style="margin: 4px 0 10px;">For your property: <strong>{{ optional($m->property)->title }}</strong> ({{ optional($m->property)->suburb }})</p>
                    <ul>
                        <li>Looking in: {{ optional($need)->areas ?: 'Not given' }}</li>
                        <li>Budget: @if(optional($need)->budget_min)R{{ number_format($need->budget_min) }} to @endif up to R{{ number_format((float) optional($need)->budget_max) }} a month</li>
                        <li>Bedrooms: {{ optional($need)->bedrooms ?: 'Not given' }}</li>
                        <li>Wants to move in: {{ optional(optional($need)->move_in_date)->format('d M Y') ?: 'Flexible' }}</li>
                    </ul>
                    <p class="m sm" style="margin: 10px 0;">Name, phone and email are released once you pay the introduction fee.</p>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <form method="POST" action="{{ route('landlord.introductions.pay', $m->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn" style="margin:0;">Proceed and pay R{{ number_format($fee, 0) }}</button>
                        </form>
                        <form method="POST" action="{{ route('landlord.introductions.decline', $m->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn g" style="margin:0;">Not interested</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2 style="margin-top: 28px; font-size: 20px;">Introductions paid</h2>
    @if($released->isEmpty())
        <p class="m" style="margin-top: 8px;">Tenants you have paid to meet appear here with their contact details.</p>
    @else
        <div class="g2">
            @foreach($released as $m)
                <div class="card tot">
                    <div class="row" style="justify-content: space-between;">
                        <h3 style="margin:0;">{{ optional($m->user)->name }}</h3>
                        <span class="chip ok">Paid</span>
                    </div>
                    <p class="m sm" style="margin: 4px 0 10px;">For: <strong>{{ optional($m->property)->title }}</strong> ({{ optional($m->property)->suburb }})</p>
                    <ul>
                        <li>Phone: {{ optional($m->user)->phone ?: 'Not given' }}</li>
                        <li>Email: {{ optional($m->user)->email }}</li>
                        <li>Wants to move in: {{ optional(optional($m->need)->move_in_date)->format('d M Y') ?: 'Flexible' }}</li>
                    </ul>
                    <p class="m sm" style="margin-top: 10px;">MrRel will coordinate the viewing with you. After that you and the tenant agree the lease directly. Rent and deposit are paid to you, not to MrRel.</p>
                </div>
            @endforeach
        </div>
    @endif
@endsection
