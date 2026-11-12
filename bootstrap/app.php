<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tell Laravel to allow PayFast to ping our webhook without a CSRF token
        $middleware->validateCsrfTokens(except: [
            'payment/notify',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();