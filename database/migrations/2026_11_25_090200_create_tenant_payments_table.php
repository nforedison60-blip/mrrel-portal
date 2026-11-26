<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// No foreign keys on purpose: removing a user account must never be blocked, and payment rows are kept.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tenant_payments')) {
            Schema::create('tenant_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('type');                       // matching_fee, viewing_attendance
                $table->decimal('amount', 10, 2);
                $table->string('status')->default('Pending'); // Pending, Paid, Failed, Cancelled
                $table->unsignedBigInteger('rental_need_id')->nullable()->index();
                $table->unsignedBigInteger('viewing_request_id')->nullable()->index();
                $table->string('ozow_transaction_id')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_payments');
    }
};
