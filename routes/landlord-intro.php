<?php

use App\Http\Controllers\Admin\IntroductionsController;
use App\Http\Controllers\Landlord\LandlordIntroController;
use Illuminate\Support\Facades\Route;

// The landlord side of finding a tenant (the signed Ozow notify goes to /payment/notify, in PaymentController)
Route::middleware(['auth', 'role:Landlord'])->group(function () {
    Route::get('/landlord/tenant-matches', [LandlordIntroController::class, 'index'])->name('landlord.introductions');
    Route::post('/landlord/tenant-matches/{propertyMatch}/pay', [LandlordIntroController::class, 'pay'])->name('landlord.introductions.pay');
    Route::post('/landlord/tenant-matches/{propertyMatch}/decline', [LandlordIntroController::class, 'decline'])->name('landlord.introductions.decline');
    Route::get('/landlord/tenant-matches/payment/return', [LandlordIntroController::class, 'back'])->name('landlord.introductions.back');
    Route::get('/landlord/tenant-matches/payment/cancelled', [LandlordIntroController::class, 'cancelled'])->name('landlord.introductions.cancelled');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/introductions', [IntroductionsController::class, 'index'])->name('admin.introductions');
});
