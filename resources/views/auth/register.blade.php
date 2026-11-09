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