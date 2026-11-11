<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix problems table
        Schema::table('problems', function (Blueprint $table) {
            if (!Schema::hasColumn('problems', 'status')) {
                $table->string('status')->default('In progress');
            }
            if (!Schema::hasColumn('problems', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable();
            }
            if (!Schema::hasColumn('problems', 'subject')) {
                $table->string('subject')->nullable();
            }
        });

        // Fix service_requests table
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'status')) {
                $table->string('status')->default('Active');
            }
        });

        // Fix bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'tracking_status')) {
                $table->string('tracking_status')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'job_confirmed')) {
                $table->boolean('job_confirmed')->default(false);
            }
        });
    }

    public function down(): void
    {
        // We don't need to drop them in this case, but it's good practice
    }
};