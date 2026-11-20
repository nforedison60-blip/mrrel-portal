<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Each person now owns their own rows, so nobody sees someone else's data.
        $this->addNullable('customers', 'user_id');          // which login owns this customer profile
        $this->addNullable('bookings', 'user_id');           // the customer who made the booking
        $this->addNullable('viewing_requests', 'user_id');   // the customer who asked for the viewing
        $this->addNullable('quotes', 'provider_user_id');    // the provider login who sent the quote
        $this->addNullable('reviews', 'provider_user_id');   // the provider login being reviewed
        $this->addNullable('reviews', 'user_id');            // the customer who wrote the review

        // This column was missing from the migrations (it only existed in your old database)
        if (! Schema::hasColumn('service_requests', 'status')) {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->string('status')->default('Active');
            });
        }
    }

    public function down(): void
    {
        // Nothing to undo
    }

    private function addNullable(string $tableName, string $column): void
    {
        if (! Schema::hasColumn($tableName, $column)) {
            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->unsignedBigInteger($column)->nullable();
            });
        }
    }
};
