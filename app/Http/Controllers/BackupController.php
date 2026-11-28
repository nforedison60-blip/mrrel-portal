<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Admin backup: download the system's data as spreadsheets (CSV opens in Excel and Google Sheets).
 * Passwords, login tokens and bank account numbers are never included.
 */
class BackupController extends Controller
{
    // key => [label, table, what it holds]
    private const SETS = [
        'users'            => ['Users', 'users', 'Everyone with an account: name, email, role, phone, when they joined'],
        'providers'        => ['Providers (movers)', 'providers', 'Mover profiles and approval status'],
        'customers'        => ['Customers', 'customers', 'Customer records'],
        'properties'       => ['Properties', 'properties', 'Homes listed by landlords'],
        'rental_needs'     => ['Tenant requirements', 'rental_needs', 'What each tenant is looking for'],
        'property_matches' => ['Matches and introductions', 'property_matches', 'Homes matched to tenants and the introduction status'],
        'viewing_requests' => ['Viewing requests', 'viewing_requests', 'Viewings tenants asked for'],
        'service_requests' => ['Move requests', 'service_requests', 'Moving requests from customers'],
        'quotes'           => ['Quotes', 'quotes', 'Quotes sent by movers'],
        'bookings'         => ['Bookings', 'bookings', 'Moving bookings, amounts and payment status'],
        'tenant_payments'  => ['Tenant payments', 'tenant_payments', 'Matching fees and viewing attendance fees'],
        'invoices'         => ['Landlord invoices', 'invoices', 'Invoices and receipts'],
        'problems'         => ['Disputes and problems', 'problems', 'Problems reported on bookings'],
        'reviews'          => ['Reviews', 'reviews', 'Customer reviews of movers'],
        'messages'         => ['Messages', 'messages', 'Messages between customers and providers'],
    ];

    // Never leave the system in a spreadsheet
    private const HIDDEN = [
        'password', 'remember_token', 'bank_account_number', 'two_factor_secret',
        'two_factor_recovery_codes', 'api_token',
    ];

    public function index()
    {
        $sets = [];
        foreach (self::SETS as $key => [$label, $table, $about]) {
            $sets[] = ['key' => $key, 'label' => $label, 'about' => $about,
                'count' => Schema::hasTable($table) ? DB::table($table)->count() : 0];
        }

        return view('admin.backup', ['sets' => $sets]);
    }

    public function download(string $key)
    {
        abort_unless(isset(self::SETS[$key]) && Schema::hasTable(self::SETS[$key][1]), 404);
        $table = self::SETS[$key][1];

        Log::info('Admin downloaded a data export', ['set' => $key, 'admin' => auth()->id()]);

        return response()->streamDownload(function () use ($table) {
            $out = fopen('php://output', 'w');
            $this->writeCsv($out, $table);
            fclose($out);
        }, 'mrrel-' . $key . '-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function all()
    {
        if (! class_exists(\ZipArchive::class)) {
            return redirect()->route('admin.backup')->with('error', 'The server cannot make a ZIP file. Download the spreadsheets one by one instead.');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'mrrel-backup-');
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::OVERWRITE);

        $temps = [];
        foreach (self::SETS as $key => [$label, $table]) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $tmp = tempnam(sys_get_temp_dir(), 'mrrel-csv-');
            $temps[] = $tmp;
            $h = fopen($tmp, 'w');
            $this->writeCsv($h, $table);
            fclose($h);
            $zip->addFile($tmp, $key . '.csv');
        }
        $zip->close();
        foreach ($temps as $t) {
            @unlink($t);
        }

        Log::info('Admin downloaded a full data backup', ['admin' => auth()->id()]);

        return response()->download($zipPath, 'mrrel-backup-' . now()->format('Y-m-d') . '.zip')->deleteFileAfterSend(true);
    }

    private function writeCsv($handle, string $table): void
    {
        fwrite($handle, "\xEF\xBB\xBF"); // so Excel shows accents and symbols correctly

        $columns = array_values(array_diff(Schema::getColumnListing($table), self::HIDDEN));
        fputcsv($handle, $columns);

        DB::table($table)->select($columns)->orderBy('id')->chunk(500, function ($rows) use ($handle, $columns) {
            foreach ($rows as $row) {
                $line = [];
                foreach ($columns as $c) {
                    $line[] = $this->cell($row->{$c} ?? null);
                }
                fputcsv($handle, $line);
            }
        });
    }

    // A cell that starts with = + - @ would run as a formula in Excel, so mark it as plain text
    private function cell($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        $text = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $text) ? "'" . $text : $text;
    }
}
