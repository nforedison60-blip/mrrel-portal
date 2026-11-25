<?php

use App\Http\Controllers\LandlordPropertyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Landlord'])->group(function () {
    Route::get('/landlord/properties/{property}/edit', [LandlordPropertyController::class, 'edit'])->name('landlord.properties.edit');
    Route::post('/landlord/properties/{property}', [LandlordPropertyController::class, 'update'])->name('landlord.properties.update');
});
