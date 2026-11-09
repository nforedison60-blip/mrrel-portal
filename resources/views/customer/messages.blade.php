@extends('layouts.app')

@section('content')
    <h1>Messages</h1>
    <div class="m">Chat with your provider about your upcoming move.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-top:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    <div class="g2" style="margin-top: 20px;">
        <div class="card">
            <h3>Conversation with Pieter</h3>
            
            <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px;">
                @if($messages->isEmpty())
                    <p class="m sm">No messages yet. Send a message to start the conversation.</p>
                @else
                    @foreach($messages as $msg)
                        <div style="background: {{ $msg->sender_name == 'Lerato' ? '#E3F1F2' : '#F5F8FA' }}; padding: 10px; border-radius: 8px; align-self: {{ $msg->sender_name == 'Lerato' ? 'flex-end' : 'flex-start' }}; max-width: 80%;">
                            <b class="sm">{{ $msg->sender_name }}</b>
                            <div class="sm">{{ $msg->message }}</div>
                            <div class="m sm" style="font-size: 11px; text-align: right;">{{ $msg->created_at->format('H:i') }}</div>
                        </div>
                    @endforeach
                @endif
            </div>

            <form method="POST" action="{{ route('customer.messages.store') }}">
                @csrf
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="message" placeholder="Type your message..." required style="margin:0;">
                    <button type="submit" class="btn" style="margin:0;">Send</button>
                </div>
            </form>
        </div>
    </div>
@endsection