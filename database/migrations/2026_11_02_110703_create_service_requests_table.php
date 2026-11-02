<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('services_needed');
            $table->string('moving_from');
            $table->string('moving_to')->nullable();
            $table->date('move_date');
            $table->string('date_flexibility');
            $table->string('item_size');
            $table->integer('items_count')->nullable();
            $table->string('budget')->nullable();
            $table->string('access_pickup');
            $table->string('access_delivery');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};