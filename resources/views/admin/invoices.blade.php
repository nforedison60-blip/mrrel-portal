@extends('layouts.admin')

@section('content')
@php
    $outstanding = $invoices->where('status', 'Unpaid');
    $collected = $invoices->where('status', 'Paid');
    $chips = ['Unpaid' => 'wn', 'Paid' => 'ok', 'Cancelled' => ''];
@endphp

    <h1>Invoices</h1>
    <div class="m">Send a letting fee invoice to a landlord once the lease is concluded. The landlord pays online or by EFT, and MrRel issues the receipt.</div>

    <div class="g3" style="margin-top: 20px;">
        <div class="card"><div class="m sm">Outstanding</div><div class="big">R{{ number_format($outstanding->sum('amount'), 2) }}</div><div class="m sm">{{ $outstanding->count() }} {{ \Illuminate\Support\Str::plural('invoice', $outstanding->count()) }}</div></div>
        <div class="card"><div class="m sm">Collected</div><div class="big">R{{ number_format($collected->sum('amount'), 2) }}</div><div class="m sm">{{ $collected->count() }} {{ \Illuminate\Support\Str::plural('invoice', $collected->count()) }} paid</div></div>
        <div class="card"><div class="m sm">Overdue</div><div class="big">{{ $outstanding->filter(fn($i) => $i->isOverdue())->count() }}</div><div class="m sm">Past the due date</div></div>
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>New invoice</h3>
        @if($properties->isEmpty())
            <p class="m">There are no live or accepted properties to invoice yet.</p>
        @else
            <form method="POST" action="{{ route('admin.invoices.store') }}">
                @csrf
                <div class="f">
                    <label class="k">Property (accepted ones are listed first)
                        <select name="property_id" id="invProperty" required>
                            <option value="">Choose a property...</option>
                            @foreach($properties as $p)
                                <option value="{{ $p->id }}" data-rent="{{ $p->rent }}" data-title="{{ $p->title }}" @selected((string) old('property_id') === (string) $p->id)>
                                    {{ $p->title }} ({{ $p->suburb }}) - {{ $owners[$p->user_id] ?? 'No landlord' }} - {{ $p->status === 'Under Offer' ? 'Tenant accepted' : 'Live' }} - rent R{{ number_format($p->rent, 0) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>Amount (R)
                        <input type="number" name="amount" id="invAmount" step="0.01" min="1" value="{{ old('amount') }}" required>
                        <span class="m sm">Suggested: {{ round($feeRate * 100) }}% of one month's rent. You can change it.</span>
                    </label>
                    <label>Days to pay
                        <input type="number" name="due_days" min="1" max="60" value="{{ old('due_days', $dueDays) }}">
                    </label>
                    <label class="k">Description (optional)
                        <input type="text" name="description" id="invDesc" maxlength="255" value="{{ old('description') }}" placeholder="Letting fee: property name">
                    </label>
                </div>
                <button type="submit" class="btn">Send invoice</button>
            </form>
            <script>
                (function () {
                    var rate = {{ json_encode($feeRate) }};
                    var sel = document.getElementById('invProperty'), amt = document.getElementById('invAmount');
                    sel.addEventListener('change', function () {
                        var opt = sel.options[sel.selectedIndex];
                        if (opt && opt.dataset.rent && !amt.value) {
                            amt.value = (parseFloat(opt.dataset.rent) * rate).toFixed(2);
                        }
                    });
                })();
            </script>
        @endif
    </div>

    <div class="card" style="margin-top: 16px;">
        <h3>All invoices</h3>
        @if($invoices->isEmpty())
            <p class="m">No invoices yet.</p>
        @else
            <div style="overflow-x:auto;">
            <table class="tb">
                <tr>
                    <th>Invoice</th>
                    <th>Landlord</th>
                    <th>For</th>
                    <th>Amount</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @foreach($invoices as $invoice)
                    <tr>
                        <td><b>{{ $invoice->number }}</b><div class="m sm">{{ $invoice->created_at->format('d M Y') }}</div></td>
                        <td>{{ $owners[$invoice->user_id] ?? 'Removed user' }}</td>
                        <td>{{ $invoice->description }}</td>
                        <td>R{{ number_format($invoice->amount, 2) }}</td>
                        <td>{{ $invoice->due_at ? $invoice->due_at->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="chip {{ $chips[$invoice->status] ?? '' }}">{{ $invoice->status }}</span>
                            @if($invoice->isOverdue()) <span class="chip wn">Overdue</span> @endif
                            @if($invoice->isPaid())
                                <div class="m sm">{{ $invoice->payment_method === 'Ozow' ? 'Online' : 'EFT' }} &middot; {{ $invoice->paid_at->format('d M Y') }}<br>{{ $invoice->receipt_number }}</div>
                            @endif
                        </td>
                        <td>
                            @if($invoice->status === 'Unpaid')
                                <form method="POST" action="{{ route('admin.invoices.paid', $invoice) }}" style="display:flex; gap:6px; flex-wrap:wrap; margin:0 0 6px;" onsubmit="return confirm('Only do this when the money is in the bank account. Record it as paid?');">
                                    @csrf
                                    <input type="text" name="reference" placeholder="Bank reference (optional)" maxlength="100" style="max-width:190px; margin:0;">
                                    <button type="submit" class="btn" style="padding:6px 14px;font-size:13px;margin:0;">EFT received</button>
                                </form>
                                <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}" style="margin:0;" onsubmit="return confirm('Cancel this invoice?');">
                                    @csrf
                                    <button type="submit" class="btn g" style="padding:6px 14px;font-size:13px;margin:0;">Cancel invoice</button>
                                </form>
                            @else
                                <span class="m sm">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            </div>
        @endif
    </div>
@endsection
