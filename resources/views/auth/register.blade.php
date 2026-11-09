<x-guest-layout>
    <h2>Create an account</h2>
    <div class="m">Join MrRel today</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="f">
            <label>Name
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            </label>

            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>

            <label>I am registering as a...
                <select name="role" required>
                    <option value="Customer" {{ old('role') == 'Customer' ? 'selected' : '' }}>Customer (Looking for services / a place)</option>
                    <option value="Landlord" {{ old('role') == 'Landlord' ? 'selected' : '' }}>Landlord (Listing a property)</option>
                    <option value="Provider" {{ old('role') == 'Provider' ? 'selected' : '' }}>Provider (Offering moving / services)</option>
                </select>
            </label>

            <label>Password
                <input type="password" name="password" required>
            </label>

            <label>Confirm Password
                <input type="password" name="password_confirmation" required>
            </label>
        </div>

        <button type="submit" class="btn">Register</button>

        <div class="links">
            Already registered? <a href="{{ route('login') }}">Log in here</a>
        </div>
    </form>
</x-guest-layout>