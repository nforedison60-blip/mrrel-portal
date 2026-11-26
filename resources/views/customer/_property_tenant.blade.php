@php
    $uid = auth()->id();
    $accepted = (int) $property->accepted_by === (int) $uid;
    $viewing = $viewings[$property->id] ?? null;
    $full = \App\Support\TenantAccess::canSeeFull($uid, $property);
    $matched = \App\Support\TenantAccess::isMatched($uid, $property->id);
    $paid = \App\Support\TenantAccess::hasPaidMatchingFee($uid);
    $attendOk = \App\Support\TenantFees::attendanceAvailable();
    $boxStyle = 'width:100%; height:200px; background:#E6EEF8; border-radius:8px; align-items:center; justify-content:center; color:var(--m); font-size:14px; margin-bottom:10px; text-align:center; padding:0 12px;';
@endphp
<div class="card">
    <div style="min-height:30px;">
        @if($accepted)
            <span class="chip ok">Accepted by you</span>
        @endif
        @if($viewing)
            <span class="chip {{ $viewing->status === 'Pending' ? 'wn' : 'ok' }}">Viewing {{ strtolower($viewing->status) }}</span>
            @if($viewing->mrrel_attends)
                <span class="chip ok">MrRel will attend</span>
            @endif
        @endif
        @if($matched)
            <span class="chip ok">Matched for you</span>
        @endif
    </div>

    @if($full)
        @if($property->image_path)
            <img src="{{ Storage::disk('s3')->url($property->image_path) }}" alt="Photo of {{ $property->title }}"
                 style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display:none; {{ $boxStyle }}">No photo available</div>
        @else
            <div style="display:flex; {{ $boxStyle }}">No photo uploaded</div>
        @endif
        @php($morePhotos = array_slice($property->photoUrls(), 1))
        @if($morePhotos)<div style="display:flex; gap:6px; margin-bottom:10px; overflow-x:auto;">@foreach($morePhotos as $url)<a href="{{ $url }}" target="_blank"><img src="{{ $url }}" alt="More photos of {{ $property->title }}" style="width:70px;height:56px;object-fit:cover;border-radius:6px;"></a>@endforeach</div>@endif
        <h3 style="margin-bottom:4px;">{{ $property->title }}</h3>
        <div class="m sm">R{{ number_format($property->rent, 0) }} a month · Deposit R{{ number_format($property->deposit, 0) }}</div>
    @else
        <div style="display:flex; {{ $boxStyle }}">Photo and full details are shown after the matching fee</div>
        <h3 style="margin-bottom:4px;">{{ $property->property_type }} in {{ $property->suburb }}</h3>
        <div class="m sm">R{{ number_format($property->rent, 0) }} a month</div>
    @endif
    <div class="m sm">{{ $property->bedrooms }} bed · {{ $property->bathrooms }} bath · {{ $property->property_type }}</div>
    <p class="sm" style="margin-top:8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
    @if($full && $property->description)
        <p class="m sm">{{ \Illuminate\Support\Str::limit($property->description, 120) }}</p>
    @endif

    <div style="margin-top:12px;">
        @if($full)
            @if($accepted)
                <p class="m sm">You will finalise the lease directly with the landlord.</p>
                <a href="{{ route('customer.fees') }}" class="btn g" style="margin:0 6px 0 0; text-decoration:none;">See costs</a>
                <a href="{{ route('customer.new-request') }}" class="btn g" style="margin:0; text-decoration:none;">Plan my move</a>
            @else
                @if(!$viewing || $viewing->status !== 'Pending')
                    <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin:0 0 8px;">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="text" name="message" maxlength="500" placeholder="Message about the viewing (optional)" style="margin-bottom:8px;">
                        @if($attendOk)
                            <label class="sm" style="display:flex; gap:8px; align-items:flex-start; margin-bottom:8px;">
                                <input type="checkbox" name="mrrel_attends" value="1" style="width:auto; margin-top:3px;">
                                <span>Have someone from MrRel attend for me<br><span class="m sm">R150 for the first 10 km round trip, plus R7.50 per additional kilometre. You see your exact estimate before you pay.</span></span>
                            </label>
                        @endif
                        <button type="submit" class="btn g" style="width:100%; margin:0;">Request a viewing</button>
                    </form>
                @else
                    <p class="m sm" style="margin-bottom:8px;">MrRel has your viewing request and is coordinating with the landlord.</p>
                @endif

                @if($property->status === 'Approved')
                    <form method="POST" action="{{ route('customer.shortlist.accept', $property->id) }}" style="margin:0;" onsubmit="return confirm('Accept this place? You will finalise the lease directly with the landlord.');">
                        @csrf
                        <button type="submit" class="btn" style="width:100%; margin:0;">Accept this place</button>
                    </form>
                @endif
            @endif
        @elseif($matched && !$paid)
            <p class="sm"><b>Pay the once-off R99 matching fee to see the full details.</b></p>
            <form method="POST" action="{{ route('tenant.pay.matching') }}" style="margin:8px 0 0;">
                @csrf
                <button type="submit" class="btn" style="width:100%; margin:0;">Pay R99</button>
            </form>
        @else
            <p class="m sm">MrRel shows the full details of homes matched to you.</p>
            <a href="{{ route('customer.needs.create') }}" class="btn g" style="margin:0; text-decoration:none;">Find a home</a>
        @endif
    </div>
</div>
