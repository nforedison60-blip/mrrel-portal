<?php

use App\Http\Controllers\Admin\TenantMatchingController;
use App\Http\Controllers\Admin\TenantPaymentsController;
use App\Http\Controllers\Tenant\TenantMatchController;
use App\Http\Controllers\Tenant\TenantNeedController;
use App\Http\Controllers\Tenant\TenantPaymentController;
use App\Http\Controllers\Tenant\TenantViewingController;
use Illuminate\Support\Facades\Route;

/*
| The tenant process (finding a home). This file is loaded from the END of routes/web.php. When two routes have the same address, the one registered LAST wins,
| so the routes marked "takes over" replace the older routes that use the same address. They also take over the old route NAMES, because the old route is dropped entirely.
*/

Route::middleware(['auth', 'role:Customer'])->group(function () {
    // Step 1
    Route::get('/find-a-home', [TenantNeedController::class, 'create'])->name('customer.needs.create');
    Route::post('/find-a-home', [TenantNeedController::class, 'store'])->name('customer.needs.store');
    Route::get('/find-a-home/edit', [TenantNeedController::class, 'edit'])->name('customer.needs.edit');
    Route::put('/find-a-home', [TenantNeedController::class, 'update'])->name('customer.needs.update');

    // Steps 2 and 3
    Route::get('/my-matches', [TenantMatchController::class, 'index'])->name('customer.matches');
    Route::post('/tenant/pay/matching-fee', [TenantPaymentController::class, 'startMatchingFee'])->name('tenant.pay.matching');

    // Step 5
    Route::get('/tenant/viewings/{viewing}/attendance', [TenantPaymentController::class, 'attendanceShow'])->name('tenant.attendance.show');
    Route::post('/tenant/viewings/{viewing}/attendance/pay', [TenantPaymentController::class, 'startAttendance'])->name('tenant.attendance.pay');

    // Ozow return pages
    Route::get('/tenant/payments/back', [TenantPaymentController::class, 'returned'])->name('tenant.payments.back');
    Route::get('/tenant/payments/cancelled', [TenantPaymentController::class, 'cancelled'])->name('tenant.payments.cancelled');

    // Steps 4 and 6: these take over the older /viewing-request and accept routes
    Route::post('/viewing-request', [TenantViewingController::class, 'store'])->name('customer.viewing.store');
    Route::post('/shortlist/{property}/accept', [TenantViewingController::class, 'accept'])->name('customer.shortlist.accept');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    // takes over the older static /admin/matching page
    Route::get('/admin/matching', [TenantMatchingController::class, 'index'])->name('admin.matching');
    Route::post('/admin/matching/{need}/send', [TenantMatchingController::class, 'send'])->name('admin.tenant-matching.send');
    Route::post('/admin/matching/matches/{propertyMatch}/unmatch', [TenantMatchingController::class, 'unmatch'])->name('admin.tenant-matching.unmatch');
    Route::get('/admin/tenant-payments', [TenantPaymentsController::class, 'index'])->name('admin.tenant-payments');
});
