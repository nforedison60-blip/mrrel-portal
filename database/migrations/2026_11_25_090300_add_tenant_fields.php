<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('viewing_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('viewing_requests', 'mrrel_attends')) {
                $table->boolean('mrrel_attends')->default(false);
            }
            if (!Schema::hasColumn('viewing_requests', 'rental_need_id')) {
                $table->unsignedBigInteger('rental_need_id')->nullable()->index();
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'geo_lat')) {
                $table->decimal('geo_lat', 10, 7)->nullable();
                $table->decimal('geo_lng', 10, 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('viewing_requests', function (Blueprint $table) {
            $table->dropColumn(['mrrel_attends', 'rental_need_id']);
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['geo_lat', 'geo_lng']);
        });
    }
};
