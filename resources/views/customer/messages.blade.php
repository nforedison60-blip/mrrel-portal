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
        background-image: radial-gradient(#E6EEF8 1px, transparent 1px);
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
    .msg-bubble.theirs .msg-sender-name { color: #E6EEF8; }
    
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
<div class="m" style="margin-bottom: 12px;">Chat with the provider you booked.</div>

@if($providers->isEmpty())
    <div class="card empty">
        <h3>No chat yet</h3>
        <p class="m" style="margin-bottom: 12px;">Your chat opens as soon as you accept a quote and book a provider.</p>
        <a href="{{ route('customer.quotes') }}" class="btn">View my quotes</a>
    </div>
@else
    @if($providers->count() > 1)
        <nav class="stb" aria-label="Your providers">
            @foreach($providers as $p)
                <a href="{{ route('customer.messages', ['with' => $p['id']]) }}" class="{{ $p['id'] == $with ? 'on' : '' }}">{{ $p['name'] }}</a>
            @endforeach
        </nav>
    @endif

    <div class="chat-wrapper">
        <div class="chat-header">
            <div class="dot"></div> {{ $withName ?? 'Provider' }}
        </div>

        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                <div class="msg-row {{ $msg->user_id == auth()->id() ? 'mine' : 'theirs' }}">
                    <div class="msg-bubble {{ $msg->user_id == auth()->id() ? 'mine' : 'theirs' }}">
                        <div class="msg-sender-name">
                            {{ $msg->user_id == auth()->id() ? 'You' : $msg->sender_name }}
                        </div>
                        <div>{{ $msg->message }}</div>
                        <span class="msg-time">{{ $msg->created_at->format('d M, H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="m sm" style="text-align: center; padding: 40px;">No messages yet. Say hello to {{ $withName ?? 'your provider' }}!</div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('customer.messages.store') }}" class="chat-input-area">
            @csrf
            <input type="hidden" name="with" value="{{ $with }}">
            <input type="text" id="chatInput" name="message" required maxlength="1000" placeholder="Type a message..." autocomplete="off">
            <button type="submit" aria-label="Send">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </form>
    </div>

<script>
    (function () {
        var box = document.getElementById('chatMessages');
        if (!box) return;
        box.scrollTop = box.scrollHeight;
        function nearBottom() { return box.scrollHeight - box.scrollTop - box.clientHeight < 80; }
        // Quietly check for new messages every 5 seconds; no page reload, so typing is never interrupted
        setInterval(function () {
            if (document.hidden) return;
            fetch(window.location.href, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.ok ? r.text() : null; })
                .then(function (html) {
                    if (!html) return;
                    var fresh = new DOMParser().parseFromString(html, 'text/html').getElementById('chatMessages');
                    if (!fresh || fresh.innerHTML.trim() === box.innerHTML.trim()) return;
                    var stick = nearBottom();
                    box.innerHTML = fresh.innerHTML;
                    if (stick) box.scrollTop = box.scrollHeight;
                })
                .catch(function () {});
        }, 5000);
    })();
</script>
@endif
@endsection
