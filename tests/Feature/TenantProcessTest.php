<?php

use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\User;
use App\Models\ViewingRequest;
use App\Support\Geo;
use App\Support\TenantFees;

beforeEach(function () {
    config([
        'services.ozow.site_code'   => 'TESTSITE',
        'services.ozow.private_key' => 'secret-key',
        'services.ozow.is_test'     => true,
        'tenant.base.lat'           => null,
        'tenant.base.lng'           => null,
    ]);
});

function tenantMakeUser(string $role = 'Customer'): User
{
    return User::factory()->create(['role' => $role]);
}

function tenantMakeProperty(User $landlord, array $override = []): Property
{
    return Property::create(array_merge([
        'user_id'       => $landlord->id,
        'title'         => 'Secret Villa 12 Oak Road',
        'description'   => 'Hidden garden description',
        'property_type' => 'Apartment',
        'city'          => 'Cape Town',
        'suburb'        => 'Claremont',
        'bedrooms'      => '2',
        'bathrooms'     => '1',
        'rent'          => 9000,
        'deposit'       => 12345,
        'status'        => 'Approved',
    ], $override));
}

function tenantMakeMatch(User $tenant, Property $property): RentalNeed
{
    $need = RentalNeed::create([
        'user_id' => $tenant->id, 'areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Matched',
    ]);
    PropertyMatch::create([
        'rental_need_id' => $need->id, 'property_id' => $property->id, 'user_id' => $tenant->id, 'status' => 'Offered',
    ]);
    return $need;
}

function tenantPaidFee(User $tenant): TenantPayment
{
    return TenantPayment::create([
        'user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Paid', 'paid_at' => now(),
    ]);
}

// A message the way Ozow sends it, signed with the test private key
function tenantOzowMessage(string $reference, float $amount, array $override = []): array
{
    $fields = array_merge([
        'SiteCode' => 'TESTSITE', 'TransactionId' => 'tx-123', 'TransactionReference' => $reference,
        'Amount' => number_format($amount, 2, '.', ''), 'Status' => 'Complete',
        'Optional1' => '', 'Optional2' => '', 'Optional3' => '', 'Optional4' => '', 'Optional5' => '',
        'CurrencyCode' => 'ZAR', 'IsTest' => 'true', 'StatusMessage' => 'Test transaction',
    ], $override);
    $fields['Hash'] = hash('sha512', strtolower(implode('', $fields) . 'secret-key'));

    return $fields;
}

it('lets a tenant save what they need', function () {
    $tenant = tenantMakeUser();

    $this->actingAs($tenant)->post(route('customer.needs.store'), [
        'areas' => 'Claremont, Rondebosch', 'budget_max' => 12000, 'bedrooms' => '2',
        'move_in_date' => now()->addMonth()->toDateString(), 'notes' => 'Parking please',
    ])->assertRedirect(route('customer.needs.edit'));

    $need = RentalNeed::where('user_id', $tenant->id)->first();
    expect($need)->not->toBeNull();
    expect($need->status)->toBe('Open');
    expect($need->areas)->toBe('Claremont, Rondebosch');

    // They can change it while it is open, and there is still only one
    $this->actingAs($tenant)->put(route('customer.needs.update'), [
        'areas' => 'Newlands', 'budget_max' => 9000, 'bedrooms' => '1',
    ])->assertRedirect(route('customer.needs.edit'));
    expect(RentalNeed::where('user_id', $tenant->id)->count())->toBe(1);
    expect($need->fresh()->areas)->toBe('Newlands');
});

it('shows a preview before the fee is paid and the full details after', function () {
    $tenant = tenantMakeUser();
    $property = tenantMakeProperty(tenantMakeUser('Landlord'));
    tenantMakeMatch($tenant, $property);

    $this->actingAs($tenant)->get(route('customer.matches'))
        ->assertOk()
        ->assertSee('Claremont')
        ->assertSee('Pay the once-off R99 matching fee to see the full details')
        ->assertDontSee('Secret Villa')
        ->assertDontSee('Hidden garden description')
        ->assertDontSee('12,345');

    tenantPaidFee($tenant);

    $this->actingAs($tenant)->get(route('customer.matches'))
        ->assertOk()
        ->assertSee('Secret Villa 12 Oak Road')
        ->assertSee('Hidden garden description')
        ->assertSee('12,345');
});

it('refuses a viewing request before the fee is paid and accepts it after', function () {
    $tenant = tenantMakeUser();
    $property = tenantMakeProperty(tenantMakeUser('Landlord'));
    tenantMakeMatch($tenant, $property);

    $this->actingAs($tenant)->post('/viewing-request', ['property_id' => $property->id])
        ->assertSessionHas('error');
    expect(ViewingRequest::count())->toBe(0);

    tenantPaidFee($tenant);

    $this->actingAs($tenant)->post('/viewing-request', ['property_id' => $property->id])
        ->assertRedirect(route('customer.shortlist'));
    expect(ViewingRequest::count())->toBe(1);
});

it('refuses a viewing for a place that was not matched to the tenant', function () {
    $tenant = tenantMakeUser();
    $property = tenantMakeProperty(tenantMakeUser('Landlord'));
    tenantPaidFee($tenant);

    $this->actingAs($tenant)->post('/viewing-request', ['property_id' => $property->id])
        ->assertSessionHas('error');
    expect(ViewingRequest::count())->toBe(0);
});

it('does not let a tenant accept a place before paying', function () {
    $tenant = tenantMakeUser();
    $property = tenantMakeProperty(tenantMakeUser('Landlord'));
    tenantMakeMatch($tenant, $property);

    $this->actingAs($tenant)->post('/shortlist/' . $property->id . '/accept')->assertSessionHas('error');
    expect($property->fresh()->status)->toBe('Approved');
});

it('marks a tenant payment paid from a signed Ozow message, and only once', function () {
    $tenant = tenantMakeUser();
    $payment = TenantPayment::create([
        'user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending',
    ]);
    $message = tenantOzowMessage($payment->reference(), 99);

    $this->post('/payment/notify', $message)->assertOk();
    expect($payment->fresh()->status)->toBe('Paid');
    $paidAt = $payment->fresh()->paid_at->toDateTimeString();

    $this->travel(5)->minutes();
    $this->post('/payment/notify', $message)->assertOk();

    expect($payment->fresh()->paid_at->toDateTimeString())->toBe($paidAt);
    expect(TenantPayment::where('status', 'Paid')->count())->toBe(1);
});

it('ignores a wrong amount and a bad signature', function () {
    $tenant = tenantMakeUser();
    $payment = TenantPayment::create([
        'user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending',
    ]);

    $this->post('/payment/notify', tenantOzowMessage($payment->reference(), 50))->assertOk();
    expect($payment->fresh()->status)->toBe('Pending');

    $forged = tenantOzowMessage($payment->reference(), 99);
    $forged['Hash'] = 'not-the-real-signature';
    $this->post('/payment/notify', $forged)->assertStatus(400);
    expect($payment->fresh()->status)->toBe('Pending');
});

it('does not mark a payment paid when the return page is opened', function () {
    $tenant = tenantMakeUser();
    $payment = TenantPayment::create([
        'user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending',
    ]);

    $this->actingAs($tenant)->get(route('tenant.payments.back', [
        'TransactionReference' => $payment->reference(), 'Status' => 'Complete',
    ]))->assertRedirect();

    expect($payment->fresh()->status)->toBe('Pending');
});

it('works out the attendance fee: R150 up to 10 km round trip, then R7.50 per extra km', function () {
    expect(TenantFees::feeForOneWayKm(0)['fee'])->toBe(150.0);
    expect(TenantFees::feeForOneWayKm(3)['fee'])->toBe(150.0);      // 7.8 km round trip, rounded to 8
    expect(TenantFees::feeForOneWayKm(3.8)['fee'])->toBe(150.0);    // 9.88 km, rounded to 10
    expect(TenantFees::feeForOneWayKm(4)['fee'])->toBe(157.5);      // 10.4 km, rounded to 11: one extra km
    expect(TenantFees::feeForOneWayKm(10)['fee'])->toBe(270.0);     // 26 km: 16 extra km
    expect(TenantFees::feeForOneWayKm(10)['round_trip_km'])->toBe(26);
});

it('does not let a tenant open another tenant\'s payment', function () {
    $owner = tenantMakeUser();
    $other = tenantMakeUser();
    $payment = TenantPayment::create([
        'user_id' => $owner->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending',
    ]);

    $this->actingAs($other)->get(route('tenant.payments.back', ['TransactionReference' => $payment->reference()]))
        ->assertNotFound();
});

it('starts the matching fee once and reuses the unfinished payment', function () {
    $tenant = tenantMakeUser();
    tenantMakeMatch($tenant, tenantMakeProperty(tenantMakeUser('Landlord')));

    $this->actingAs($tenant)->post(route('tenant.pay.matching'))->assertOk()->assertSee('ozow_form', false);
    $this->actingAs($tenant)->post(route('tenant.pay.matching'))->assertOk();

    expect(TenantPayment::where('user_id', $tenant->id)->where('type', 'matching_fee')->count())->toBe(1);
    expect((float) TenantPayment::first()->amount)->toBe(99.0);
});

it('hides the attendance option when MrRel\'s base location is not set', function () {
    $tenant = tenantMakeUser();
    tenantMakeMatch($tenant, tenantMakeProperty(tenantMakeUser('Landlord')));
    tenantPaidFee($tenant);

    expect(TenantFees::attendanceAvailable())->toBeFalse();
    $this->actingAs($tenant)->get(route('customer.matches'))
        ->assertOk()->assertDontSee('Have someone from MrRel attend for me');

    config(['tenant.base.lat' => '-33.9249', 'tenant.base.lng' => '18.4241']);
    expect(TenantFees::attendanceAvailable())->toBeTrue();
    $this->actingAs($tenant)->get(route('customer.matches'))
        ->assertOk()->assertSee('Have someone from MrRel attend for me');
});

it('stores the attendance fee on the server and pays what was stored', function () {
    config(['tenant.base.lat' => '-33.9249', 'tenant.base.lng' => '18.4241']);

    $tenant = tenantMakeUser();
    $property = tenantMakeProperty(tenantMakeUser('Landlord'));
    $property->forceFill(['geo_lat' => -33.8000, 'geo_lng' => 18.5000])->save();
    tenantMakeMatch($tenant, $property);
    tenantPaidFee($tenant);

    $this->actingAs($tenant)->post('/viewing-request', ['property_id' => $property->id, 'mrrel_attends' => '1'])
        ->assertRedirect();
    $viewing = ViewingRequest::first();

    $this->actingAs($tenant)->post(route('tenant.attendance.pay', $viewing->id))
        ->assertOk()->assertSee('ozow_form', false);

    $property = $property->fresh();
    $expected = TenantFees::feeForOneWayKm(
        Geo::distanceKm(-33.9249, 18.4241, (float) $property->geo_lat, (float) $property->geo_lng)
    )['fee'];

    $payment = TenantPayment::where('type', 'viewing_attendance')->first();
    expect((float) $payment->amount)->toBe($expected);
    expect($payment->status)->toBe('Pending');

    // The signed message confirms it and MrRel is marked as attending
    $this->post('/payment/notify', tenantOzowMessage($payment->reference(), $expected))->assertOk();
    expect($payment->fresh()->status)->toBe('Paid');
    expect((bool) $viewing->fresh()->mrrel_attends)->toBeTrue();
});
