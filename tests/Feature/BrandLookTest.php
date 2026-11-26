<?php

use App\Models\User;

it('no longer shows demo text or the role switcher to a customer', function () {
    $user = User::factory()->create(['role' => 'Customer']);
    $this->actingAs($user)->get(route('customer.dashboard'))
        ->assertOk()
        ->assertDontSee('Example data')
        ->assertDontSee('id="sw"', false)
        ->assertSee('images/mrrel-logo.png', false)
        ->assertSee('mrrel-brand.css', false);
});

it('shows the logo on the sign in page', function () {
    $this->get(route('login'))->assertOk()->assertSee('images/mrrel-logo.png', false);
});
