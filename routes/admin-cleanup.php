<?php

use App\Http\Controllers\Admin\AdminCleanupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::post('/admin/properties/{property}/delete', [AdminCleanupController::class, 'deleteProperty'])->name('admin.properties.delete');
    Route::post('/admin/requests/{serviceRequest}/delete', [AdminCleanupController::class, 'deleteRequest'])->name('admin.requests.delete');
    Route::post('/admin/tenant-payments/{payment}/refund', [AdminCleanupController::class, 'refundTenantPayment'])->name('admin.tenant-payments.refund');
    Route::post('/admin/introductions/{propertyMatch}/refund', [AdminCleanupController::class, 'refundIntroduction'])->name('admin.introductions.refund');
});
