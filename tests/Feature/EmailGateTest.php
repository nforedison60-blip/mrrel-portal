<?php

use App\Models\User;

it('keeps unverified accounts out of every area', function (string $role, string $route) {
    $user = User::factory()->unverified()->create(['role' => $role]);

    $this->actingAs($user)->get(route($route))->assertRedirect(route('verification.notice'));
})->with([
    ['Customer', 'customer.dashboard'],
    ['Landlord', 'landlord.properties'],
    ['Provider', 'provider.dashboard'],
    ['Admin', 'admin.dashboard'],
]);

it('lets verified accounts in as before', function () {
    $user = User::factory()->create(['role' => 'Customer']);

    $this->actingAs($user)->get(route('customer.dashboard'))->assertOk();
});

it('lets someone verify through the emailed link and then continue', function () {
    $user = User::factory()->unverified()->create(['role' => 'Customer']);
    $url = \Illuminate\Support\Facades\URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1($user->email)]);

    $this->actingAs($user)->get($url);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->actingAs($user->fresh())->get(route('customer.dashboard'))->assertOk();
});
