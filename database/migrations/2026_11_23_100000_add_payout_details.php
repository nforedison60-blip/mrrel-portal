<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'bank_name')) {
                $table->string('bank_name')->nullable();
                $table->string('bank_account_holder')->nullable();
                $table->text('bank_account_number')->nullable(); // stored encrypted
                $table->string('bank_account_type')->nullable();
                $table->string('bank_branch_code')->nullable();
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'paid_out_at')) {
                $table->timestamp('paid_out_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_holder', 'bank_account_number', 'bank_account_type', 'bank_branch_code']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('paid_out_at');
        });
    }
};
