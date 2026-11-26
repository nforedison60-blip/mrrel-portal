<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MrRel Portal - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--n:#003068;--t:#0A4C9A;--tl:#E6EEF8;--td:#003068;--a:#FF7A1A;--m:#51657A;--l:#D5DEE6;--bg:#F5F8FA}
        *{box-sizing:border-box;margin:0}
        body{font:15px/1.5 Open Sans,system-ui,sans-serif;color:var(--n);background:var(--bg);display:flex;align-items:center;justify-content:center;min-height:100vh}
        .card{background:#fff;border:1px solid var(--l);border-radius:14px;padding:30px;width:100%;max-width:400px;box-shadow:0 8px 22px rgba(0,48,104,.08);}
        .logo{display:flex;align-items:center;justify-content:center;gap:10px;font:800 32px 'Montserrat';margin-bottom:20px;color:var(--n)}
        .logo i{width:24px;height:24px;border-radius:6px;background:var(--a)}
        h2{text-align:center;margin-bottom:20px;font-size:20px;}
        .f label{display:block;font:600 13px Open Sans;margin-bottom:12px;}
        input{display:block;width:100%;margin-top:4px;border:1px solid #B9C6D2;border-radius:10px;padding:9px 12px;font:inherit;background:#fff;color:var(--n);outline:0;transition:.2s}
        input:focus{border-color:var(--t);box-shadow:0 0 0 3px rgba(10,76,154,.18)}
        .btn{display:block;width:100%;background:var(--a);color:var(--n);font:700 15px Open Sans;padding:12px;border-radius:99px;border:0;cursor:pointer;margin-top:20px;transition:.2s;text-align:center;text-decoration:none;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 6px 14px rgba(255,122,26,.4)}
        .links{text-align:center;margin-top:16px;font-size:14px;}
        .links a{color:var(--t);text-decoration:none;font-weight:600;}
        .m{color:var(--m);font-size:14px;text-align:center;margin-bottom:20px;}
    </style>
    <link rel="stylesheet" href="{{ asset('css/mrrel-ui.css') }}?v={{ @filemtime(public_path('css/mrrel-ui.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mrrel-brand.css') }}?v={{ @filemtime(public_path('css/mrrel-brand.css')) }}">
    <script defer src="{{ asset('js/mrrel-ui.js') }}?v={{ @filemtime(public_path('js/mrrel-ui.js')) }}"></script>
</head>
<body>
    <div class="card">
        <div class="logo"><img src="{{ asset('images/mrrel-logo.png') }}" alt="MrRel"></div>
        
        {{ $slot }}
        
    </div>
</body>
</html>