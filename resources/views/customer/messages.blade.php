@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 200px); /* Adjust based on header */
        max-width: 800px;
        background: #EFEAE2; /* WhatsApp-style background */
        border-radius: 12px;
        border: 1px solid var(--l);
        overflow: hidden;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .msg-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        animation: popIn 0.3s ease-out;
    }
    @keyframes popIn {
        0% { opacity: 0; transform: scale(0.95) translateY(5px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .msg-mine {
        background: #DCF8C6;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    .msg-theirs {
        background: #FFFFFF;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        border: 1px solid #E5E5E5;
    }
    .msg-time {
        font-size: 10px;
        color: #999;
        margin-top: 4px;
        text-align: right;
        display: block;
    }
    .msg-sender {
        font-size: 12px;
        font-weight: bold;
        color: #0E7C86;
        margin-bottom: 2px;
    }
    .chat-input-area {
        display: flex;
        gap: 10px;
        padding: 15px;
        background: #F0F2F5;
        border-top: 1px solid var(--l);
    }
    .chat-input-area input {
        flex: 1;
        margin: 0;
        border-radius: 24px;
        padding: 12px 20px;
        border: 1px solid #ccc;
    }
    .chat-input-area button {
        margin: 0;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

<h1>Messages</h1>
<div class="m" style="margin-bottom: 20px;">Chat with your provider in real-time.</div>

<div class="card" style="padding: 0; border: none; background: transparent;">
    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                @if($msg->user_id == auth()->id())
                    <div class="msg-bubble msg-mine">
                        <div>{{ $msg->message }}</div>
                        <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                @else
                    <div class="msg-bubble msg-theirs">
                        <div class="msg-sender">{{ $msg->sender_name }}</div>
                        <div>{{ $msg->message }}</div>
                        <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                @endif
            @empty
                <div class="m sm" style="text-align: center; padding: 40px;">No messages yet. Say hello!</div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('customer.messages.store') }}" class="chat-input-area">
            @csrf
            <input type="text" name="message" required placeholder="Type a message..." autocomplete="off">
            <button type="submit" class="btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto-scroll to the bottom of the chat on page load
    const chatContainer = document.getElementById('chatMessages');
    chatContainer.scrollTop = chatContainer.scrollHeight;
</script>
@endsection