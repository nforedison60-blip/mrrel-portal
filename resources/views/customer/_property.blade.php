@php
    $accepted = (int) $property->accepted_by === (int) auth()->id();
    $viewing = $viewings[$property->id] ?? null;
    $boxStyle = 'width:100%; height:200px; background:#E6EEF8; border-radius:8px; align-items:center; justify-content:center; color:var(--m); font-size:14px; margin-bottom:10px;';
@endphp
<div class="card">
    <div style="min-height:30px;">
        @if($accepted)
            <span class="chip ok">Accepted by you</span>
        @endif
        @if($viewing)
            <span class="chip {{ $viewing->status === 'Pending' ? 'wn' : 'ok' }}">Viewing {{ strtolower($viewing->status) }}</span>
        @endif
    </div>

    @if($property->image_path)
        <img src="{{ Storage::disk('s3')->url($property->image_path) }}" alt="Photo of {{ $property->title }}"
             style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="display:none; {{ $boxStyle }}">No photo available</div>
    @else
        <div style="display:flex; {{ $boxStyle }}">No photo uploaded</div>
    @endif

    <h3 style="margin-bottom:4px;">{{ $property->title }}</h3>
    <div class="m sm">
        R{{ number_format($property->rent, 0) }} a month · Deposit R{{ number_format($property->deposit, 0) }}
    </div>
    <div class="m sm">{{ $property->bedrooms }} bed · {{ $property->bathrooms }} bath · {{ $property->property_type }}</div>
    <p class="sm" style="margin-top:8px;"><b>Location:</b> {{ $property->suburb }}, {{ $property->city }}</p>
    @if($property->description)
        <p class="m sm">{{ \Illuminate\Support\Str::limit($property->description, 120) }}</p>
    @endif

    <div style="margin-top:12px;">
        @if($accepted)
            <p class="m sm">MrRel will contact you to finalise the lease.</p>
            <a href="{{ route('customer.fees') }}" class="btn g" style="margin:0 6px 0 0; text-decoration:none;">See costs</a>
            <a href="{{ route('customer.new-request') }}" class="btn g" style="margin:0; text-decoration:none;">Plan my move</a>
        @else
            @if(!$viewing || $viewing->status !== 'Pending')
                <form method="POST" action="{{ route('customer.viewing.store') }}" style="margin:0 0 8px;">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <input type="text" name="message" maxlength="500" placeholder="Message to the landlord (optional)" style="margin-bottom:8px;">
                    <button type="submit" class="btn g" style="width:100%; margin:0;">Request a viewing</button>
                </form>
            @else
                <p class="m sm" style="margin-bottom:8px;">The landlord has your viewing request.</p>
            @endif

            @if($property->status === 'Approved')
                <form method="POST" action="{{ route('customer.shortlist.accept', $property->id) }}" style="margin:0;" onsubmit="return confirm('Accept this place? MrRel will contact you to finalise the lease.')">
                    @csrf
                    <button type="submit" class="btn" style="width:100%; margin:0;">Accept this place</button>
                </form>
            @endif
        @endif
    </div>
</div>
