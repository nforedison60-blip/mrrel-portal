<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_id to properties (Shortlist)
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'user_id')) {
                $table->foreignId('user_id')->nullable();
            }
        });

        // Add user_id to problems (Disputes)
        Schema::table('problems', function (Blueprint $table) {
            if (!Schema::hasColumn('problems', 'user_id')) {
                $table->foreignId('user_id')->nullable();
            }
        });

        // Note: Rental fees are usually linked to properties, but if you have a separate table, 
        // you would add user_id to it here. For now, we filter by the property owner.
    }

    public function down(): void
    {
        // Not needed
    }
};