@props(['title', 'sub' => null, 'steps' => [], 'optional' => null, 'summary' => 'See the full details'])
@php
    $icons = [
        'pencil' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'unlock' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 7.5-2"/>',
        'home'   => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'card'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'camera' => '<path d="M4 8h3l2-3h6l2 3h3v11H4Z"/><circle cx="12" cy="13" r="3.5"/>',
        'handshake' => '<path d="m11 17 2 2a1 1 0 0 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 0 0 3-3L16 9.5"/><path d="M3 11l5-5 5 1 3-2 5 5"/>',
    ];
@endphp
<style>
    .proc{background:#fff;border:1px solid var(--l);border-radius:22px;padding:24px 28px;margin:16px 0;box-shadow:0 10px 30px rgba(0,48,104,.07)}
    .proc h3{font-size:22px;margin:0}
    .proc .psub{color:var(--m);margin:2px 0 20px}
    .proc .psteps{display:grid;grid-template-columns:repeat(var(--n-steps),1fr);gap:0;list-style:none;margin:0;padding:0}
    .proc .pstep{position:relative;padding-right:18px}
    .proc .pstep:not(:last-child)::after{content:"";position:absolute;top:28px;left:70px;right:6px;border-top:2px dashed #FFB070}
    .proc .pic{position:relative;width:56px;height:56px;border-radius:50%;background:#E6EEF8;color:var(--n);display:flex;align-items:center;justify-content:center;margin-bottom:12px}
    .proc .pic svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .proc .pic b{position:absolute;top:-4px;right:-4px;width:22px;height:22px;border-radius:50%;background:var(--n);color:#fff;font:700 12px Montserrat,sans-serif;display:flex;align-items:center;justify-content:center}
    .proc .pstep h4{font:700 16px Montserrat,sans-serif;color:var(--n);margin:0 0 3px}
    .proc .pstep p{font-size:14px;color:var(--m);margin:0}
    .proc .chip2{display:inline-block;margin-top:8px;background:#FFF0E5;border:1px solid #FFB070;color:#8A3A00;font:700 12px 'Open Sans',sans-serif;padding:3px 12px;border-radius:99px}
    .proc .popt{display:flex;flex-wrap:wrap;align-items:center;gap:8px 12px;margin-top:20px;background:#F3F7FC;border:1px solid var(--l);border-radius:12px;padding:12px 16px;font-size:15px}
    .proc .popt .tag{background:var(--n);color:#fff;font:700 12px Montserrat,sans-serif;letter-spacing:.08em;text-transform:uppercase;padding:4px 12px;border-radius:99px}
    .proc .popt strong{color:var(--n)}
    .proc details{margin-top:14px}
    .proc summary{cursor:pointer;font-weight:700;color:var(--n);padding:6px 0}
    .proc .pfull{margin:10px 0 0 20px;line-height:1.7}
    @media(max-width:760px){
        .proc{padding:18px 16px;border-radius:18px}
        .proc .psteps{grid-template-columns:1fr;gap:18px}
        .proc .pstep{display:grid;grid-template-columns:56px 1fr;column-gap:14px;padding-right:0}
        .proc .pstep::after{display:none}
        .proc .pic{margin:0;grid-row:1 / span 3}
    }
</style>
<div class="proc">
    <h3>{{ $title }}</h3>
    @if($sub)<p class="psub">{{ $sub }}</p>@endif
    <ol class="psteps" style="--n-steps: {{ count($steps) }}">
        @foreach($steps as $i => $s)
            <li class="pstep">
                <span class="pic"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $icons[$s['icon']] ?? $icons['home'] !!}</svg><b>{{ $i + 1 }}</b></span>
                <div>
                    <h4>{{ $s['title'] }}</h4>
                    <p>{{ $s['text'] }}</p>
                    @if(!empty($s['chip']))<span class="chip2">{{ $s['chip'] }}</span>@endif
                </div>
            </li>
        @endforeach
    </ol>
    @if($optional)
        <div class="popt"><span class="tag">Optional</span><strong>{{ $optional['title'] }}</strong><span>{{ $optional['text'] }}</span></div>
    @endif
    @if(trim((string) $slot) !== '')
        <details>
            <summary>{{ $summary }}</summary>
            {{ $slot }}
        </details>
    @endif
</div>
