@extends('layouts.admin')

@section('content')
    <h1>Landlord introductions</h1>
    <div class="m">Landlords who answered a tenant match. Once the introduction fee is paid, both contacts are shown here so you can arrange the viewing.</div>

    <div class="g3" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Collected</div><div class="big">R{{ number_format($collected, 2) }}</div><div class="m sm">{{ $paidCount }} paid {{ \Illuminate\Support\Str::plural('introduction', $paidCount) }}</div></div>
        <div class="card"><div class="m sm">Waiting to pay</div><div class="big">{{ $matches->where('landlord_status', 'Interested')->filter(fn($m) => !$m->introPaid())->count() }}</div><div class="m sm">Landlord interested, not yet paid</div></div>
        <div class="card"><div class="m sm">Declined</div><div class="big">{{ $matches->where('landlord_status', 'Declined')->count() }}</div><div class="m sm">Landlord not interested</div></div>
    </div>

    <div class="card" style="margin-top: 16px;">
        @if($matches->isEmpty())
            <div class="empty"><h3>Nothing yet</h3><p class="m">Landlords appear here once they answer a tenant match.</p></div>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr><th>Property</th><th>Landlord</th><th>Tenant</th><th>Answer</th><th>Fee</th><th>Contacts to coordinate</th></tr>
                @foreach($matches as $m)
                    @php($landlord = $landlords[optional($m->property)->user_id] ?? null)
                    <tr>
                        <td>{{ optional($m->property)->title }}<div class="m sm">{{ optional($m->property)->suburb }}</div></td>
                        <td>{{ optional($landlord)->name ?: 'Unknown' }}</td>
                        <td>{{ optional($m->user)->name }}</td>
                        <td><span class="chip {{ $m->landlord_status === 'Interested' ? 'ok' : 'gr' }}">{{ $m->landlord_status }}</span></td>
                        <td>
                            @if($m->introPaid())
                                <span class="chip ok">Paid R{{ number_format((float) $m->intro_amount, 2) }}</span>
                                <div class="m sm">{{ optional($m->intro_paid_at)->format('d M Y H:i') }}</div>
                            @elseif($m->landlord_status === 'Interested')
                                <span class="chip wn">Waiting</span>
                            @else
                                <span class="m sm">-</span>
                            @endif
                        </td>
                        <td class="sm">
                            @if($m->introPaid())
                                <strong>Landlord:</strong> {{ optional($landlord)->phone ?: 'no phone' }} / {{ optional($landlord)->email }}<br>
                                <strong>Tenant:</strong> {{ optional($m->user)->phone ?: 'no phone' }} / {{ optional($m->user)->email }}
                            @else
                                <span class="m">Shown after payment</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
