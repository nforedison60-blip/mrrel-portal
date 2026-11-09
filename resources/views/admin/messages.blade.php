@extends('layouts.admin')

@section('content')
    <h1>Messages</h1>
    <div class="m">Monitor platform messages for disputes and quality control.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>All Messages</h3>
        @if($messages->isEmpty())
            <p class="m">No messages have been sent yet.</p>
        @else
            <table class="tb">
                <tr>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                @foreach($messages as $msg)
                <tr>
                    <td><b>{{ $msg->sender_name }}</b></td>
                    <td>{{ $msg->message }}</td>
                    <td>{{ $msg->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection