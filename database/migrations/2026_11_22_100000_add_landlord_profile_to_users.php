<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contact and agency details for landlords. Safe to run twice: each column is only added if missing.
        foreach ([
            'phone'         => 30,
            'landlord_type' => 20,
            'agency_name'   => 150,
            'ppra_number'   => 50,
        ] as $column => $length) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column, $length) {
                    $table->string($column, $length)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // Nothing to undo
    }
};
