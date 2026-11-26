<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('property_matches')) {
            Schema::create('property_matches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rental_need_id')->index();
                $table->unsignedBigInteger('property_id')->index();
                $table->unsignedBigInteger('user_id')->index();      // the tenant
                $table->unsignedBigInteger('matched_by')->nullable()->index(); // the admin
                $table->string('status')->default('Offered');        // Offered, Declined
                $table->timestamps();
                $table->unique(['rental_need_id', 'property_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_matches');
    }
};
