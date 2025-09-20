<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register tenant routes
            Route::middleware([
                'web',
                \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
                \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
            ])->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your custom middleware here
        $middleware->alias([
            'check.tenant.status' => \App\Http\Middleware\CheckTenantStatus::class,
        ]);
        
        // Add tenancy middleware to web group
        $middleware->web(append: [
            \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
