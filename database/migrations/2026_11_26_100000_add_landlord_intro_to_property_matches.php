<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_matches', function (Blueprint $table) {
            if (!Schema::hasColumn('property_matches', 'landlord_status')) {
                $table->string('landlord_status')->nullable()->index();      // null = not decided, Interested, Declined
                $table->timestamp('landlord_decided_at')->nullable();
                $table->string('intro_status')->nullable();                  // null, Pending, Paid
                $table->decimal('intro_amount', 10, 2)->nullable();          // the fee when the landlord started paying
                $table->string('intro_ozow_id')->nullable();
                $table->timestamp('intro_paid_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_matches', function (Blueprint $table) {
            $table->dropColumn(['landlord_status', 'landlord_decided_at', 'intro_status', 'intro_amount', 'intro_ozow_id', 'intro_paid_at']);
        });
    }
};
