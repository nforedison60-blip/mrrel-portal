<x-guest-layout>
    <h2 style="font:700 22px Montserrat,sans-serif;color:#003068;margin:0 0 8px;">Check your email</h2>
    <div class="mb-4 text-sm text-gray-600">
        We sent a verification link to <b>{{ auth()->user()->email }}</b>. Click the link in that email to activate your MrRel account. It may take a minute, and please check your spam folder too.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent to {{ auth()->user()->email }}.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Send the link again</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">Log out</button>
        </form>
    </div>
</x-guest-layout>
