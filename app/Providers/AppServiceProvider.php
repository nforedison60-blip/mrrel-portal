<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // If we are testing locally and don't have S3 keys, 
        // tell Laravel to use the local 'public' disk instead so it doesn't crash.
        if (app()->environment('local') && !env('AWS_ACCESS_KEY_ID')) {
            config(['filesystems.disks.s3' => config('filesystems.disks.public')]);
        }
    }
}