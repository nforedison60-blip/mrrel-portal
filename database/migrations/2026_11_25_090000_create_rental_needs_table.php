<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rental_needs')) {
            Schema::create('rental_needs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('areas');
                $table->unsignedInteger('budget_min')->nullable();
                $table->unsignedInteger('budget_max');
                $table->string('bedrooms');
                $table->date('move_in_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('Open')->index(); // Open, Matched, Closed
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_needs');
    }
};
