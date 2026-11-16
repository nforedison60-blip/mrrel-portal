<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'extras_status')) {
                $table->string('extras_status')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'job_confirmed')) {
                $table->boolean('job_confirmed')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['extras_status', 'job_confirmed']);
        });
    }
};