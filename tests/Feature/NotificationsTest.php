<?php

use App\Mail\Notice;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Provider;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    config(['mrrel.business.email' => 'admin@mrrel.test']);
});

function noteUser(string $role, array $o = []): User
{
    return User::factory()->create(array_merge(['role' => $role], $o));
}

function noteProperty(User $landlord, string $title = 'Garden Flat', string $status = 'Approved'): Property
{
    return Property::create([
        'user_id' => $landlord->id, 'title' => $title, 'description' => 'x', 'property_type' => 'Apartment', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000, 'status' => $status,
    ]);
}

function noteMatch(User $tenant, Property $property): PropertyMatch
{
    $need = RentalNeed::firstOrCreate(['user_id' => $tenant->id], ['areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Matched']);

    return PropertyMatch::create(['rental_need_id' => $need->id, 'property_id' => $property->id, 'user_id' => $tenant->id, 'status' => 'Offered']);
}

function noteSentTo(string $email): bool
{
    return Mail::sent(Notice::class, fn ($m) => $m->hasTo($email))->count() > 0;
}

it('emails the tenant when MrRel sends their matches', function () {
    $admin = noteUser('Admin');
    $tenant = noteUser('Customer', ['email' => 'tenant@example.test']);
    $property = noteProperty(noteUser('Landlord'));
    $need = RentalNeed::create(['user_id' => $tenant->id, 'areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Open']);

    $this->actingAs($admin)->post(route('admin.tenant-matching.send', $need->id), ['property_ids' => [$property->id]])->assertRedirect();

    expect(noteSentTo('tenant@example.test'))->toBeTrue();
});

it('tells the tenant and each matched landlord once when the matching fee is paid', function () {
    $tenant = noteUser('Customer', ['email' => 'tenant@example.test']);
    $landlord = noteUser('Landlord', ['email' => 'landlord@example.test']);
    noteMatch($tenant, noteProperty($landlord, 'Flat One'));
    noteMatch($tenant, noteProperty($landlord, 'Flat Two'));
    $payment = TenantPayment::create(['user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending']);

    $payment->update(['status' => 'Paid', 'paid_at' => now()]);

    expect(noteSentTo('tenant@example.test'))->toBeTrue();
    expect(Mail::sent(Notice::class, fn ($m) => $m->hasTo('landlord@example.test'))->count())->toBe(1);
});

it('does not email again when the same payment is saved again', function () {
    $tenant = noteUser('Customer', ['email' => 'tenant@example.test']);
    $payment = TenantPayment::create(['user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending']);

    $payment->update(['status' => 'Paid']);
    $payment->update(['paid_at' => now()]);

    expect(Mail::sent(Notice::class, fn ($m) => $m->hasTo('tenant@example.test'))->count())->toBe(1);
});

it('emails the landlord and the admin when an introduction is paid', function () {
    $tenant = noteUser('Customer', ['name' => 'Thandi Mokoena', 'phone' => '0821234567']);
    $landlord = noteUser('Landlord', ['email' => 'landlord@example.test']);
    $match = noteMatch($tenant, noteProperty($landlord));

    $match->forceFill(['intro_status' => 'Pending', 'intro_amount' => 250])->save();
    expect(noteSentTo('landlord@example.test'))->toBeFalse();

    $match->forceFill(['intro_status' => 'Paid', 'intro_paid_at' => now()])->save();

    expect(noteSentTo('landlord@example.test'))->toBeTrue();
    $adminMail = Mail::sent(Notice::class, fn ($m) => $m->hasTo('admin@mrrel.test'))->first();
    expect($adminMail)->not->toBeNull();
    expect(implode(' ', $adminMail->lines))->toContain('0821234567');
});

it('emails the landlord when MrRel approves a property, but not when a live one goes back to review', function () {
    $landlord = noteUser('Landlord', ['email' => 'landlord@example.test']);
    $property = noteProperty($landlord, 'New Place', 'Draft');

    $property->update(['status' => 'Approved']);
    expect(Mail::sent(Notice::class, fn ($m) => $m->hasTo('landlord@example.test'))->count())->toBe(1);

    $property->update(['status' => 'Draft']);
    expect(Mail::sent(Notice::class, fn ($m) => $m->hasTo('landlord@example.test'))->count())->toBe(1);
});

it('emails a mover when they are verified', function () {
    $user = noteUser('Provider', ['email' => 'mover@example.test']);
    $provider = Provider::create(['email' => $user->email, 'name' => 'Fast Movers', 'user_id' => $user->id, 'verification_status' => 'Pending']);

    $provider->update(['verification_status' => 'Verified']);

    expect(noteSentTo('mover@example.test'))->toBeTrue();
});

it('never breaks the page when an email cannot be sent', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('mail server down'));

    \App\Support\Notifications::send('someone@example.test', 'Hi', 'Hi', ['x']);

    expect(true)->toBeTrue();
});
