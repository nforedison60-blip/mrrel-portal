<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('bookings', 'tracking_status')) {
                $table->string('tracking_status')->nullable();
            }
        });

        // Fix quotes table
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'provider_name')) {
                $table->string('provider_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Not needed for this fix
    }
};