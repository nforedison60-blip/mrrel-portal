<?php

use App\Http\Controllers\BankTransferController;
use Illuminate\Support\Facades\Route;

// Paying by direct bank transfer (proof of payment is emailed, an admin confirms)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pay/bank/{reference}', [BankTransferController::class, 'show'])->name('bank.show');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/bank-payments', [BankTransferController::class, 'adminIndex'])->name('admin.bank-payments');
    Route::post('/admin/bank-payments/confirm', [BankTransferController::class, 'adminConfirm'])->name('admin.bank-payments.confirm');
});
