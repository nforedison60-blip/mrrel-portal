<?php

use App\Models\User;

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

test('new customers can register', function () {
    $response = $this->post('/register', validRegistration());

    $this->assertAuthenticated();
    $response->assertRedirect(route('customer.dashboard', absolute: false));
    expect(User::where('email', 'test@example.com')->value('phone'))->toBe('082 123 4567');
});

test('providers land on the provider dashboard', function () {
    $this->post('/register', validRegistration(['role' => 'Provider']))
        ->assertRedirect(route('provider.dashboard', absolute: false));
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
