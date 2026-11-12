<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ForceFixLiveDatabase extends Command
{
    protected $signature = 'db:force-fix';
    protected $description = 'Forcefully adds missing columns to the live database';

    public function handle()
    {
        $this->info('Starting force fix...');

        // 1. Fix service_requests
        if (!Schema::hasColumn('service_requests', 'customer_name')) {
            Schema::table('service_requests', function ($table) {
                $table->string('customer_name')->nullable();
            });
            $this->info('Added customer_name to service_requests.');
        } else {
            $this->info('customer_name already exists in service_requests.');
        }

        // 2. Fix bookings
        if (!Schema::hasColumn('bookings', 'customer_name')) {
            Schema::table('bookings', function ($table) {
                $table->string('customer_name')->nullable();
            });
            $this->info('Added customer_name to bookings.');
        } else {
            $this->info('customer_name already exists in bookings.');
        }

        if (!Schema::hasColumn('bookings', 'total_price')) {
            Schema::table('bookings', function ($table) {
                $table->decimal('total_price', 10, 2)->default(0);
            });
            $this->info('Added total_price to bookings.');
        } else {
            $this->info('total_price already exists in bookings.');
        }

        $this->info('Force fix complete!');
    }
}