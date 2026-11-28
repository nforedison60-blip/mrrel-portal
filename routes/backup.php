<?php

use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::get('/admin/backup/all', [BackupController::class, 'all'])->name('admin.backup.all');
    Route::get('/admin/backup/{key}', [BackupController::class, 'download'])->name('admin.backup.download');
});
