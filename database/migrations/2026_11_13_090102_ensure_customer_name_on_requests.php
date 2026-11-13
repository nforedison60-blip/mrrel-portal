<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safely check if the column exists before adding it
        if (!Schema::hasColumn('service_requests', 'customer_name')) {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->string('customer_name')->nullable();
            });
        }
    }

    public function down(): void
    {
        // No need to drop it
    }
};