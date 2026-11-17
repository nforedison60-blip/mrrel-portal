<x-guest-layout>
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h3 style="text-align: center; font-size: 24px; margin-bottom: 20px;">Create your MrRel Account</h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="f">
                <label class="k">Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                </label>
                @error('name') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <!-- Email -->
            <div class="f">
                <label class="k">Email Address
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                </label>
                @error('email') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <!-- Role Selection -->
            <div class="f">
                <label class="k">I want to join as a:
                    <select name="role" required>
                        <option value="Customer" {{ old('role') == 'Customer' ? 'selected' : '' }}>Customer (I need a service)</option>
                        <option value="Provider" {{ old('role') == 'Provider' ? 'selected' : '' }}>Provider (I offer services)</option>
                        <option value="Landlord" {{ old('role') == 'Landlord' ? 'selected' : '' }}>Landlord (I have properties)</option>
                    </select>
                </label>
                @error('role') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <!-- Password -->
            <div class="f">
                <label class="k">Password
                    <input type="password" name="password" required autocomplete="new-password">
                </label>
                @error('password') <div class="m sm" style="color: red;">{{ $message }}</div> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="f">
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