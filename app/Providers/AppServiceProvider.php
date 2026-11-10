<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force local to use public disk if no AWS keys are present
        if (app()->environment('local') && !env('AWS_ACCESS_KEY_ID')) {
            config(['filesystems.default' => 'public']);
            config(['filesystems.disks.s3' => config('filesystems.disks.public')]);
        }
    }
}