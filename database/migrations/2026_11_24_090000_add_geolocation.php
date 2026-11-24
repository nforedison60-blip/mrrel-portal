<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'pickup_lat')) {
                $table->decimal('pickup_lat', 10, 7)->nullable();
                $table->decimal('pickup_lng', 10, 7)->nullable();
                $table->index(['pickup_lat', 'pickup_lng']);
            }
        });

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'base_lat')) {
                $table->string('base_label')->nullable();
                $table->decimal('base_lat', 10, 7)->nullable();
                $table->decimal('base_lng', 10, 7)->nullable();
                $table->unsignedSmallInteger('service_radius_km')->default(50);
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['pickup_lat', 'pickup_lng']);
            $table->dropColumn(['pickup_lat', 'pickup_lng']);
        });
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['base_label', 'base_lat', 'base_lng', 'service_radius_km']);
        });
    }
};
