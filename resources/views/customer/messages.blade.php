@extends('layouts.app')

@section('content')
    <h1>Messages</h1>
    <div class="m" style="margin-bottom: 20px;">Chat with your provider in real-time.</div>

    <div class="card" style="max-width: 800px;">
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; max-height: 400px; overflow-y: auto; padding-right: 10px;">
            @forelse($messages as $msg)
                @if($msg->user_id == auth()->id())
                    <!-- My Message -->
                    <div style="background: #E3F1F2; padding: 12px 16px; border-radius: 12px; border-bottom-right-radius: 0; align-self: flex-end; max-width: 80%; border: 1px solid #D5DEE6;">
                        <div class="sm">{{ $msg->message }}</div>
                        <div style="font-size: 10px; color: #51657A; margin-top: 4px; text-align: right;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                @else
                    <!-- Their Message -->
                    <div style="background: #F5F8FA; padding: 12px 16px; border-radius: 12px; border-bottom-left-radius: 0; align-self: flex-start; max-width: 80%; border: 1px solid #D5DEE6;">
                        <div style="font-size: 12px; font-weight: bold; color: #0E7C86; margin-bottom: 4px;">{{ $msg->sender_name }}</div>
                        <div class="sm">{{ $msg->message }}</div>
                        <div style="font-size: 10px; color: #51657A; margin-top: 4px;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                @endif
            @empty
                <div class="m sm" style="text-align: center; padding: 20px;">No messages yet. Say hello!</div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('customer.messages.store') }}">
            @csrf
            <div class="f" style="margin:0; display: flex; gap: 10px;">
                <input type="text" name="message" required placeholder="Type your message here..." style="flex: 1;">
                <button type="submit" class="btn" style="margin: 0;">Send</button>
            </div>
        </form>
    </div>
@endsection