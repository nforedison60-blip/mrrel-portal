@extends('layouts.admin')

@section('content')
    <h1>Matching</h1>
    <div class="m">Tenants looking for a home. Tick the places that fit and send the match.</div>

    <h3 style="margin-top: 20px;">Waiting for a match ({{ $open->count() }})</h3>
    @forelse($open as $need)
        <div class="card" style="margin-top: 12px;">
            <div class="row" style="justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h3 style="margin-bottom: 2px;">{{ optional($need->user)->name ?? 'Tenant' }}</h3>
                    <div class="m sm">Asked {{ $need->created_at->format('d M Y') }}</div>
                </div>
                <span class="chip {{ $need->paid ? 'ok' : 'wn' }}">{{ $need->paid ? 'Matching fee paid' : 'Matching fee not paid yet' }}</span>
            </div>
            <table class="tb" style="margin-top: 8px;">
                <tr><td>Areas</td><td>{{ $need->areas }}</td></tr>
                <tr><td>Budget</td><td>@if($need->budget_min)R{{ number_format($need->budget_min) }} to @endif R{{ number_format($need->budget_max) }} a month</td></tr>
                <tr><td>Bedrooms</td><td>{{ $need->bedrooms }}</td></tr>
                <tr><td>Move-in date</td><td>{{ optional($need->move_in_date)->format('d M Y') ?: 'Not given' }}</td></tr>
                <tr><td>Other requirements</td><td>{{ $need->notes ?: 'None' }}</td></tr>
            </table>

            <h3 style="margin-top: 14px;">Suggested places</h3>
            @if($need->suggested->isEmpty())
                <p class="m sm">No live places fit yet. When one is approved it appears here.</p>
            @else
                <form method="POST" action="{{ route('admin.tenant-matching.send', $need->id) }}">
                    @csrf
                    @foreach($need->suggested as $property)
                        <label style="display:flex; gap:10px; align-items:flex-start; padding:6px 0;">
                            <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" style="width:auto; margin-top:4px;">
                            <span><b>{{ $property->title }}</b><br><span class="m sm">{{ $property->suburb }}, {{ $property->city }} · {{ $property->bedrooms }} bed · R{{ number_format($property->rent) }} a month</span></span>
                        </label>
                    @endforeach
                    <button type="submit" class="btn" style="margin-top: 8px;">Send match</button>
                </form>
            @endif
        </div>
    @empty
        <div class="card empty" style="margin-top: 12px;"><p class="m">No tenants are waiting for a match.</p></div>
    @endforelse

    <h3 style="margin-top: 28px;">Already matched ({{ $matched->count() }})</h3>
    @forelse($matched as $need)
        <div class="card" style="margin-top: 12px;">
            <div class="row" style="justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <h3 style="margin-bottom: 2px;">{{ optional($need->user)->name ?? 'Tenant' }} <span class="m sm">({{ $need->areas }}, up to R{{ number_format($need->budget_max) }})</span></h3>
                <span class="chip {{ $need->paid ? 'ok' : 'wn' }}">{{ $need->paid ? 'Matching fee paid' : 'Matching fee not paid yet' }}</span>
            </div>
            <table class="tb" style="margin-top: 8px;">
                @foreach($need->matches->where('status', 'Offered') as $match)
                    <tr>
                        <td>{{ optional($match->property)->title ?? 'Property no longer available' }}<div class="m sm">{{ optional($match->property)->suburb }}, {{ optional($match->property)->city }}</div></td>
                        <td style="text-align:right;">
                            <form method="POST" action="{{ route('admin.tenant-matching.unmatch', $match->id) }}" style="margin:0;" onsubmit="return confirm('Remove this match?');">
                                @csrf
                                <button type="submit" class="btn g" style="padding:4px 12px; font-size:12px; margin:0;">Unmatch</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @empty
        <div class="card empty" style="margin-top: 12px;"><p class="m">Nothing matched yet.</p></div>
    @endforelse
@endsection
