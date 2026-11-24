<?php

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function customer(): User
{
    return User::create([
        'name' => 'Photo Customer',
        'email' => 'photos@example.com',
        'password' => 'password1',
        'role' => 'Customer',
    ]);
}

function requestPayload(array $overrides = []): array
{
    return array_merge([
        'services_needed' => 'Movers',
        'moving_from' => 'Claremont, Cape Town',
        'pickup_lat' => '-33.9800000',
        'pickup_lng' => '18.4650000',
        'move_date' => now()->addWeek()->format('Y-m-d'),
        'date_flexibility' => 'Exact date',
        'item_size' => 'A few items',
        'access_pickup' => 'Ground floor',
        'access_delivery' => 'Ground floor',
    ], $overrides);
}

test('a request can be sent with photos', function () {
    Storage::fake('s3');

    $this->actingAs(customer())->post('/requests/new', requestPayload([
        'photos' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
    ]))->assertRedirect(route('customer.requests'));

    $saved = ServiceRequest::firstOrFail();
    expect($saved->photo_paths)->toHaveCount(2);
    foreach ($saved->photo_paths as $path) {
        Storage::disk('s3')->assertExists($path);
    }
});

test('a request still works without photos', function () {
    Storage::fake('s3');

    $this->actingAs(customer())->post('/requests/new', requestPayload())
        ->assertRedirect(route('customer.requests'));

    expect(ServiceRequest::firstOrFail()->photo_paths)->toBeNull();
});

test('more than 5 photos is refused', function () {
    Storage::fake('s3');
    $photos = array_map(fn ($i) => UploadedFile::fake()->image("p$i.jpg"), range(1, 6));

    $this->actingAs(customer())->post('/requests/new', requestPayload(['photos' => $photos]))
        ->assertSessionHasErrors('photos');

    expect(ServiceRequest::count())->toBe(0);
});

test('files that are not images are refused', function () {
    Storage::fake('s3');

    $this->actingAs(customer())->post('/requests/new', requestPayload([
        'photos' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
    ]))->assertSessionHasErrors('photos.0');
});
