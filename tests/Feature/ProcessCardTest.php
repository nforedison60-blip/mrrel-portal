<?php

use App\Models\User;

it('shows the compact how-it-works card to tenants', function () {
    $user = User::factory()->create(['role' => 'Customer']);
    $this->actingAs($user)->get(route('customer.needs.create'))
        ->assertOk()
        ->assertSee('How it works')
        ->assertSee('R99 once-off')
        ->assertSee('See the full details');
});

it('shows the how-it-works card to landlords on the tenant matches page', function () {
    $user = User::factory()->create(['role' => 'Landlord']);
    $this->actingAs($user)->get(route('landlord.introductions'))
        ->assertOk()
        ->assertSee('How it works')
        ->assertSee('R250 once-off');
});
