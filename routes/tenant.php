<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\PropertyController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Tenant dashboard routes
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
        
        // Building management routes (to be implemented)
        // Route::prefix('buildings')->name('tenant.buildings.')->group(function () {
        //     Route::get('/', [BuildingController::class, 'index'])->name('index');
        //     Route::get('/create', [BuildingController::class, 'create'])->name('create');
        //     Route::post('/', [BuildingController::class, 'store'])->name('store');
        //     Route::get('/{building}', [BuildingController::class, 'show'])->name('show');
        //     Route::get('/{building}/edit', [BuildingController::class, 'edit'])->name('edit');
        //     Route::put('/{building}', [BuildingController::class, 'update'])->name('update');
        //     Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('destroy');
        // });
        
        // Floor management routes (to be implemented)
        // Route::prefix('floors')->name('tenant.floors.')->group(function () {
        //     Route::get('/', [FloorController::class, 'index'])->name('index');
        //     Route::get('/create', [FloorController::class, 'create'])->name('create');
        //     Route::post('/', [FloorController::class, 'store'])->name('store');
        //     Route::get('/{floor}', [FloorController::class, 'show'])->name('show');
        //     Route::get('/{floor}/edit', [FloorController::class, 'edit'])->name('edit');
        //     Route::put('/{floor}', [FloorController::class, 'update'])->name('update');
        //     Route::delete('/{floor}', [FloorController::class, 'destroy'])->name('destroy');
        // });
        
        // Room management routes (to be implemented)
        // Route::prefix('rooms')->name('tenant.rooms.')->group(function () {
        //     Route::get('/', [RoomController::class, 'index'])->name('index');
        //     Route::get('/create', [RoomController::class, 'create'])->name('create');
        //     Route::post('/', [RoomController::class, 'store'])->name('store');
        //     Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        //     Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        //     Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        //     Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        //     
        //     // Room status management
        //     Route::post('/{room}/change-status', [RoomController::class, 'changeStatus'])->name('change-status');
        //     Route::post('/{room}/maintenance', [RoomController::class, 'setMaintenance'])->name('maintenance');
        // });
        
        // User management routes (to be implemented)
        // Route::prefix('users')->name('tenant.users.')->group(function () {
        //     Route::get('/', [UserController::class, 'index'])->name('index');
        //     Route::get('/create', [UserController::class, 'create'])->name('create');
        //     Route::post('/', [UserController::class, 'store'])->name('store');
        //     Route::get('/{user}', [UserController::class, 'show'])->name('show');
        //     Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        //     Route::put('/{user}', [UserController::class, 'update'])->name('update');
        //     Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        //     
        //     // User status management
        //     Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        //     Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        // });
        
        // Reservation management routes (to be implemented)
        // Route::prefix('reservations')->name('tenant.reservations.')->group(function () {
        //     Route::get('/', [ReservationController::class, 'index'])->name('index');
        //     Route::get('/create', [ReservationController::class, 'create'])->name('create');
        //     Route::post('/', [ReservationController::class, 'store'])->name('store');
        //     Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        //     Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('edit');
        //     Route::put('/{reservation}', [ReservationController::class, 'update'])->name('update');
        //     Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy');
        //     
        //     // Reservation actions
        //     Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('confirm');
        //     Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
        //     Route::post('/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('check-in');
        //     Route::post('/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('check-out');
        // });
        
        // Guest management routes (to be implemented)
        // Route::prefix('guests')->name('tenant.guests.')->group(function () {
        //     Route::get('/', [GuestController::class, 'index'])->name('index');
        //     Route::get('/create', [GuestController::class, 'create'])->name('create');
        //     Route::post('/', [GuestController::class, 'store'])->name('store');
        //     Route::get('/{guest}', [GuestController::class, 'show'])->name('show');
        //     Route::get('/{guest}/edit', [GuestController::class, 'edit'])->name('edit');
        //     Route::put('/{guest}', [GuestController::class, 'update'])->name('update');
        //     Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('destroy');
        // });
        
        // Housekeeping routes (to be implemented)
        // Route::prefix('housekeeping')->name('tenant.housekeeping.')->group(function () {
        //     Route::get('/', [HousekeepingController::class, 'index'])->name('index');
        //     Route::get('/tasks', [HousekeepingController::class, 'tasks'])->name('tasks');
        //     Route::post('/tasks', [HousekeepingController::class, 'createTask'])->name('create-task');
        //     Route::put('/tasks/{task}', [HousekeepingController::class, 'updateTask'])->name('update-task');
        //     Route::post('/tasks/{task}/complete', [HousekeepingController::class, 'completeTask'])->name('complete-task');
        // });
        
        // Maintenance routes (to be implemented)
        // Route::prefix('maintenance')->name('tenant.maintenance.')->group(function () {
        //     Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        //     Route::get('/requests', [MaintenanceController::class, 'requests'])->name('requests');
        //     Route::post('/requests', [MaintenanceController::class, 'createRequest'])->name('create-request');
        //     Route::put('/requests/{request}', [MaintenanceController::class, 'updateRequest'])->name('update-request');
        //     Route::post('/requests/{request}/complete', [MaintenanceController::class, 'completeRequest'])->name('complete-request');
        // });
        
        // Inventory management routes (to be implemented)
        // Route::prefix('inventory')->name('tenant.inventory.')->group(function () {
        //     Route::get('/', [InventoryController::class, 'index'])->name('index');
        //     Route::get('/items', [InventoryController::class, 'items'])->name('items');
        //     Route::post('/items', [InventoryController::class, 'createItem'])->name('create-item');
        //     Route::put('/items/{item}', [InventoryController::class, 'updateItem'])->name('update-item');
        //     Route::get('/stock-movements', [InventoryController::class, 'stockMovements'])->name('stock-movements');
        //     Route::post('/stock-movements', [InventoryController::class, 'recordMovement'])->name('record-movement');
        // });
        
        // Reports routes (to be implemented)
        // Route::prefix('reports')->name('tenant.reports.')->group(function () {
        //     Route::get('/', [ReportController::class, 'index'])->name('index');
        //     Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        //     Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        //     Route::get('/guests', [ReportController::class, 'guests'])->name('guests');
        //     Route::get('/staff-performance', [ReportController::class, 'staffPerformance'])->name('staff-performance');
        //     Route::get('/maintenance', [ReportController::class, 'maintenance'])->name('maintenance');
        // });
        
        // Settings routes (to be implemented)
        // Route::prefix('settings')->name('tenant.settings.')->group(function () {
        //     Route::get('/', [SettingsController::class, 'index'])->name('index');
        //     Route::get('/general', [SettingsController::class, 'general'])->name('general');
        //     Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('update-general');
        //     Route::get('/billing', [SettingsController::class, 'billing'])->name('billing');
        //     Route::put('/billing', [SettingsController::class, 'updateBilling'])->name('update-billing');
        //     Route::get('/integrations', [SettingsController::class, 'integrations'])->name('integrations');
        //     Route::put('/integrations', [SettingsController::class, 'updateIntegrations'])->name('update-integrations');
        // });
        
        // Dashboard route (tenant-specific)
        Route::get('/dashboard', function () {
            return view('tenant.dashboard');
        })->name('tenant.dashboard');
        
        // API routes for AJAX calls
        Route::prefix('api')->name('tenant.api.')->group(function () {
            // Property API endpoints
            Route::get('/properties/{property}/statistics', [PropertyController::class, 'getStats']);
            
            // Future API endpoints for other modules
            // Route::get('/rooms/availability', [RoomController::class, 'checkAvailability']);
            // Route::get('/reservations/calendar', [ReservationController::class, 'getCalendarData']);
            // Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
        });
    });
});