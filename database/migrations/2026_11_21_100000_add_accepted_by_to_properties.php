<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remembers WHICH customer accepted a property, so fees and shortlist are per person
        if (! Schema::hasColumn('properties', 'accepted_by')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->unsignedBigInteger('accepted_by')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Nothing to undo
    }
};
