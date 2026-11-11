<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            // Check if the column doesn't exist before adding it
            if (!Schema::hasColumn('problems', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable();
            }

            if (!Schema::hasColumn('problems', 'subject')) {
                $table->string('subject')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            if (Schema::hasColumn('problems', 'booking_id')) {
                $table->dropColumn('booking_id');
            }
            if (Schema::hasColumn('problems', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};