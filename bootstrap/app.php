<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // DISABLED TENANCY FOR NOW
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.tenant.status' => \App\Http\Middleware\CheckTenantStatus::class,
        ]);
        // DISABLED TENANCY MIDDLEWARE
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
