<?php

use App\Http\Controllers\AdminInvoiceController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// Landlord invoices (the signed Ozow notify for these goes to /payment/notify, in PaymentController)
Route::middleware(['auth', 'role:Landlord'])->group(function () {
    Route::get('/landlord/invoices', [InvoiceController::class, 'index'])->name('landlord.invoices');
    Route::get('/landlord/invoices/{invoice}', [InvoiceController::class, 'show'])->name('landlord.invoices.show');
    Route::get('/landlord/invoices/{invoice}/receipt', [InvoiceController::class, 'receipt'])->name('landlord.invoices.receipt');
    Route::post('/landlord/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('landlord.invoices.pay');
    Route::get('/landlord/payment/return', [InvoiceController::class, 'back'])->name('landlord.payment.return');
    Route::get('/landlord/payment/cancelled', [InvoiceController::class, 'cancelled'])->name('landlord.payment.cancelled');
});

// Admin: send invoices and record EFT payments
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/invoices', [AdminInvoiceController::class, 'index'])->name('admin.invoices');
    Route::post('/admin/invoices', [AdminInvoiceController::class, 'store'])->name('admin.invoices.store');
    Route::post('/admin/invoices/{invoice}/paid', [AdminInvoiceController::class, 'markPaid'])->name('admin.invoices.paid');
    Route::post('/admin/invoices/{invoice}/cancel', [AdminInvoiceController::class, 'cancel'])->name('admin.invoices.cancel');
});
