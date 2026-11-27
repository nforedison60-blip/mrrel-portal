<?php

use App\Models\Property;
use App\Models\User;

function editLandlord(string $email = 'edit-landlord@example.com'): User
{
    return User::factory()->create(['name' => 'Landlord', 'email' => $email, 'password' => 'password1', 'role' => 'Landlord']);
}

function editProperty(User $landlord, string $status): Property
{
    return Property::create([
        'user_id' => $landlord->id, 'title' => 'Old title', 'property_type' => 'House', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 10000, 'deposit' => 10000, 'status' => $status,
    ]);
}

function editPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'New title', 'description' => 'Sunny', 'property_type' => 'House', 'city' => 'Cape Town',
        'suburb' => 'Newlands', 'bedrooms' => '3', 'bathrooms' => '2', 'rent' => '12000', 'deposit' => '12000',
    ], $overrides);
}

test('a landlord can open the edit page for their own property', function () {
    $landlord = editLandlord();
    $property = editProperty($landlord, 'Draft');

    $this->actingAs($landlord)->get("/landlord/properties/{$property->id}/edit")->assertOk()->assertSee('Old title');
});

test('editing a property waiting for review keeps it waiting', function () {
    $landlord = editLandlord();
    $property = editProperty($landlord, 'Draft');

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", editPayload())
        ->assertRedirect(route('landlord.properties'));

    $property->refresh();
    expect($property->title)->toBe('New title')->and($property->suburb)->toBe('Newlands')->and($property->status)->toBe('Draft');
});

test('editing a live property sends it back for review', function () {
    $landlord = editLandlord();
    $property = editProperty($landlord, 'Approved');

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", editPayload());

    expect($property->fresh()->status)->toBe('Draft');
});

test('a property a tenant accepted cannot be edited', function () {
    $landlord = editLandlord();
    $property = editProperty($landlord, 'Under Offer');

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", editPayload())
        ->assertRedirect(route('landlord.properties'))->assertSessionHas('error');

    expect($property->fresh()->title)->toBe('Old title');
});

test('a landlord cannot edit someone elses property', function () {
    $owner = editLandlord('owner@example.com');
    $other = editLandlord('other@example.com');
    $property = editProperty($owner, 'Draft');

    $this->actingAs($other)->get("/landlord/properties/{$property->id}/edit")->assertNotFound();
    $this->actingAs($other)->post("/landlord/properties/{$property->id}", editPayload())->assertNotFound();
    expect($property->fresh()->title)->toBe('Old title');
});
