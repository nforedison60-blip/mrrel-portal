<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            return;
        }

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();           // INV-2026-00001
            $table->unsignedBigInteger('user_id')->index();            // the landlord being invoiced
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('Unpaid');               // Unpaid, Paid, Cancelled
            $table->date('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();              // Ozow or EFT
            $table->string('payment_reference')->nullable();           // Ozow transaction id or the bank reference
            $table->string('receipt_number')->nullable();              // RCT-2026-00001
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Invoices are financial records, so they are never dropped automatically
    }
};
