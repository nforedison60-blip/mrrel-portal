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
        \App\Support\Notifications::register(); // emails that tell people something happened

        // Links in emails (verify, reset) must be https on the live site or their signature fails with Forbidden
        if (! app()->environment(['local', 'testing'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // If we are on local (testing), and don't have AWS keys, 
        // force it to use the public disk so it doesn't crash.
        if (app()->environment('local') && !env('AWS_BUCKET')) {
            config(['filesystems.default' => 'public']);
            config(['filesystems.disks.s3' => config('filesystems.disks.public')]);
        }
    }
}