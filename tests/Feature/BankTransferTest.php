<?php

use App\Models\Booking;
use App\Models\Property;
use App\Models\PropertyMatch;
use App\Models\Quote;
use App\Models\RentalNeed;
use App\Models\TenantPayment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config([
        'mrrel.bank' => ['name' => 'FNB', 'holder' => 'MRREL (PTY) LTD', 'account' => '63236576625', 'type' => 'Gold Business Account', 'branch' => '203809', 'swift' => 'FIRNZAJJ'],
        'mrrel.pop_email' => 'relocations@mrrel.co.za',
        'introductions.fee' => 250,
    ]);
});

function bankBooking(User $customer): Booking
{
    $mover = User::factory()->create(['role' => 'Provider']);
    $quote = Quote::create(['provider_name' => 'Mover Mike', 'provider_user_id' => $mover->id, 'total_price' => 500, 'price_type' => 'Fixed', 'validity' => '7 days']);

    return Booking::create(['quote_id' => $quote->id, 'user_id' => $customer->id, 'customer_name' => $customer->name,
        'total_price' => 500, 'status' => 'Awaiting payment', 'tracking_status' => 'Not started']);
}

test('a customer sees the bank details with the booking number as reference', function () {
    $customer = User::factory()->create(['role' => 'Customer']);
    $booking = bankBooking($customer);
    $ref = 'BKG-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

    $this->actingAs($customer)->get(route('bank.show', $ref))
        ->assertOk()
        ->assertSee($ref)
        ->assertSee('63236576625')
        ->assertSee('203809')
        ->assertSee('relocations@mrrel.co.za');
});

test('nobody can open someone else\'s bank page', function () {
    $owner = User::factory()->create(['role' => 'Customer']);
    $other = User::factory()->create(['role' => 'Customer']);
    $booking = bankBooking($owner);

    $this->actingAs($other)->get(route('bank.show', 'BKG-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT)))->assertNotFound();
});

test('the bank button on the matching fee makes a pending payment and opens the bank page', function () {
    $tenant = User::factory()->create(['role' => 'Customer']);
    $landlord = User::factory()->create(['role' => 'Landlord']);
    $property = Property::create([
        'user_id' => $landlord->id, 'title' => 'Flat', 'description' => 'x', 'property_type' => 'Apartment', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 9000, 'deposit' => 9000, 'status' => 'Approved',
    ]);
    $need = RentalNeed::create(['user_id' => $tenant->id, 'areas' => 'Claremont', 'budget_max' => 10000, 'bedrooms' => '2', 'status' => 'Matched']);
    PropertyMatch::create(['rental_need_id' => $need->id, 'property_id' => $property->id, 'user_id' => $tenant->id, 'status' => 'Offered']);

    $response = $this->actingAs($tenant)->post(route('tenant.pay.matching'), ['method' => 'bank']);

    $payment = TenantPayment::where('user_id', $tenant->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->status)->toBe('Pending');
    $response->assertRedirect(route('bank.show', $payment->reference()));
});

test('an admin confirms a bank payment and the booking becomes paid', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'Admin']);
    $customer = User::factory()->create(['role' => 'Customer']);
    $booking = bankBooking($customer);
    $ref = 'BKG-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);

    $this->actingAs($admin)->get(route('admin.bank-payments'))->assertOk()->assertSee($ref);
    $this->actingAs($admin)->post(route('admin.bank-payments.confirm'), ['reference' => $ref])->assertRedirect(route('admin.bank-payments'));

    expect($booking->fresh()->status)->toBe('Paid');
});

test('a customer cannot confirm a bank payment', function () {
    $customer = User::factory()->create(['role' => 'Customer']);
    $booking = bankBooking($customer);

    $this->actingAs($customer)->post(route('admin.bank-payments.confirm'), ['reference' => 'BKG-' . str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT)])->assertStatus(403);
    expect($booking->fresh()->status)->toBe('Awaiting payment');
});
