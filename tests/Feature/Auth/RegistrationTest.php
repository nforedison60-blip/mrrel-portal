<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

function validRegistration(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '082 123 4567',
        'password' => 'password1',
        'password_confirmation' => 'password1',
        'role' => 'Customer',
        'terms' => '1',
    ], $overrides);
}

test('registration screen can be rendered', function () {
    $this->get('/register')->assertStatus(200)->assertSee('I offer moving services');
});

test('new customers can register and are asked to verify their email', function () {
    Notification::fake();

    $response = $this->post('/register', validRegistration());

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice'));
    $user = User::where('email', 'test@example.com')->first();
    expect($user->phone)->toBe('082 123 4567');
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('providers and landlords also have to verify first', function () {
    Notification::fake();

    $this->post('/register', validRegistration(['role' => 'Provider']))
        ->assertRedirect(route('verification.notice'));
});

test('registration needs the terms ticked', function () {
    $this->post('/register', validRegistration(['terms' => null]))->assertSessionHasErrors('terms');
    $this->assertGuest();
});

test('password needs a number', function () {
    $this->post('/register', validRegistration(['password' => 'password', 'password_confirmation' => 'password']))
        ->assertSessionHasErrors('password');
});

test('nobody can register as admin', function () {
    $this->post('/register', validRegistration(['role' => 'Admin']))->assertSessionHasErrors('role');
});
