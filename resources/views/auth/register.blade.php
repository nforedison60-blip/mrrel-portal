<x-guest-layout>
    <div class="card" style="max-width: 500px; margin: 40px auto; padding: 30px;">
        <h3 style="text-align: center; font-size: 24px; margin-bottom: 20px;">Create your MrRel Account</h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            @php
                // Pre-select from a link such as /register?role=Provider, but never guess when nothing is given
                $chosen = old('role', request('role'));
                $chosen = in_array($chosen, ['Customer', 'Provider', 'Landlord'], true) ? $chosen : null;
            @endphp
            <style>
                .roles{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:0 0 6px}
                .role-opt{position:relative;display:block;cursor:pointer;margin:0}
                .role-opt input{position:absolute;opacity:0;inset:0;width:100%;height:100%;margin:0;cursor:pointer}
                .ro-box{display:flex;flex-direction:column;align-items:center;text-align:center;gap:4px;height:100%;padding:16px 8px 14px;border:2px solid var(--l);border-radius:16px;background:#fff;transition:transform .25s,border-color .2s,background .2s,box-shadow .25s}
                .ro-box svg{width:30px;height:30px;fill:none;stroke:var(--t);stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;margin-bottom:2px}
                .ro-box b{font-size:15px}
                .ro-box small{color:var(--m);font-size:12px;line-height:1.3}
                .role-opt:hover .ro-box{border-color:#9CC9CD;transform:translateY(-2px)}
                .role-opt input:focus-visible + .ro-box{outline:3px solid rgba(14,124,134,.35);outline-offset:2px}
                .role-opt input:checked + .ro-box{border-color:var(--t);background:linear-gradient(180deg,#F1F9FA,#E3F1F2);box-shadow:0 10px 22px rgba(14,124,134,.22)}
                .role-opt input:checked + .ro-box::after{content:"\2713";position:absolute;top:8px;right:10px;width:22px;height:22px;border-radius:50%;background:var(--t);color:#fff;font-size:13px;font-weight:700;display:grid;place-items:center}
                @media(max-width:520px){
                    .roles{grid-template-columns:1fr}
                    .ro-box{flex-direction:row;text-align:left;gap:12px;padding:12px 14px}
                    .ro-box svg{margin:0;flex:none}
                }
            </style>

            <div style="margin-bottom: 18px;">
                <div style="font-weight: 600; margin-bottom: 8px;">First, who are you joining as?</div>
                <div class="roles" role="radiogroup" aria-label="Account type">
                    <label class="role-opt">
                        <input type="radio" name="role" value="Customer" required {{ $chosen === 'Customer' ? 'checked' : '' }}>
                        <span class="ro-box">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.6 3-6 7-6s7 2.4 7 6"/></svg>
                            <span><b>Customer</b><br><small>I need a move</small></span>
                        </span>
                    </label>
                    <label class="role-opt">
                        <input type="radio" name="role" value="Provider" required {{ $chosen === 'Provider' ? 'checked' : '' }}>
                        <span class="ro-box">
                            <svg viewBox="0 0 24 24"><path d="M2 6h11v10H2z"/><path d="M13 9h4.5L21 12.5V16h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>
                            <span><b>Mover</b><br><small>I offer moving services</small></span>
                        </span>
                    </label>
                    <label class="role-opt">
                        <input type="radio" name="role" value="Landlord" required {{ $chosen === 'Landlord' ? 'checked' : '' }}>
                        <span class="ro-box">
                            <svg viewBox="0 0 24 24"><path d="M5 21V4h9v17"/><path d="M14 9h5v12"/><path d="M8 8h3M8 12h3M8 16h3"/></svg>
                            <span><b>Landlord</b><br><small>I have properties to let</small></span>
                        </span>
                    </label>
                </div>
                <div class="m sm">Choose carefully. This decides which portal you get, and you cannot change it yourself later.</div>
                @error('role') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <div class="f" style="margin-bottom: 16px;">
                <label class="k">Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                </label>
                @error('name') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <div class="f" style="margin-bottom: 16px;">
                <label class="k">Email Address
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                </label>
                @error('email') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <div class="f" style="margin-bottom: 16px;">
                <label class="k">Password
                    <input type="password" name="password" required autocomplete="new-password">
                </label>
                @error('password') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <div class="f" style="margin-bottom: 16px;">
                <label class="k">Confirm Password
                    <input type="password" name="password_confirmation" required autocomplete="new-password">
                </label>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('login') }}" style="font-size: 14px; color: var(--t); text-decoration: none;">Already registered?</a>
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
    </div>
</x-guest-layout>