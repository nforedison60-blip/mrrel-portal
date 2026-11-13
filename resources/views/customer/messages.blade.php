@extends('layouts.app')

@section('content')
<style>
    .chat-wrapper {
        max-width: 800px;
        background: #F5F8FA; /* Brand light background */
        border-radius: 12px;
        border: 1px solid var(--l);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        position: relative;
    }
    .chat-header {
        background: var(--n); /* Brand dark blue */
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .chat-header .dot {
        width: 10px;
        height: 10px;
        background: #10b981; /* Green dot for online */
        border-radius: 50%;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background-image: radial-gradient(#E3F1F2 1px, transparent 1px);
        background-size: 20px 20px; /* Creative dotted background */
    }
    .msg-row {
        display: flex;
        width: 100%;
    }
    .msg-row.mine {
        justify-content: flex-end;
    }
    .msg-row.theirs {
        justify-content: flex-start;
    }
    .msg-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        animation: popIn 0.2s ease-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    @keyframes popIn {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    /* SENDER (Orange brand color) */
    .msg-bubble.mine {
        background: var(--a); /* Brand Orange */
        color: var(--n); /* Dark text for contrast */
        border-bottom-right-radius: 4px;
    }
    /* RECEIVER (Blue brand color) */
    .msg-bubble.theirs {
        background: var(--t); /* Brand Teal/Blue */
        color: white;
        border-bottom-left-radius: 4px;
    }
    .msg-sender-name {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
        opacity: 0.8;
    }
    .msg-bubble.mine .msg-sender-name { color: var(--n); }
    .msg-bubble.theirs .msg-sender-name { color: #E3F1F2; }
    
    .msg-time {
        font-size: 10px;
        margin-top: 6px;
        text-align: right;
        opacity: 0.7;
        display: block;
    }
    .chat-input-area {
        display: flex;
        gap: 10px;
        padding: 15px;
        background: white;
        border-top: 1px solid var(--l);
    }
    .chat-input-area input {
        flex: 1;
        margin: 0;
        border-radius: 24px;
        padding: 12px 20px;
        border: 1px solid var(--l);
        background: var(--bg);
    }
    .chat-input-area input:focus {
        border-color: var(--a);
        box-shadow: 0 0 0 3px rgba(244, 183, 49, 0.2);
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
        background: var(--a);
        color: var(--n);
    }
</style>

<h1>Messages</h1>
<div class="m" style="margin-bottom: 20px;">Chat with your provider in real-time.</div>

<div class="chat-wrapper">
    <div class="chat-header">
        <div class="dot"></div> Provider Chat
    </div>

    <div class="chat-messages" id="chatMessages">
        @forelse($messages as $msg)
            <div class="msg-row {{ $msg->user_id == auth()->id() ? 'mine' : 'theirs' }}">
                <div class="msg-bubble {{ $msg->user_id == auth()->id() ? 'mine' : 'theirs' }}">
                    <div class="msg-sender-name">
                        {{ $msg->user_id == auth()->id() ? 'You' : $msg->sender_name }}
                    </div>
                    <div>{{ $msg->message }}</div>
                    <span class="msg-time">{{ $msg->created_at->format('H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="m sm" style="text-align: center; padding: 40px; color: var(--m);">No messages yet. Say hello!</div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('customer.messages.store') }}" class="chat-input-area">
        @csrf
        <input type="text" name="message" required placeholder="Type a message..." autocomplete="off">
        <button type="submit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        </button>
    </form>
</div>

<script>
    const chatContainer = document.getElementById('chatMessages');
    chatContainer.scrollTop = chatContainer.scrollHeight;
</script>
@endsection