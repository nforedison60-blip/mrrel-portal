<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearData extends Command
{
    protected $signature = 'app:clear-data {--force : Skip the confirmation question}';

    protected $description = 'Delete all test data (requests, quotes, bookings, properties...) but KEEP user accounts';

    public function handle()
    {
        if (! $this->option('force') && ! $this->confirm('This deletes ALL requests, quotes, bookings, properties, messages, reviews and problems. User accounts are kept. Continue?')) {
            $this->info('Cancelled. Nothing was deleted.');
            return self::SUCCESS;
        }

        $tables = [
            'provider_locations', 'problems', 'reviews', 'messages', 'viewing_requests',
            'bookings', 'quotes', 'service_requests', 'properties', 'customers', 'providers',
        ];

        // Only the tables that really exist
        $tables = array_values(array_filter($tables, fn ($t) => Schema::hasTable($t)));

        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: truncate everything in one statement
            DB::statement('TRUNCATE TABLE ' . implode(', ', $tables) . ' RESTART IDENTITY CASCADE');
        } else {
            // MySQL / SQLite: switch foreign keys off while clearing
            Schema::disableForeignKeyConstraints();
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
            Schema::enableForeignKeyConstraints();
        }

        foreach ($tables as $table) {
            $this->line("Cleared: {$table}");
        }

        $this->info('Done. Users were not touched.');
        return self::SUCCESS;
    }
}
