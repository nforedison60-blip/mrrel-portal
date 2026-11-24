<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Up to 5 photo paths per request, stored as JSON. Safe to run twice.
        if (! Schema::hasColumn('service_requests', 'photo_paths')) {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->json('photo_paths')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Nothing to undo
    }
};
