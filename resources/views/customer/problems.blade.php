@extends('layouts.app')

@section('content')
    <h1>Report a problem</h1>
    <div class="m">Something went wrong with a booking or the service? Tell us and MrRel will help.</div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Tell us what happened</h3>
            <form method="POST" action="{{ route('customer.problems.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="f">
                    <label>Which booking?
                        <select name="booking_id">
                            <option value="">General issue (no booking)</option>
                            @foreach($bookings as $b)
                                <option value="{{ $b->id }}" @selected((string) old('booking_id') === (string) $b->id)>
                                    BKG-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }} · {{ optional($b->quote)->provider_name ?: 'Provider' }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="k">Subject
                        <input type="text" name="subject" value="{{ old('subject') }}" maxlength="150" required>
                    </label>
                    <label class="k">What happened
                        <textarea name="description" rows="4" maxlength="2000" required>{{ old('description') }}</textarea>
                    </label>
                    <label class="k">Photo (optional)
                        <input type="file" name="image" accept="image/*">
                    </label>
                </div>
                <button type="submit" class="btn">Send</button>
            </form>
        </div>

        <div class="card">
            <h3>My problems</h3>
            @if($problems->isEmpty())
                <p class="m">You have not reported any problems.</p>
            @else
                <table class="tb">
                    <tr>
                        <th>Ticket</th>
                        <th>Status</th>
                    </tr>
                    @foreach($problems as $problem)
                        <tr>
                            <td>
                                D-{{ str_pad($problem->id, 3, '0', STR_PAD_LEFT) }} · {{ $problem->subject ?? \Illuminate\Support\Str::limit($problem->description, 40) }}
                                <div class="m sm">{{ $problem->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="chip {{ $problem->status == 'Resolved' ? 'ok' : 'wn' }}">
                                    {{ $problem->status == 'Resolved' ? 'Resolved' : 'In progress' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
@endsection
