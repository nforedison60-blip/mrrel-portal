<?php

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

// A real 1x1 PNG, so these tests do not need the GD extension
function landlordPhoto(string $name = 'photo.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent(
        $name,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
    );
}

function photoLandlord(): User
{
    return User::factory()->create(['role' => 'Landlord']);
}

function photoPayload(array $extra = []): array
{
    return array_merge([
        'title' => 'Garden Flat', 'description' => 'Nice', 'property_type' => 'Apartment', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000, 'own' => '1',
    ], $extra);
}

function photoProperty(User $landlord, array $override = []): Property
{
    return Property::create(array_merge([
        'user_id' => $landlord->id, 'title' => 'Old', 'description' => 'x', 'property_type' => 'Apartment', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000, 'status' => 'Draft',
    ], $override));
}

it('saves several photos when a landlord adds a property, the first as cover', function () {
    $landlord = photoLandlord();

    $this->actingAs($landlord)->post(route('landlord.properties.store'), photoPayload([
        'photos' => [landlordPhoto('a.jpg'), landlordPhoto('b.jpg'), landlordPhoto('c.png')],
    ]))->assertRedirect(route('landlord.properties'));

    $property = Property::where('user_id', $landlord->id)->first();
    expect($property->image_path)->not->toBeNull();
    expect($property->photo_paths)->toHaveCount(2);
    expect($property->photoUrls())->toHaveCount(3);
    Storage::disk('s3')->assertExists($property->image_path);
});

it('still works with no photos at all', function () {
    $landlord = photoLandlord();

    $this->actingAs($landlord)->post(route('landlord.properties.store'), photoPayload())->assertRedirect(route('landlord.properties'));

    $property = Property::where('user_id', $landlord->id)->first();
    expect($property->image_path)->toBeNull();
    expect($property->photo_paths)->toBeNull();
});

it('refuses more than 6 photos and files that are not images', function () {
    $landlord = photoLandlord();
    $seven = array_map(fn ($i) => landlordPhoto("p{$i}.jpg"), range(1, 7));

    $this->actingAs($landlord)->post(route('landlord.properties.store'), photoPayload(['photos' => $seven]))->assertSessionHasErrors('photos');
    $this->actingAs($landlord)->post(route('landlord.properties.store'), photoPayload(['photos' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')]]))->assertSessionHasErrors('photos.0');
    expect(Property::count())->toBe(0);
});

it('lets a landlord add and remove photos on an existing property', function () {
    $landlord = photoLandlord();
    Storage::disk('s3')->put('properties/cover.jpg', 'x');
    Storage::disk('s3')->put('properties/second.jpg', 'x');
    $property = photoProperty($landlord, ['image_path' => 'properties/cover.jpg', 'photo_paths' => ['properties/second.jpg']]);

    // Remove the cover and add a new photo: the second photo becomes the cover
    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", photoPayload([
        'remove_photos' => ['properties/cover.jpg'],
        'photos' => [landlordPhoto('new.jpg')],
    ]))->assertRedirect(route('landlord.properties'));

    $property->refresh();
    expect($property->image_path)->toBe('properties/second.jpg');
    expect($property->photo_paths)->toHaveCount(1);
    Storage::disk('s3')->assertMissing('properties/cover.jpg');
    Storage::disk('s3')->assertExists('properties/second.jpg');
});

it('keeps the photos when the landlord changes only the text', function () {
    $landlord = photoLandlord();
    Storage::disk('s3')->put('properties/cover.jpg', 'x');
    $property = photoProperty($landlord, ['image_path' => 'properties/cover.jpg']);

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", photoPayload(['title' => 'New title']))->assertRedirect(route('landlord.properties'));

    expect($property->fresh()->image_path)->toBe('properties/cover.jpg');
    expect($property->fresh()->title)->toBe('New title');
});

it('never removes a path that is not on the property', function () {
    $landlord = photoLandlord();
    Storage::disk('s3')->put('properties/mine.jpg', 'x');
    Storage::disk('s3')->put('properties/someone-elses.jpg', 'x');
    $property = photoProperty($landlord, ['image_path' => 'properties/mine.jpg']);

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", photoPayload(['remove_photos' => ['properties/someone-elses.jpg']]));

    Storage::disk('s3')->assertExists('properties/someone-elses.jpg');
    Storage::disk('s3')->assertExists('properties/mine.jpg');
});

it('rejects going over 6 photos in total when editing', function () {
    $landlord = photoLandlord();
    $paths = array_map(fn ($i) => "properties/p{$i}.jpg", range(1, 5));
    foreach ($paths as $p) { Storage::disk('s3')->put($p, 'x'); }
    $property = photoProperty($landlord, ['image_path' => $paths[0], 'photo_paths' => array_slice($paths, 1)]);

    $this->actingAs($landlord)->post("/landlord/properties/{$property->id}", photoPayload([
        'photos' => [landlordPhoto('a.jpg'), landlordPhoto('b.jpg')],
    ]))->assertSessionHasErrors('photos');

    expect($property->fresh()->photo_paths)->toHaveCount(4);
});

it('lets a landlord change their profile details', function () {
    $landlord = photoLandlord();

    $this->actingAs($landlord)->post(route('landlord.profile.update'), [
        'name' => 'New Name', 'phone' => '0821112222', 'landlord_type' => 'Agent', 'agency_name' => 'Cape Lets', 'ppra_number' => 'PPRA-1',
    ])->assertRedirect(route('landlord.profile'));

    $landlord->refresh();
    expect($landlord->name)->toBe('New Name');
    expect($landlord->phone)->toBe('0821112222');
    expect($landlord->landlord_type)->toBe('Agent');
});

it('lets a landlord change their password from the profile page', function () {
    $landlord = User::factory()->create(['role' => 'Landlord', 'password' => bcrypt('old-password-1')]);

    $this->actingAs($landlord)->get(route('landlord.profile'))->assertOk()->assertSee('Change your password');

    $this->actingAs($landlord)->from(route('landlord.profile'))->put(route('password.update'), [
        'current_password' => 'old-password-1', 'password' => 'new-password-22', 'password_confirmation' => 'new-password-22',
    ])->assertRedirect(route('landlord.profile'));

    expect(\Illuminate\Support\Facades\Hash::check('new-password-22', $landlord->fresh()->password))->toBeTrue();
});
