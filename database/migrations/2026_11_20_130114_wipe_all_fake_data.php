<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // This migration used to delete data with truncate(), which fails on the live
    // database (foreign keys) and would also be dangerous to run again.
    // It now does nothing. To clear test data, run:  php artisan app:clear-data --force
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
