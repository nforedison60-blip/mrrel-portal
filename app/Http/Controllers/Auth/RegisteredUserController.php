<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9][0-9\s\-()]{7,}$/'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->numbers()],
            'role' => ['required', 'string', 'in:Customer,Provider,Landlord'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => 'Enter a valid mobile number, for example 082 123 4567.',
            'terms.accepted' => 'Please agree to the Terms of Use and Privacy Policy to continue.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => trim($request->phone),
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        \App\Support\Notifications::newAccount($user);

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
