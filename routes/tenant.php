<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\PropertyController;
use App\Http\Controllers\Tenant\BuildingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the tenant-specific middleware group. Now create something great!
|
*/

// All routes are already in tenant context due to bootstrap/app.php configuration
Route::middleware(['auth', 'check.tenant.status'])->group(function () {
    
    // Property management routes
    Route::prefix('properties')->name('tenant.properties.')->group(function () {
        // Main CRUD routes
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
        
        // AJAX/API routes for property management
        Route::post('/{property}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{property}/stats', [PropertyController::class, 'getStats'])->name('stats');
        Route::post('/{property}/assign-user', [PropertyController::class, 'assignUser'])->name('assign-user');
        Route::post('/{property}/remove-user', [PropertyController::class, 'removeUser'])->name('remove-user');
    });
    
    // Building management routes (AJAX endpoints)
    Route::prefix('buildings')->name('tenant.buildings.')->group(function () {
        Route::post('/', [BuildingController::class, 'store'])->name('store');
        Route::put('/{building}', [BuildingController::class, 'update'])->name('update');
        Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('destroy');
    });
    

    

    
    // Add other tenant routes here as they're implemented...
    
    // Dashboard route (tenant-specific)
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('tenant.dashboard');
    
});