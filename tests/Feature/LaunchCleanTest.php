<?php

use App\Models\User;

function launchAdmins(): array
{
    return [
        User::factory()->create(['email' => 'makamumihlovo@gmail.com', 'role' => 'Customer']),
        User::factory()->create(['email' => 'nforedison60@gmail.com', 'role' => 'Provider']),
    ];
}

it('does nothing on a dry run', function () {
    launchAdmins();
    User::factory()->count(3)->create();

    $this->artisan('app:launch-clean')->assertSuccessful();

    expect(User::count())->toBe(5);
});

it('keeps only the two launch accounts and makes them admins', function () {
    launchAdmins();
    User::factory()->count(3)->create();

    $this->artisan('app:launch-clean --confirm')->assertSuccessful();

    expect(User::count())->toBe(2);
    expect(User::pluck('role')->unique()->all())->toBe(['Admin']);
});

it('refuses to run when a kept account does not exist', function () {
    User::factory()->create(['email' => 'makamumihlovo@gmail.com']);
    User::factory()->count(2)->create();

    $this->artisan('app:launch-clean --confirm')->assertFailed();

    expect(User::count())->toBe(3);
});
