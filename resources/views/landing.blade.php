<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MrRel: movers and rentals in one place</title>
    <meta name="description" content="Compare quotes from movers, follow your move live and find a place to rent. MrRel brings it together.">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--n:#10243A;--t:#0E7C86;--tl:#E3F1F2;--td:#0A5C64;--a:#F4B731;--m:#51657A;--l:#D5DEE6;--bg:#F5F8FA}
        *{box-sizing:border-box;margin:0}
        body{font:16px/1.55 Figtree,system-ui,sans-serif;color:var(--n);background:var(--bg)}
        h1,h2,h3,.logo{font-family:'Bricolage Grotesque',Figtree,sans-serif;letter-spacing:-.015em}
        a{color:inherit}
        .wrap{max-width:1140px;margin:0 auto;padding:0 24px}
        .nav{position:sticky;top:0;z-index:30;background:rgba(245,248,250,.78);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);border-bottom:1px solid rgba(213,222,230,.7)}
        .nav .wrap{display:flex;align-items:center;justify-content:space-between;height:68px;gap:16px}
        .logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:26px;text-decoration:none}
        .logo i{width:20px;height:20px;border-radius:5px;background:var(--a)}
        .links{display:flex;align-items:center;gap:6px}
        .links a.l{text-decoration:none;font-weight:600;font-size:15px;padding:8px 14px;border-radius:99px;transition:background .25s}
        .links a.l:hover{background:rgba(14,124,134,.1)}
        .btn{display:inline-block;text-decoration:none;font:700 15px Figtree;padding:12px 24px;border-radius:99px;border:0;cursor:pointer;color:var(--n);background:var(--a);margin:0}
        .btn.g{background:#fff;border:1.5px solid var(--l)}
        .hero-l{padding:64px 0 40px}
        .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center}
        .chipx{display:inline-block;background:var(--tl);color:var(--td);font-weight:700;font-size:13px;padding:6px 14px;border-radius:99px;margin-bottom:18px}
        h1.hd{font-size:clamp(38px,5.4vw,64px);line-height:1.02;font-weight:800;margin-bottom:18px}
        h1.hd span{background:linear-gradient(120deg,#0E7C86,#F4B731);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
        .lead{font-size:19px;color:var(--m);max-width:520px;margin-bottom:28px}
        .cta{display:flex;gap:12px;flex-wrap:wrap}
        .cta .btn{padding:15px 30px;font-size:16px}
        /* floating illustration */
        .stack{position:relative;height:430px;perspective:1000px}
        .mock{position:absolute;background:#fff;border:1px solid rgba(213,222,230,.9);border-radius:20px;padding:18px 20px;box-shadow:0 8px 16px rgba(16,36,58,.07),0 30px 60px rgba(16,36,58,.16);translate:calc(var(--px,0)*var(--k)*1px) calc(var(--py,0)*var(--k)*1px);animation:fl 7s ease-in-out infinite}
        .mock small{display:block;color:var(--m);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.06em}
        .mock b{display:block;font:800 28px 'Bricolage Grotesque';margin:2px 0 8px}
        .pill{display:inline-block;background:var(--tl);color:var(--td);font-weight:700;font-size:12px;padding:4px 12px;border-radius:99px}
        .pill.y{background:#FDF1CC;color:#5C4300}
        .m1{--k:14;top:6px;left:2%;width:250px;transform:rotate(-4deg)}
        .m2{--k:26;top:120px;right:0;width:300px;transform:rotate(3deg);animation-delay:-2.3s;background:linear-gradient(145deg,#0E7C86,#10243A);color:#fff;border-color:transparent}
        .m2 small{color:rgba(255,255,255,.7)}
        .m2 .pill{background:rgba(255,255,255,.16);color:#fff}
        .m3{--k:38;bottom:6px;left:12%;width:270px;transform:rotate(-2deg);animation-delay:-4.6s}
        .route{display:block;margin:6px 0 10px}
        .dot{animation:ping 1.8s ease-out infinite}
        .ill{position:absolute;right:6px;bottom:-4px;font-size:11px;color:var(--m);opacity:.7}
        @keyframes fl{0%,100%{margin-top:0}50%{margin-top:-14px}}
        @keyframes ping{0%{r:5;opacity:1}100%{r:16;opacity:0}}
        section{padding:56px 0}
        h2.sh{font-size:clamp(28px,3.6vw,42px);text-align:center;font-weight:800;margin-bottom:10px}
        .sub{text-align:center;color:var(--m);max-width:560px;margin:0 auto 36px}
        .tri{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
        .card{padding:26px}
        .card h3{font-size:22px;margin:14px 0 6px}
        .card ul{list-style:none;padding:0;margin:12px 0 20px;color:var(--m)}
        .card li{padding:5px 0 5px 26px;position:relative}
        .card li::before{content:"";position:absolute;left:0;top:11px;width:12px;height:12px;border-radius:50%;background:linear-gradient(145deg,#14A0AC,#0E7C86);box-shadow:0 2px 6px rgba(14,124,134,.4)}
        .ic{width:54px;height:54px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(145deg,#FFE08A,#F4B731);box-shadow:0 10px 20px rgba(244,183,49,.4),inset 0 1px 0 rgba(255,255,255,.7)}
        .ic.t{background:linear-gradient(145deg,#14A0AC,#0E7C86);box-shadow:0 10px 20px rgba(14,124,134,.4),inset 0 1px 0 rgba(255,255,255,.3)}
        .ic.t svg{stroke:#fff}
        .ic svg{width:26px;height:26px;fill:none;stroke:var(--n);stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
        .step{padding:22px}
        .num{display:grid;place-items:center;width:38px;height:38px;border-radius:50%;background:linear-gradient(145deg,#1B3A58,#10243A);color:#fff;font:800 17px 'Bricolage Grotesque';margin-bottom:12px;box-shadow:0 8px 16px rgba(16,36,58,.3)}
        .step h3{font-size:18px;margin-bottom:4px}.step p{color:var(--m);font-size:15px}
        .band{margin:20px 0 70px;border-radius:28px;padding:56px 32px;text-align:center;color:#fff;position:relative;overflow:hidden;background:radial-gradient(520px 260px at 90% 0%,rgba(244,183,49,.4),transparent 60%),linear-gradient(135deg,#0E7C86,#10243A);box-shadow:0 30px 60px rgba(16,36,58,.28)}
        .band h2{font-size:clamp(28px,3.6vw,42px);margin-bottom:10px}.band p{opacity:.85;margin-bottom:24px}
        footer{padding:30px 0 50px;color:var(--m);font-size:14px;text-align:center}
        footer a{font-weight:600;text-decoration:none;margin:0 8px}
        @media(max-width:900px){.hero-grid,.tri,.steps{grid-template-columns:1fr}.stack{height:420px;max-width:420px;margin:0 auto;width:100%}.m2{width:260px}.links a.l{display:none}.hero-l{padding-top:36px}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/mrrel-ui.css') }}?v={{ @filemtime(public_path('css/mrrel-ui.css')) }}">
    <script defer src="{{ asset('js/mrrel-ui.js') }}?v={{ @filemtime(public_path('js/mrrel-ui.js')) }}"></script>
</head>
<body>
    <header class="nav">
        <div class="wrap">
            <a href="{{ url('/') }}" class="logo"><i></i>MrRel</a>
            <nav class="links">
                <a class="l" href="#who">Who it is for</a>
                <a class="l" href="#how">How it works</a>
                <a class="l" href="{{ route('login') }}">Log in</a>
                <a class="btn" href="{{ route('register') }}">Sign up</a>
            </nav>
        </div>
    </header>

    <div class="hero-l" id="hero">
        <div class="wrap hero-grid">
            <div class="rvl">
                <span class="chipx">Movers and rentals, built for South Africa</span>
                <h1 class="hd">Find a mover. <span>Find a home.</span> Move in with confidence.</h1>
                <p class="lead">Compare quotes from movers and follow your move live. All in one place.</p>
                <div class="cta">
                    <a class="btn" href="{{ route('register') }}">Get started</a>
                    <a class="btn g" href="{{ route('login') }}">Log in</a>
                </div>
            </div>
            <div class="stack" aria-hidden="true">
                <div class="mock m1"><small>Quote</small><b>R 2 450</b><span class="pill">Fixed price</span></div>
                <div class="mock m2">
                    <small>Live tracking</small>
                    <svg class="route" viewBox="0 0 260 60" width="100%" height="60"><path d="M10 48 C 70 4, 130 60, 190 22 S 244 14 250 12" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="3" stroke-dasharray="6 7" stroke-linecap="round"/><circle cx="10" cy="48" r="6" fill="#F4B731"/><circle class="dot" cx="250" cy="12" r="5" fill="#fff"/><circle cx="250" cy="12" r="5" fill="#fff"/></svg>
                    <span class="pill">On the way</span>
                </div>
                <div class="mock m3"><small>Rental</small><b>2 bedroom flat</b><span class="pill y">Viewing requested</span></div>
                <span class="ill">Illustration</span>
            </div>
        </div>
    </div>

    <section id="who">
        <div class="wrap">
            <h2 class="sh rvl">One place for everyone in a move</h2>
            <p class="sub rvl">Whether you are moving, driving the truck or letting a property, MrRel has a place for you.</p>
            <div class="tri">
                <div class="card rvl">
                    <div class="ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.6 3-6 7-6s7 2.4 7 6"/></svg></div>
                    <h3>Customers</h3>
                    <ul><li>Request a move and get quotes</li><li>Pay by instant EFT</li><li>Follow your truck live</li><li>Search places to rent</li></ul>
                    <a class="btn" href="{{ route('register') }}">Start a move</a>
                </div>
                <div class="card rvl">
                    <div class="ic t"><svg viewBox="0 0 24 24"><path d="M2 6h11v10H2z"/><path d="M13 9h4.5L21 12.5V16h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg></div>
                    <h3>Movers</h3>
                    <ul><li>See open move requests</li><li>Send quotes to customers</li><li>Get a verified badge</li><li>Share your location on the job</li></ul>
                    <a class="btn g" href="{{ route('register') }}">Join as a mover</a>
                </div>
                <div class="card rvl">
                    <div class="ic"><svg viewBox="0 0 24 24"><path d="M5 21V4h9v17"/><path d="M14 9h5v12"/><path d="M8 8h3M8 12h3M8 16h3"/></svg></div>
                    <h3>Landlords</h3>
                    <ul><li>List your properties</li><li>MrRel reviews each listing</li><li>Receive viewing requests</li><li>Match with tenants</li></ul>
                    <a class="btn g" href="{{ route('register') }}">List a property</a>
                </div>
            </div>
        </div>
    </section>

    <section id="how">
        <div class="wrap">
            <h2 class="sh rvl">How a move works</h2>
            <p class="sub rvl">Four simple steps, from the first request to moving day.</p>
            <div class="steps">
                <div class="card step rvl"><span class="num">1</span><h3>Request</h3><p>Tell us what you are moving, where from and where to.</p></div>
                <div class="card step rvl"><span class="num">2</span><h3>Compare</h3><p>Movers who MrRel has checked send you their quotes.</p></div>
                <div class="card step rvl"><span class="num">3</span><h3>Book and pay</h3><p>Pick a quote and pay online by instant EFT.</p></div>
                <div class="card step rvl"><span class="num">4</span><h3>Track</h3><p>Follow your move live and message your mover.</p></div>
            </div>
        </div>
    </section>

    <div class="wrap">
        <div class="band rvl">
            <h2>Ready to make your move?</h2>
            <p>Create a free account in a minute.</p>
            <a class="btn" href="{{ route('register') }}">Create my account</a>
        </div>
    </div>

    <footer>
        <div class="wrap">
            <a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Sign up</a>
            <div style="margin-top:10px;">&copy; {{ date('Y') }} MrRel</div>
        </div>
    </footer>

    <script>
        (function () {
            var hero = document.getElementById('hero'), stack = hero && hero.querySelector('.stack');
            if (!stack || !window.matchMedia('(hover:hover)').matches || window.matchMedia('(prefers-reduced-motion:reduce)').matches) return;
            hero.addEventListener('mousemove', function (e) {
                var r = hero.getBoundingClientRect();
                stack.style.setProperty('--px', ((e.clientX - r.left) / r.width - .5) * 2);
                stack.style.setProperty('--py', ((e.clientY - r.top) / r.height - .5) * 2);
            });
        })();
    </script>
</body>
</html>
