<?php

use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\User;
use App\Models\ViewingRequest;

beforeEach(function () {
    config([
        'services.ozow.site_code'   => 'TESTSITE',
        'services.ozow.private_key' => 'secret-key',
        'services.ozow.is_test'     => true,
        'introductions.fee'         => 250,
    ]);
});

function introUser(string $role, array $o = []): User
{
    return User::factory()->create(array_merge(['role' => $role], $o));
}

// A landlord, a live property, a tenant with a match (tenant matching fee paid unless told otherwise)
function introSetup(bool $tenantPaid = true): array
{
    $landlord = introUser('Landlord');
    $tenant = introUser('Customer', ['name' => 'Thandi Mokoena', 'phone' => '0821234567']);
    $property = Property::create([
        'user_id' => $landlord->id, 'title' => 'Garden Flat', 'description' => 'Nice', 'property_type' => 'Apartment',
        'city' => 'Cape Town', 'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000,
        'status' => 'Approved',
    ]);
    $need = RentalNeed::create(['user_id' => $tenant->id, 'areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Matched']);
    $match = PropertyMatch::create(['rental_need_id' => $need->id, 'property_id' => $property->id, 'user_id' => $tenant->id, 'status' => 'Offered']);
    if ($tenantPaid) {
        TenantPayment::create(['user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Paid', 'paid_at' => now()]);
    }

    return [$landlord, $tenant, $property, $match];
}

function introOzow(string $reference, float $amount, array $override = []): array
{
    $fields = array_merge([
        'SiteCode' => 'TESTSITE', 'TransactionId' => 'tx-777', 'TransactionReference' => $reference,
        'Amount' => number_format($amount, 2, '.', ''), 'Status' => 'Complete',
        'Optional1' => '', 'Optional2' => '', 'Optional3' => '', 'Optional4' => '', 'Optional5' => '',
        'CurrencyCode' => 'ZAR', 'IsTest' => 'true', 'StatusMessage' => 'Test transaction',
    ], $override);
    $fields['Hash'] = hash('sha512', strtolower(implode('', $fields) . 'secret-key'));

    return $fields;
}

it('shows the landlord a limited profile with no contact details', function () {
    [$landlord] = introSetup();

    $this->actingAs($landlord)->get(route('landlord.introductions'))
        ->assertOk()
        ->assertSee('Thandi')
        ->assertSee('Proceed and pay R250')
        ->assertDontSee('Mokoena')
        ->assertDontSee('0821234567');
});

it('hides tenants who have not paid their matching fee', function () {
    [$landlord] = introSetup(false);

    $this->actingAs($landlord)->get(route('landlord.introductions'))
        ->assertOk()
        ->assertDontSee('Thandi');
});

it('does not show another landlord\'s matches', function () {
    introSetup();
    $other = introUser('Landlord');

    $this->actingAs($other)->get(route('landlord.introductions'))->assertOk()->assertDontSee('Thandi');
});

it('sends a customer away from the landlord and admin pages', function () {
    [, $tenant, , $match] = introSetup();

    // A customer opening these pages is taken to their own area, and cannot act on them
    $this->actingAs($tenant)->get(route('landlord.introductions'))->assertRedirect(route('customer.dashboard'));
    $this->actingAs($tenant)->get(route('admin.introductions'))->assertRedirect(route('customer.dashboard'));
    $this->actingAs($tenant)->post(route('landlord.introductions.pay', $match->id))->assertForbidden();
    expect($match->fresh()->intro_status)->toBeNull();
});

it('stores the fee on the server when the landlord starts paying', function () {
    [$landlord, , , $match] = introSetup();

    $res = $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id), ['amount' => 1]);
    $res->assertOk()->assertSee('pay.ozow.com', false)->assertSee('LIN-' . str_pad((string) $match->id, 4, '0', STR_PAD_LEFT), false);

    $match->refresh();
    expect($match->intro_status)->toBe('Pending');
    expect((float) $match->intro_amount)->toBe(250.0);
    expect($match->landlord_status)->toBe('Interested');
});

it('never marks it paid when the landlord comes back from the bank', function () {
    [$landlord, , , $match] = introSetup();
    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));

    $this->actingAs($landlord)
        ->get(route('landlord.introductions.back', ['TransactionReference' => $match->introReference(), 'Status' => 'Complete']))
        ->assertRedirect(route('landlord.introductions'));

    expect($match->fresh()->intro_status)->toBe('Pending');
});

it('releases the tenant contact details only after the signed message', function () {
    [$landlord, , , $match] = introSetup();
    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));

    $this->post('/payment/notify', introOzow($match->introReference(), 250))->assertOk();

    expect($match->fresh()->intro_status)->toBe('Paid');
    $this->actingAs($landlord)->get(route('landlord.introductions'))
        ->assertOk()->assertSee('Thandi Mokoena')->assertSee('0821234567');
});

it('ignores a bad signature and a wrong amount', function () {
    [$landlord, , , $match] = introSetup();
    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));

    $bad = introOzow($match->introReference(), 250);
    $bad['Hash'] = 'nonsense';
    $this->post('/payment/notify', $bad);
    expect($match->fresh()->intro_status)->toBe('Pending');

    $this->post('/payment/notify', introOzow($match->introReference(), 10));
    expect($match->fresh()->intro_status)->toBe('Pending');
});

it('is safe when Ozow sends the same message twice', function () {
    [$landlord, , , $match] = introSetup();
    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));

    $this->post('/payment/notify', introOzow($match->introReference(), 250))->assertOk();
    $paidAt = $match->fresh()->intro_paid_at;
    $this->post('/payment/notify', introOzow($match->introReference(), 250))->assertOk();

    expect($match->fresh()->intro_status)->toBe('Paid');
    expect($match->fresh()->intro_paid_at->equalTo($paidAt))->toBeTrue();
});

it('refuses a landlord who tries to pay for someone else\'s match', function () {
    [, , , $match] = introSetup();
    $other = introUser('Landlord');

    $this->actingAs($other)->post(route('landlord.introductions.pay', $match->id))->assertNotFound();
    $this->actingAs($other)->post(route('landlord.introductions.decline', $match->id))->assertNotFound();
});

it('lets a landlord say not interested, and then stops showing the tenant', function () {
    [$landlord, , , $match] = introSetup();

    $this->actingAs($landlord)->post(route('landlord.introductions.decline', $match->id))->assertRedirect(route('landlord.introductions'));
    expect($match->fresh()->landlord_status)->toBe('Declined');
    $this->actingAs($landlord)->get(route('landlord.introductions'))->assertDontSee('Thandi');
});

it('shows the admin both contacts only for paid introductions', function () {
    [$landlord, , , $match] = introSetup();
    $admin = introUser('Admin');
    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));

    $this->actingAs($admin)->get(route('admin.introductions'))->assertOk()->assertSee('Shown after payment')->assertDontSee('0821234567');

    $this->post('/payment/notify', introOzow($match->introReference(), 250));
    $this->actingAs($admin)->get(route('admin.introductions'))->assertOk()->assertSee('0821234567');
});

it('hides the tenant\'s full name on viewing requests until the introduction is paid', function () {
    [$landlord, $tenant, $property, $match] = introSetup();
    ViewingRequest::create(['property_id' => $property->id, 'user_id' => $tenant->id, 'customer_name' => 'Thandi Mokoena', 'status' => 'Pending']);

    $this->actingAs($landlord)->get(route('landlord.viewing-requests'))->assertOk()->assertSee('Thandi')->assertDontSee('Mokoena');

    $this->actingAs($landlord)->post(route('landlord.introductions.pay', $match->id));
    $this->post('/payment/notify', introOzow($match->introReference(), 250));

    $this->actingAs($landlord)->get(route('landlord.viewing-requests'))->assertOk()->assertSee('Thandi Mokoena');
});
