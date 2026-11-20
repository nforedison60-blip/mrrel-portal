<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Clear all the fake data from the live database
        DB::table('providers')->truncate();
        DB::table('service_requests')->truncate();
        DB::table('quotes')->truncate();
        DB::table('bookings')->truncate();
        DB::table('properties')->truncate();
        DB::table('problems')->truncate();
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};