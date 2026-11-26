<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * One-time clean-up before the official launch.
 *
 * Without --confirm it only SHOWS what would be removed (a dry run).
 * With --confirm it deletes every test record and every account except the ones kept.
 */
class LaunchClean extends Command
{
    protected $signature = 'app:launch-clean
        {--keep=* : Email addresses to keep (they become Admins). Defaults to the two launch admins.}
        {--files : Also delete uploaded files (photos and private documents)}
        {--confirm : Really delete. Without this the command only shows what it would do}';

    protected $description = 'Remove all test data and all accounts except the kept admins, ready for launch';

    /** Data tables, children first. Only those that exist are touched. */
    private const TABLES = [
        'provider_locations', 'problems', 'reviews', 'messages', 'viewing_requests',
        'property_matches', 'tenant_payments', 'invoices', 'bookings', 'quotes',
        'service_requests', 'rental_needs', 'properties', 'customers', 'providers',
    ];

    private const DEFAULT_KEEP = ['makamumihlovo@gmail.com', 'nforedison60@gmail.com'];

    public function handle(): int
    {
        $keep = collect($this->option('keep') ?: self::DEFAULT_KEEP)
            ->map(fn ($e) => strtolower(trim($e)))->filter()->unique()->values();

        $kept = User::whereIn(DB::raw('LOWER(email)'), $keep)->get();
        $missing = $keep->diff($kept->map(fn ($u) => strtolower($u->email)));

        if ($missing->isNotEmpty()) {
            $this->error('These accounts do not exist yet, so nothing was changed: ' . $missing->implode(', '));
            $this->line('Register them on the site first, then run this again.');
            return self::FAILURE;
        }

        $tables = array_values(array_filter(self::TABLES, fn ($t) => Schema::hasTable($t)));
        $toDelete = User::whereNotIn('id', $kept->pluck('id'))->count();

        $this->line('');
        $this->info($this->option('confirm') ? 'LAUNCH CLEAN-UP' : 'DRY RUN (nothing will be deleted)');
        $this->line('Keeping and making Admin: ' . $kept->pluck('email')->implode(', '));
        $this->line("Accounts to delete: {$toDelete}");
        foreach ($tables as $t) {
            $this->line(sprintf('  %-20s %d rows', $t, DB::table($t)->count()));
        }
        if ($this->option('files')) {
            $this->line('Uploaded files to delete: ' . count($this->uploadedFiles()));
        }

        if (! $this->option('confirm')) {
            $this->line('');
            $this->warn('Nothing was deleted. Take a database backup first, then run again with --confirm to do it for real.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($tables, $kept) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('TRUNCATE TABLE ' . implode(', ', $tables) . ' RESTART IDENTITY CASCADE');
            } else {
                Schema::disableForeignKeyConstraints();
                foreach ($tables as $t) {
                    DB::table($t)->delete();
                }
                Schema::enableForeignKeyConstraints();
            }

            User::whereNotIn('id', $kept->pluck('id'))->delete();

            foreach ($kept as $u) {
                $u->role = 'Admin';
                $u->email_verified_at ??= now();
                $u->save();
            }

            foreach (['sessions', 'password_reset_tokens', 'jobs', 'failed_jobs'] as $t) {
                if (Schema::hasTable($t)) {
                    $t === 'sessions'
                        ? DB::table($t)->whereNotIn('user_id', $kept->pluck('id'))->orWhereNull('user_id')->delete()
                        : DB::table($t)->delete();
                }
            }
        });

        if ($this->option('files')) {
            foreach ($this->uploadedFiles() as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
        }

        $this->info('Done. Only the kept admin accounts remain, and all test data is gone.');
        return self::SUCCESS;
    }

    /** @return array<int, array{0:string,1:string}> */
    private function uploadedFiles(): array
    {
        $out = [];
        foreach (['private_docs', 'public'] as $disk) {
            try {
                foreach (Storage::disk($disk)->allFiles() as $f) {
                    if (! str_starts_with(basename($f), '.')) {
                        $out[] = [$disk, $f];
                    }
                }
            } catch (\Throwable $e) {
                // disk not available here: skip it
            }
        }
        return $out;
    }
}
