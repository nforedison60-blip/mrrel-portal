<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Links each provider record directly to its login, so verification no longer depends on matching emails.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('providers', 'user_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            });
        }

        // Link the records that already exist
        foreach (DB::table('providers')->whereNull('user_id')->get() as $provider) {
            $user = DB::table('users')->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($provider->email))])->first();
            if ($user) {
                DB::table('providers')->where('id', $provider->id)->update(['user_id' => $user->id]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('providers', 'user_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
