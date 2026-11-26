<?php

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Quote;
use App\Models\RentalNeed;
use App\Models\ServiceRequest;
use App\Models\TenantPayment;
use App\Models\User;
use App\Models\ViewingRequest;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

function cleanUser(string $role): User
{
    return User::factory()->create(['role' => $role]);
}

function cleanProperty(User $landlord, string $status = 'Approved', array $o = []): Property
{
    return Property::create(array_merge([
        'user_id' => $landlord->id, 'title' => 'Garden Flat', 'description' => 'x', 'property_type' => 'Apartment', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000, 'status' => $status,
    ], $o));
}

function cleanMatch(User $tenant, Property $property, array $o = []): PropertyMatch
{
    $need = RentalNeed::firstOrCreate(['user_id' => $tenant->id], ['areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Matched']);
    $match = PropertyMatch::create(['rental_need_id' => $need->id, 'property_id' => $property->id, 'user_id' => $tenant->id, 'status' => 'Offered']);
    if ($o) { $match->forceFill($o)->save(); }

    return $match;
}

function cleanRequest(User $customer): ServiceRequest
{
    return ServiceRequest::create([
        'user_id' => $customer->id, 'customer_name' => $customer->name, 'services_needed' => 'Movers', 'moving_from' => 'Claremont',
        'move_date' => now()->addWeek()->format('Y-m-d'), 'date_flexibility' => 'Exact date', 'item_size' => 'A few items',
        'access_pickup' => 'Ground floor', 'access_delivery' => 'Ground floor', 'status' => 'Active',
    ]);
}

function cleanQuote(ServiceRequest $request): Quote
{
    return Quote::create(['provider_name' => 'Fast Movers', 'total_price' => 1500, 'price_type' => 'Fixed', 'validity' => '7 days', 'service_request_id' => $request->id]);
}

it('removes an unpaid property with everything linked to it, and its photos', function () {
    $admin = cleanUser('Admin');
    $landlord = cleanUser('Landlord');
    $tenant = cleanUser('Customer');
    Storage::disk('s3')->put('properties/cover.jpg', 'x');
    $property = cleanProperty($landlord, 'Approved', ['image_path' => 'properties/cover.jpg']);
    cleanMatch($tenant, $property);
    ViewingRequest::create(['property_id' => $property->id, 'user_id' => $tenant->id, 'customer_name' => 'T', 'status' => 'Pending']);
    Invoice::issue($property, 900);

    $this->actingAs($admin)->post(route('admin.properties.delete', $property->id))->assertRedirect(route('admin.properties'))->assertSessionHas('success');

    expect(Property::count())->toBe(0);
    expect(PropertyMatch::count())->toBe(0);
    expect(ViewingRequest::count())->toBe(0);
    expect(Invoice::count())->toBe(0);
    Storage::disk('s3')->assertMissing('properties/cover.jpg');
});

it('keeps a property a tenant accepted, or one with paid fees', function () {
    $admin = cleanUser('Admin');
    $landlord = cleanUser('Landlord');
    $tenant = cleanUser('Customer');

    $accepted = cleanProperty($landlord, 'Under Offer');
    $this->actingAs($admin)->post(route('admin.properties.delete', $accepted->id))->assertSessionHas('error');
    expect(Property::find($accepted->id))->not->toBeNull();

    $paidInvoice = cleanProperty($landlord);
    $invoice = Invoice::issue($paidInvoice, 900);
    $invoice->update(['status' => 'Paid']);
    $this->actingAs($admin)->post(route('admin.properties.delete', $paidInvoice->id))->assertSessionHas('error');
    expect(Property::find($paidInvoice->id))->not->toBeNull();

    $paidIntro = cleanProperty($landlord);
    cleanMatch($tenant, $paidIntro, ['intro_status' => 'Paid']);
    $this->actingAs($admin)->post(route('admin.properties.delete', $paidIntro->id))->assertSessionHas('error');
    expect(Property::find($paidIntro->id))->not->toBeNull();
});

it('removes a request with its quotes, but keeps one with a paid booking', function () {
    $admin = cleanUser('Admin');
    $customer = cleanUser('Customer');

    $open = cleanRequest($customer);
    cleanQuote($open);
    $this->actingAs($admin)->post(route('admin.requests.delete', $open->id))->assertRedirect(route('admin.requests'))->assertSessionHas('success');
    expect(ServiceRequest::find($open->id))->toBeNull();
    expect(Quote::count())->toBe(0);

    $paid = cleanRequest($customer);
    $quote = cleanQuote($paid);
    Booking::create(['quote_id' => $quote->id, 'user_id' => $customer->id, 'customer_name' => $customer->name, 'total_price' => 1500, 'status' => 'Paid']);
    $this->actingAs($admin)->post(route('admin.requests.delete', $paid->id))->assertSessionHas('error');
    expect(ServiceRequest::find($paid->id))->not->toBeNull();
});

it('only lets an admin remove or refund anything', function () {
    $customer = cleanUser('Customer');
    $landlord = cleanUser('Landlord');
    $property = cleanProperty($landlord);

    $this->actingAs($customer)->post(route('admin.properties.delete', $property->id))->assertForbidden();
    $this->actingAs($landlord)->post(route('admin.properties.delete', $property->id))->assertForbidden();
    expect(Property::find($property->id))->not->toBeNull();
});

it('records a tenant refund once, and the tenant loses what it unlocked', function () {
    $admin = cleanUser('Admin');
    $tenant = cleanUser('Customer');
    $payment = TenantPayment::create(['user_id' => $tenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Paid', 'paid_at' => now()]);
    expect(\App\Support\TenantAccess::hasPaidMatchingFee($tenant->id))->toBeTrue();

    $this->actingAs($admin)->post(route('admin.tenant-payments.refund', $payment->id))->assertSessionHas('success');
    expect($payment->fresh()->status)->toBe('Refunded');
    expect(\App\Support\TenantAccess::hasPaidMatchingFee($tenant->id))->toBeFalse();

    $this->actingAs($admin)->post(route('admin.tenant-payments.refund', $payment->id))->assertSessionHas('error');
});

it('records a landlord introduction refund and hides the tenant details again', function () {
    $admin = cleanUser('Admin');
    $landlord = cleanUser('Landlord');
    $tenant = cleanUser('Customer');
    $match = cleanMatch($tenant, cleanProperty($landlord), ['intro_status' => 'Paid', 'intro_amount' => 250]);

    $this->actingAs($admin)->post(route('admin.introductions.refund', $match->id))->assertSessionHas('success');

    expect($match->fresh()->intro_status)->toBe('Refunded');
    expect($match->fresh()->introPaid())->toBeFalse();
});

it('keeps an account that has paid fees, and cleans up one that has not', function () {
    $admin = cleanUser('Admin');
    $paidTenant = cleanUser('Customer');
    TenantPayment::create(['user_id' => $paidTenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Paid', 'paid_at' => now()]);

    $this->actingAs($admin)->post(route('admin.users.delete', $paidTenant->id))->assertSessionHas('error');
    expect(User::find($paidTenant->id))->not->toBeNull();

    $freeTenant = cleanUser('Customer');
    RentalNeed::create(['user_id' => $freeTenant->id, 'areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Open']);
    TenantPayment::create(['user_id' => $freeTenant->id, 'type' => 'matching_fee', 'amount' => 99, 'status' => 'Pending']);

    $this->actingAs($admin)->post(route('admin.users.delete', $freeTenant->id))->assertSessionHas('success');
    expect(User::find($freeTenant->id))->toBeNull();
    expect(RentalNeed::where('user_id', $freeTenant->id)->count())->toBe(0);
    expect(TenantPayment::where('user_id', $freeTenant->id)->count())->toBe(0);
});
