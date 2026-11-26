<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Admins already exist and must never be locked out by the new email check.
    public function up(): void
    {
        DB::table('users')->where('role', 'Admin')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
    }
};
