<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // When the provider agreed to each statement. Safe to run twice.
        foreach (['consent_display_at', 'consent_verification_at', 'consent_terms_at'] as $column) {
            if (! Schema::hasColumn('providers', $column)) {
                Schema::table('providers', function (Blueprint $table) use ($column) {
                    $table->timestamp($column)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // Nothing to undo
    }
};
