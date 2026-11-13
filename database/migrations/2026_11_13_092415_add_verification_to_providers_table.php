<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'id_document')) {
                $table->string('id_document')->nullable();
            }
            if (!Schema::hasColumn('providers', 'vehicle_document')) {
                $table->string('vehicle_document')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['id_document', 'vehicle_document']);
        });
    }
};