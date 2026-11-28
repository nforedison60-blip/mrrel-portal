<?php

use App\Models\User;

test('an admin can open the backup page', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)->get(route('admin.backup'))->assertOk()->assertSee('Download full backup');
});

test('the users spreadsheet has emails but never passwords', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    User::factory()->create(['role' => 'Customer', 'email' => 'thandi@example.com', 'name' => '=HYPERLINK("http://x")']);

    $response = $this->actingAs($admin)->get(route('admin.backup.download', 'users'));
    $response->assertOk();
    $csv = $response->streamedContent();

    expect($csv)->toContain('thandi@example.com');
    expect($csv)->not->toContain('password');
    expect($csv)->toContain("'=HYPERLINK");
});

test('the full backup downloads as a zip', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $response = $this->actingAs($admin)->get(route('admin.backup.all'));
    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('mrrel-backup-');
});

test('customers cannot download backups', function () {
    $customer = User::factory()->create(['role' => 'Customer']);

    $this->actingAs($customer)->get(route('admin.backup.download', 'users'))->assertRedirect();
});

test('an unknown list is not found', function () {
    $admin = User::factory()->create(['role' => 'Admin']);

    $this->actingAs($admin)->get(route('admin.backup.download', 'secrets'))->assertNotFound();
});
