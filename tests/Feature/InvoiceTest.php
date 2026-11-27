<?php

use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;

function makeUser(string $role, string $email): User
{
    return User::factory()->create(['name' => ucfirst($role), 'email' => $email, 'password' => 'password1', 'role' => $role]);
}

function makeProperty(User $landlord, string $status = 'Under Offer'): Property
{
    return Property::create([
        'user_id' => $landlord->id, 'title' => '2 bed flat', 'property_type' => 'Flat', 'city' => 'Cape Town',
        'suburb' => 'Claremont', 'bedrooms' => '2', 'bathrooms' => '1', 'rent' => 10000, 'deposit' => 10000, 'status' => $status,
    ]);
}

// Builds a message the way Ozow signs it
function ozowMessage(string $reference, string $amount, string $status = 'Complete'): array
{
    config(['services.ozow.site_code' => 'TEST-SITE', 'services.ozow.private_key' => 'secret-key', 'services.ozow.is_test' => false]);
    $data = [
        'SiteCode' => 'TEST-SITE', 'TransactionId' => 'tx-123', 'TransactionReference' => $reference,
        'Amount' => $amount, 'Status' => $status, 'CurrencyCode' => 'ZAR', 'IsTest' => 'false', 'StatusMessage' => 'ok',
    ];
    $data['Hash'] = hash('sha512', strtolower(implode('', $data) . 'secret-key'));

    return $data;
}

test('admin can send an invoice and the landlord sees it', function () {
    $admin = makeUser('Admin', 'admin@example.com');
    $landlord = makeUser('Landlord', 'landlord@example.com');
    $property = makeProperty($landlord);

    $this->actingAs($admin)->post('/admin/invoices', ['property_id' => $property->id, 'amount' => '1000'])
        ->assertRedirect(route('admin.invoices'));

    $invoice = Invoice::firstOrFail();
    expect($invoice->user_id)->toBe($landlord->id)->and($invoice->status)->toBe('Unpaid')->and($invoice->number)->toStartWith('INV-');

    $this->actingAs($landlord)->get('/landlord/invoices')->assertOk()->assertSee($invoice->number);
});

test('a landlord cannot open someone elses invoice', function () {
    $owner = makeUser('Landlord', 'owner@example.com');
    $other = makeUser('Landlord', 'other@example.com');
    $invoice = Invoice::issue(makeProperty($owner), 1000);

    $this->actingAs($other)->get("/landlord/invoices/{$invoice->id}")->assertNotFound();
});

test('a signed Ozow message marks the invoice paid once and issues a receipt', function () {
    $landlord = makeUser('Landlord', 'landlord@example.com');
    $invoice = Invoice::issue(makeProperty($landlord), 1000);

    $this->post('/payment/notify', ozowMessage($invoice->gatewayReference(), '1000.00'))->assertOk();
    $this->post('/payment/notify', ozowMessage($invoice->gatewayReference(), '1000.00'))->assertOk(); // sent twice

    $invoice->refresh();
    expect($invoice->status)->toBe('Paid')->and($invoice->payment_method)->toBe('Ozow')->and($invoice->receipt_number)->toStartWith('RCT-');

    $this->actingAs($landlord)->get("/landlord/invoices/{$invoice->id}/receipt")->assertOk()->assertSee($invoice->receipt_number);
});

test('the wrong amount or a bad signature does not mark it paid', function () {
    $invoice = Invoice::issue(makeProperty(makeUser('Landlord', 'landlord@example.com')), 1000);

    $this->post('/payment/notify', ozowMessage($invoice->gatewayReference(), '1.00'))->assertOk();
    expect($invoice->fresh()->status)->toBe('Unpaid');

    $bad = ozowMessage($invoice->gatewayReference(), '1000.00');
    $bad['Hash'] = 'not-the-real-hash';
    $this->post('/payment/notify', $bad)->assertStatus(400);
    expect($invoice->fresh()->status)->toBe('Unpaid');
});

test('admin can record an EFT payment', function () {
    $admin = makeUser('Admin', 'admin@example.com');
    $invoice = Invoice::issue(makeProperty(makeUser('Landlord', 'landlord@example.com')), 1000);

    $this->actingAs($admin)->post("/admin/invoices/{$invoice->id}/paid", ['reference' => 'FNB 12345'])
        ->assertRedirect(route('admin.invoices'));

    $invoice->refresh();
    expect($invoice->status)->toBe('Paid')->and($invoice->payment_method)->toBe('EFT')->and($invoice->payment_reference)->toBe('FNB 12345');
});
