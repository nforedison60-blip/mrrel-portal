@extends('layouts.admin')

@section('content')
    <h1>Disputes</h1>
    <div class="m">Open, In progress and Resolved. A genuine dispute pauses the payout.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="sp" style="margin-top: 20px;">
        
        <!-- LEFT: TICKETS LIST -->
        <div class="card">
            <h3>Tickets</h3>
            @if($problems->isEmpty())
                <p class="m">No disputes reported yet.</p>
            @else
                <table class="tb">
                    <tr>
                        <th>Ticket</th>
                        <th>Status</th>
                    </tr>
                    @foreach($problems as $problem)
                    <tr>
                        <td>D-{{ str_pad($problem->id, 3, '0', STR_PAD_LEFT) }} · {{ $problem->subject ?? $problem->description }}</td>
                        <td>
                            <span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">
                                {{ $problem->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <!-- RIGHT: DETAILED VIEW -->
        <div class="card">
            <h3>Ticket Details</h3>
            @if($problems->isEmpty())
                <p class="m">Select a ticket to view details.</p>
            @else
                @php $latest = $problems->first(); @endphp
                <h3>D-{{ str_pad($latest->id, 3, '0', STR_PAD_LEFT) }} · {{ $latest->subject ?? 'Issue' }}</h3>
                <div class="m sm" style="margin-bottom: 16px;">Customer: {{ $latest->customer_name }}</div>

                <div class="g2" style="margin-bottom: 16px;">
                    <div class="card" style="background: #F5F8FA;">
                        <h3>Customer evidence</h3>
                        <p class="m sm" style="margin-top: 12px;">{{ $latest->description }}</p>
                        @if($latest->image_path)
                            <div style="margin-top: 8px;">
                                <a href="{{ Storage::url($latest->image_path) }}" target="_blank" style="color: var(--t); text-decoration: underline;">View attached document</a>
                            </div>
                        @endif
                    </div>

                    <div class="card" style="background: #F5F8FA;">
                        <h3>Provider response</h3>
                        <p class="m sm" style="margin-top: 12px;">Awaiting response from provider.</p>
                    </div>
                </div>

                <div class="f">
                    <label>Outcome
                        <select>
                            <option>Redo</option>
                            <option>Refund</option>
                            <option>Dismissed</option>
                        </select>
                    </label>
                    <label>Resolution note
                        <input type="text" placeholder="e.g. Provider to redo the job">
                    </label>
                </div>
                
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; margin-top: 8px;">
                    <input type="checkbox" style="width: auto; margin: 0;"> Pause the payout
                </label>

                <div style="display: flex; gap: 8px; margin-top: 16px;">
                    <button type="button" class="btn">Save</button>
                    <form method="POST" action="{{ route('admin.disputes.resolve', $latest->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn g">Resolve</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection