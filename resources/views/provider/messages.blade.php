@extends('layouts.provider')

@section('content')
    <h1>Messages</h1>
    <div class="m">Chat with your customer.</div>

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Conversation with Lerato</h3>
            
            <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px;">
                @if($messages->isEmpty())
                    <p class="m sm">No messages yet.</p>
                @else
                    @foreach($messages as $msg)
                        <div style="background: {{ $msg->sender_name == 'Pieter' ? '#E3F1F2' : '#F5F8FA' }}; padding: 10px; border-radius: 8px; align-self: {{ $msg->sender_name == 'Pieter' ? 'flex-end' : 'flex-start' }}; max-width: 80%;">
                            <b class="sm">{{ $msg->sender_name }}</b>
                            <div class="sm">{{ $msg->message }}</div>
                            <div class="m sm" style="font-size: 11px; text-align: right;">{{ $msg->created_at->format('H:i') }}</div>
                        </div>
                    @endforeach
                @endif
            </div>

            <form method="POST" action="{{ route('provider.messages.store') }}">
                @csrf
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="message" placeholder="Type your message..." required style="margin:0;">
                    <button type="submit" class="btn" style="margin:0;">Send</button>
                </div>
            </form>
        </div>
    </div>
@endsection