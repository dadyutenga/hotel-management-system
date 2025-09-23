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
    
    // Room Types management routes
    Route::prefix('room-types')->name('tenant.room-types.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'store'])->name('store');
        Route::get('/{roomType}', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'show'])->name('show');
        Route::get('/{roomType}/edit', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'edit'])->name('edit');
        Route::put('/{roomType}', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'update'])->name('update');
        Route::delete('/{roomType}', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'destroy'])->name('destroy');
        Route::put('/{roomType}/status', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'updateStatus'])->name('update-status');
        Route::get('/property/{property}/types', [\App\Http\Controllers\Users\Tenant\RoomTypesController::class, 'getRoomTypesByProperty'])->name('by-property');
    });
    
    // Rooms management routes
    Route::prefix('rooms')->name('tenant.rooms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'store'])->name('store');
        Route::get('/{room}', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'show'])->name('show');
        Route::get('/{room}/edit', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'edit'])->name('edit');
        Route::put('/{room}', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'update'])->name('update');
        Route::delete('/{room}', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'destroy'])->name('destroy');
        Route::put('/{room}/status', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'updateStatus'])->name('update-status');
        Route::get('/property/{property}/buildings', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'getBuildings'])->name('buildings');
        Route::get('/building/{building}/floors', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'getFloors'])->name('floors');
        Route::get('/property/{property}/room-types', [\App\Http\Controllers\Users\Tenant\RoomsController::class, 'getRoomTypes'])->name('room-types');
    });
    
    // Add other tenant routes here as they're implemented...
    
    // Dashboard route (tenant-specific)
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('tenant.dashboard');
    
});