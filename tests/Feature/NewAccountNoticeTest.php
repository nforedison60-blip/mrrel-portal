<?php

use App\Mail\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('admins are emailed when someone creates an account', function () {
    Mail::fake();
    User::factory()->create(['role' => 'Admin', 'email' => 'boss@example.com']);
    User::factory()->create(['role' => 'Admin', 'email' => 'boss2@example.com']);

    $this->post('/register', [
        'name' => 'Thandi Nkosi', 'email' => 'thandi@example.com', 'phone' => '082 123 4567',
        'password' => 'password1', 'password_confirmation' => 'password1', 'role' => 'Provider', 'terms' => '1',
    ]);

    Mail::assertSent(Notice::class, fn ($m) => $m->hasTo('boss@example.com') && str_contains($m->subjectLine, 'New'));
    Mail::assertSent(Notice::class, fn ($m) => $m->hasTo('boss2@example.com'));
});

test('a failed admin email does not stop registration', function () {
    // no admins exist: nothing to send, and the person still gets their account
    $this->post('/register', [
        'name' => 'Sam Dlamini', 'email' => 'sam@example.com', 'phone' => '082 123 4567',
        'password' => 'password1', 'password_confirmation' => 'password1', 'role' => 'Customer', 'terms' => '1',
    ])->assertRedirect();

    expect(User::where('email', 'sam@example.com')->exists())->toBeTrue();
});
