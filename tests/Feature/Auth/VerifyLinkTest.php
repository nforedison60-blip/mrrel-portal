<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;

test('the verification link works without being logged in first', function () {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id, 'hash' => sha1($user->email),
    ]);

    $this->get($url)->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->assertAuthenticatedAs($user);
});

test('a link with the wrong hash is refused', function () {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id, 'hash' => sha1('someone-else@example.com'),
    ]);

    $this->get($url)->assertForbidden();
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
