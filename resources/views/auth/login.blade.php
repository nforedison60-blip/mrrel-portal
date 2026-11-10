<x-guest-layout>
    <h2>Welcome back</h2>
    <div class="m">Log in to your MrRel account</div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="f">
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>

            <label>Password
                <input type="password" name="password" required>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
            @if (Route::has('password.request'))
                <a class="links" href="{{ route('password.request') }}" style="font-size: 13px;">
                    Forgot your password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn">Log in</button>

        <div class="links">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </form>
</x-guest-layout>