<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MrRel Portal - Provider</title>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--n:#10243A;--t:#0E7C86;--tl:#E3F1F2;--td:#0A5C64;--a:#F4B731;--m:#51657A;--l:#D5DEE6;--bg:#F5F8FA;--wn:#FDF1CC}
        *{box-sizing:border-box;margin:0}
        body{font:15px/1.5 Figtree,system-ui,sans-serif;color:var(--n);background:var(--bg)}
        h1,h2,h3,.big{font-family:'Bricolage Grotesque',Figtree,sans-serif;letter-spacing:-.01em}
        h1{font-size:30px}h3{font-size:17px;margin-bottom:8px}
        .top{background:var(--n);color:#fff;min-height:58px;display:flex;align-items:center;justify-content:space-between;padding:8px 24px;gap:12px;flex-wrap:wrap;position:sticky;top:0;z-index:20}
        .logo{display:flex;align-items:center;gap:10px;font:800 24px 'Bricolage Grotesque'}.logo i{width:18px;height:18px;border-radius:4px;background:var(--a)}
        .sw{display:flex;gap:4px;background:rgba(255,255,255,.1);padding:4px;border-radius:99px}
        .sw a{border:0;background:none;color:#fff;font:600 13px Figtree;padding:7px 14px;border-radius:99px;cursor:pointer;transition:.25s;text-decoration:none;}
        .sw a.on{background:var(--a);color:var(--n)}
        .bd{display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - 58px)}
        .sd{background:#fff;border-right:1px solid var(--l);padding:16px 0;position:sticky;top:58px;height:calc(100vh - 58px);overflow:auto}
        .sd a{display:block;padding:9px 22px;color:var(--n);text-decoration:none;font-weight:500;border-left:4px solid transparent;cursor:pointer;transition:.2s}
        .sd a:hover{background:var(--bg)}.sd a.on{border-left-color:var(--t);background:var(--tl);color:var(--td);font-weight:700}
        .mn{padding:26px 30px 60px;min-width:0;animation:up .45s both}
        @keyframes up{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
        .card{background:#fff;border:1px solid var(--l);border-radius:14px;padding:16px 18px;transition:.25s;animation:up .5s both}
        .card:hover{box-shadow:0 8px 22px rgba(16,36,58,.08)}
        .g2,.g3,.g4,.sp{display:grid;gap:16px;margin:16px 0}.g2{grid-template-columns:repeat(2,1fr)}.g3{grid-template-columns:repeat(3,1fr)}.g4{grid-template-columns:repeat(4,1fr)}.sp{grid-template-columns:2fr 1fr}
        .m{color:var(--m)}.sm{font-size:14px}.big{font-weight:800;font-size:28px;line-height:1.1}
        .chip{display:inline-block;border:1px solid var(--l);border-radius:99px;padding:3px 12px;margin:0 6px 6px 0;background:#fff;font-size:13px;font-weight:600}
        .ok{background:var(--tl);color:var(--td);border-color:var(--tl)}.wn{background:var(--wn);color:#5C4300;border-color:var(--wn)}
        .btn{display:inline-block;background:var(--a);color:var(--n);font:700 14px Figtree;padding:10px 18px;border-radius:99px;border:0;cursor:pointer;margin:0 8px 8px 0;transition:.2s}
        .btn:hover{transform:translateY(-2px);box-shadow:0 6px 14px rgba(244,183,49,.4)}.btn.g{background:#fff;border:2px solid var(--l);padding:8px 16px;box-shadow:none}.btn.w{width:100%;margin:0 0 8px}
        .f{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:10px 0}.f label{display:block;font:600 13px Figtree}.f .k{grid-column:1/-1;font-weight:500}
        input,select,textarea{display:block;width:100%;margin-top:4px;border:1px solid #B9C6D2;border-radius:10px;padding:9px 12px;font:inherit;background:#fff;color:var(--n);outline:0;transition:.2s}
        input:focus,select:focus,textarea:focus{border-color:var(--t);box-shadow:0 0 0 3px rgba(14,124,134,.18)}input[type=checkbox],input[type=radio]{display:inline;width:auto;margin:0 6px 0 0}
        .tb{width:100%;border-collapse:collapse;font-size:14px}.tb th{text-align:left;padding:7px 8px;border-bottom:2px solid var(--l)}.tb td{padding:7px 8px;border-bottom:1px solid #EEF2F5;vertical-align:top}.tb td:last-child.r{text-align:right}
        .av{width:44px;height:44px;border-radius:50%;background:var(--a);display:inline-flex;align-items:center;justify-content:center;font-weight:700;flex:none}
        .row{display:flex;gap:12px;align-items:center}.ph{background:#EEF2F5;border:2px dashed #B9C6D2;border-radius:12px;color:var(--m);text-align:center;padding:30px 14px;margin-bottom:8px}
        ul{margin:6px 0 0;padding-left:20px}li{margin-bottom:3px}
        .st{position:sticky;top:78px}.tot{border:2px solid var(--t)}
        .pr{display:grid;grid-template-columns:1fr 130px;gap:10px;align-items:center;padding:6px 0;border-bottom:1px solid #EEF2F5}
        .pl{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0}.pl label{display:flex;gap:6px;align-items:center;border:1px solid var(--l);border-radius:99px;padding:5px 12px;font-size:14px;font-weight:500;cursor:pointer;margin:0}
        .star{cursor:pointer}.star.on{background:var(--a);border-color:var(--a)}
        #toast{position:fixed;bottom:24px;left:50%;transform:translate(-50%,80px);background:var(--n);color:#fff;padding:12px 22px;border-radius:99px;font-weight:600;transition:.4s;z-index:50;opacity:0}#toast.s{transform:translate(-50%,0);opacity:1}
        @media(max-width:860px){.bd{grid-template-columns:1fr}.sd{position:fixed;bottom:0;left:0;right:0;top:auto;height:auto;display:flex;overflow-x:auto;padding:0;border-top:1px solid var(--l);z-index:30}.sd a{white-space:nowrap;border-left:0;border-top:3px solid transparent;padding:12px 14px}.sd a.on{border-top-color:var(--t)}.g2,.g3,.g4,.sp,.f{grid-template-columns:1fr}.mn{padding:20px 16px 90px}.st{position:static}}
    </style>
</head>
<body>
    <div class="top">
        <div class="logo"><i></i>MrRel</div>
        <div class="sw" id="sw">
            <a href="{{ route('customer.dashboard') }}">Customer</a>
            <a href="{{ route('landlord.properties') }}">Landlord</a>
            <a href="{{ route('provider.dashboard') }}" class="on">Provider</a>
            <a href="{{ route('admin.dashboard') }}">Admin</a>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span style="opacity:.85;font-size:13px">Example data</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:none; border:1px solid rgba(255,255,255,0.3); color:#fff; padding:6px 14px; border-radius:99px; cursor:pointer; font:600 13px Figtree;">Log Out</button>
            </form>
        </div>
    </div>

    <div class="bd">
        <nav class="sd" id="sd">
            <a href="{{ route('provider.dashboard') }}" class="{{ request()->routeIs('provider.dashboard') ? 'on' : '' }}">Dashboard</a>
            
            <!-- LIVE TRACKING LINK ADDED HERE -->
            <a href="{{ route('provider.tracking') }}" class="{{ request()->routeIs('provider.tracking') ? 'on' : '' }}">Live Tracking</a>
            
            <a href="{{ route('provider.requests') }}" class="{{ request()->routeIs('provider.requests') ? 'on' : '' }}">Requests</a>
            <a href="{{ route('provider.quotes') }}" class="{{ request()->routeIs('provider.quotes') ? 'on' : '' }}">My quotes</a>
            <a href="{{ route('provider.pbook') }}" class="{{ request()->routeIs('provider.pbook') ? 'on' : '' }}">Bookings and payouts</a>
            <a href="{{ route('provider.messages') }}" class="{{ request()->routeIs('provider.messages') ? 'on' : '' }}">Messages</a>
            <a href="{{ route('provider.reviews') }}" class="{{ request()->routeIs('provider.reviews') ? 'on' : '' }}">Reviews</a>
            <a href="{{ route('provider.rate') }}" class="{{ request()->routeIs('provider.rate') ? 'on' : '' }}">Rate card</a>
            <a href="{{ route('provider.profile') }}" class="{{ request()->routeIs('provider.profile') ? 'on' : '' }}">Profile and verification</a>
            <a href="{{ route('provider.subscription') }}" class="{{ request()->routeIs('provider.subscription') ? 'on' : '' }}">Subscription</a>
            <a href="{{ route('provider.problems') }}" class="{{ request()->routeIs('provider.problems') ? 'on' : '' }}">Problems</a>
        </nav>
        
        <main class="mn" id="mn">
            @if(session('success'))
                <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div id="toast"></div>
</body>
</html>