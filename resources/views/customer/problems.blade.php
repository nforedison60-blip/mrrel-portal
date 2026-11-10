@extends('layouts.app')

@section('content')
    <h1>Problems</h1>
    <div class="m">Pages not shown elsewhere.</div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Raise a problem</h3>
            <form method="POST" action="{{ route('customer.problems.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="f">
                    <label>Booking
                        <select name="booking_id">
                            <option value="">General issue</option>
                            <option value="1">BKG-0101</option>
                        </select>
                    </label>
                    <label class="k">Subject
                        <input type="text" name="subject">
                    </label>
                    <label class="k">What happened
                        <textarea name="description" rows="4" required></textarea>
                    </label>
                    <label class="k">Photos or documents
                        <input type="file" name="image" accept="image/*">
                    </label>
                </div>
                <button type="submit" class="btn">Send</button>
            </form>
        </div>

        <div class="card">
            <h3>My problems</h3>
            @if($problems->isEmpty())
                <p class="m">You have no open problems.</p>
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