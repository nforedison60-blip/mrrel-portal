<x-guest-layout>
    @php
        // Customer is pre-selected. A link such as /register?role=Provider pre-selects that type instead.
        $chosen = old('role', request('role'));
        $chosen = in_array($chosen, ['Customer', 'Provider', 'Landlord'], true) ? $chosen : 'Customer';
    @endphp

    <style>
        .logo{display:none}
        .card{max-width:480px;padding:28px 28px 24px}
        .rg-head{text-align:center;margin-bottom:22px}
        .rg-head .mark{width:34px;height:34px;fill:none;stroke:var(--n);stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;margin-bottom:10px}
        .rg-head h1{font:800 24px 'Montserrat',Open Sans,sans-serif;margin:0 0 4px}
        .rg-head p{color:var(--m);font-size:14px;margin:0}
        .rg-label{font:600 14px Open Sans,sans-serif;margin:0 0 8px;display:block}
        .roles{display:grid;gap:10px;margin:0 0 18px}
        .role-opt{position:relative;display:block;cursor:pointer;margin:0}
        .role-opt input{position:absolute;opacity:0;inset:0;width:100%;height:100%;margin:0;cursor:pointer}
        .ro-box{display:flex;align-items:center;gap:12px;padding:12px 14px;border:1.5px solid var(--l);border-radius:12px;background:#fff;transition:border-color .2s,background .2s}
        .ro-ico{flex:none;width:40px;height:40px;border-radius:10px;background:var(--bg);display:grid;place-items:center;transition:background .2s}
        .ro-ico svg{width:22px;height:22px;fill:none;stroke:var(--n);stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;transition:stroke .2s}
        .ro-txt{flex:1;line-height:1.3}
        .ro-txt b{display:block;font-size:15px}
        .ro-txt small{color:var(--m);font-size:13px}
        .ro-tick{flex:none;width:24px;height:24px;border-radius:50%;border:1.5px solid var(--l);display:grid;place-items:center;transition:background .2s,border-color .2s}
        .ro-tick svg{width:14px;height:14px;fill:none;stroke:var(--n);stroke-width:3;stroke-linecap:round;stroke-linejoin:round;opacity:0}
        .role-opt:hover .ro-box{border-color:#EBCB78}
        .role-opt input:focus-visible + .ro-box{outline:3px solid rgba(255,122,26,.5);outline-offset:2px}
        .role-opt input:checked + .ro-box{border-color:var(--a);background:#FEF6DD}
        .role-opt input:checked + .ro-box .ro-ico{background:var(--a)}
        .role-opt input:checked + .ro-box .ro-ico svg{stroke:var(--n)}
        .role-opt input:checked + .ro-box .ro-tick{background:var(--a);border-color:var(--a)}
        .role-opt input:checked + .ro-box .ro-tick svg{opacity:1}
        .rg-field{margin-bottom:14px}
        .rg-field label{display:block;font:600 14px Open Sans,sans-serif;margin:0}
        .rg-field input{padding:11px 12px;margin-top:6px}
        .rg-hint{color:var(--m);font-size:12.5px;margin-top:5px}
        .rg-err{color:#C0392B;font-size:13px;margin-top:5px}
        .pw{position:relative}
        .pw input{padding-right:44px}
        .pw button{position:absolute;right:6px;top:6px;width:34px;height:34px;border:0;background:none;cursor:pointer;display:grid;place-items:center;border-radius:8px;color:var(--m)}
        .pw button:hover{background:var(--bg)}
        .pw svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .pw .off{display:none}
        .pw.show .on{display:none}
        .pw.show .off{display:block}
        .terms{display:flex;align-items:center;gap:8px;font-size:14px;margin:4px 0 0;cursor:pointer}
        .terms input[type=checkbox]{display:inline-block;width:18px;height:18px;margin:0;padding:0;accent-color:var(--a);flex:none}
        .terms a{color:var(--t);font-weight:600;text-decoration:none}
        .terms a:hover{text-decoration:underline}
        .rg-foot{text-align:center;margin-top:16px;font-size:14px;color:var(--m)}
        .rg-foot a{color:var(--t);font-weight:600;text-decoration:none}
    </style>

    <div class="rg-head">
        <svg class="mark" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11 12 3l9 8"/><path d="M5 9.5V20h14V9.5"/><path d="M12 17.2c-2-1.4-2.8-2.4-2.8-3.5a1.6 1.6 0 0 1 2.8-1 1.6 1.6 0 0 1 2.8 1c0 1.1-.8 2.1-2.8 3.5Z"/></svg>
        <h1>Create your MrRel account</h1>
        <p>It takes less than a minute</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <span class="rg-label" id="joining-as">I'm joining as</span>
        <div class="roles" role="radiogroup" aria-labelledby="joining-as">
            <label class="role-opt">
                <input type="radio" name="role" value="Customer" required {{ $chosen === 'Customer' ? 'checked' : '' }}>
                <span class="ro-box">
                    <span class="ro-ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.6 3-6 7-6s7 2.4 7 6"/></svg></span>
                    <span class="ro-txt"><b>Customer</b><small>I need a service or a place to rent</small></span>
                    <span class="ro-tick"><svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>
                </span>
            </label>
            <label class="role-opt">
                <input type="radio" name="role" value="Provider" required {{ $chosen === 'Provider' ? 'checked' : '' }}>
                <span class="ro-box">
                    <span class="ro-ico"><svg viewBox="0 0 24 24"><path d="M2 6h11v10H2z"/><path d="M13 9h4.5L21 12.5V16h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg></span>
                    <span class="ro-txt"><b>Service provider</b><small>I offer moving services</small></span>
                    <span class="ro-tick"><svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>
                </span>
            </label>
            <label class="role-opt">
                <input type="radio" name="role" value="Landlord" required {{ $chosen === 'Landlord' ? 'checked' : '' }}>
                <span class="ro-box">
                    <span class="ro-ico"><svg viewBox="0 0 24 24"><path d="M5 21V4h9v17"/><path d="M14 9h5v12"/><path d="M8 8h3M8 12h3M8 16h3"/></svg></span>
                    <span class="ro-txt"><b>Landlord</b><small>I have properties to let</small></span>
                    <span class="ro-tick"><svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>
                </span>
            </label>
        </div>
        @error('role') <div class="rg-err" style="margin:-10px 0 14px">{{ $message }}</div> @enderror

        <div class="rg-field">
            <label>Full name
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Thandi Nkosi" required autofocus autocomplete="name">
            </label>
            @error('name') <div class="rg-err">{{ $message }}</div> @enderror
        </div>

        <div class="rg-field">
            <label>Email address
                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="username">
            </label>
            @error('email') <div class="rg-err">{{ $message }}</div> @enderror
        </div>

        <div class="rg-field">
            <label>Mobile number
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="082 123 4567" required autocomplete="tel" inputmode="tel">
            </label>
            @error('phone') <div class="rg-err">{{ $message }}</div> @enderror
        </div>

        <div class="rg-field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" id="password" name="password" placeholder="Create a password" required autocomplete="new-password">
                <button type="button" data-toggle="password" aria-label="Show password">
                    <svg class="on" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7a9.7 9.7 0 0 0 4.2-.9"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
            <div class="rg-hint">At least 8 characters, with a number.</div>
            @error('password') <div class="rg-err">{{ $message }}</div> @enderror
        </div>

        <div class="rg-field">
            <label for="password_confirmation">Confirm password</label>
            <div class="pw">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required autocomplete="new-password">
                <button type="button" data-toggle="password_confirmation" aria-label="Show password">
                    <svg class="on" viewBox="0 0 24 24"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7a9.7 9.7 0 0 0 4.2-.9"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
        </div>

        <label class="terms">
            <input type="checkbox" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }}>
            <span>I agree to the <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms of Use</a> and <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</span>
        </label>
        @error('terms') <div class="rg-err">{{ $message }}</div> @enderror

        <button type="submit" class="btn">Create account</button>

        <div class="rg-foot">Already have an account? <a href="{{ route('login') }}">Log in</a></div>
    </form>

    <script>
        document.querySelectorAll('.pw button[data-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.toggle);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.parentElement.classList.toggle('show', show);
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });
    </script>
</x-guest-layout>
