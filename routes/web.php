<?php

use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Tenant\PropertyController;
use App\Http\Controllers\Tenant\RoomsController;
use App\Http\Controllers\Tenant\RoomTypesController;
use App\Http\Controllers\Tenant\BuildingController;
use App\Http\Controllers\Tenant\UserController;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// User Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// User Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/pending', [AuthController::class, 'showPendingDashboard'])->name('dashboard.pending');
    Route::get('/dashboard', [AuthController::class, 'showDashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Property management routes (WITHOUT TENANCY FOR NOW)
    Route::prefix('properties')->name('tenant.properties.')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
        
        // AJAX routes
        Route::post('/{property}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{property}/stats', [PropertyController::class, 'getStats'])->name('stats');
        Route::post('/{property}/assign-user', [PropertyController::class, 'assignUser'])->name('assign-user');
        Route::post('/{property}/remove-user', [PropertyController::class, 'removeUser'])->name('remove-user');
    });


     Route::prefix('buildings')->name('tenant.buildings.')->group(function () {
        Route::post('/', [BuildingController::class, 'store'])->name('store');
        Route::put('/{building}', [BuildingController::class, 'update'])->name('update');
        Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('destroy');
    });

    // User management routes  
    Route::prefix('users')->name('tenant.users.')->group(function () {
        // Main CRUD routes
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // AJAX routes
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Room Types management routes
    Route::prefix('room-types')->name('tenant.room-types.')->group(function () {
        Route::get('/', [RoomTypesController::class, 'index'])->name('index');
        Route::get('/create', [RoomTypesController::class, 'create'])->name('create');
        Route::post('/', [RoomTypesController::class, 'store'])->name('store');
        Route::get('/{roomType}', [RoomTypesController::class, 'show'])->name('show');
        Route::get('/{roomType}/edit', [RoomTypesController::class, 'edit'])->name('edit');
        Route::put('/{roomType}', [RoomTypesController::class, 'update'])->name('update');
        Route::delete('/{roomType}', [RoomTypesController::class, 'destroy'])->name('destroy');
        Route::get('/property/{property}/types', [RoomTypesController::class, 'getRoomTypesByProperty'])->name('by-property');
    });
    
    // Rooms management routes
    Route::prefix('rooms')->name('tenant.rooms.')->group(function () {
        Route::get('/', [RoomsController::class, 'index'])->name('index');
        Route::get('/create', [RoomsController::class, 'create'])->name('create');
        Route::post('/', [RoomsController::class, 'store'])->name('store');
        Route::get('/{room}', [RoomsController::class, 'show'])->name('show');
        Route::get('/{room}/edit', [RoomsController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomsController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomsController::class, 'destroy'])->name('destroy');
        Route::put('/{room}/status', [RoomsController::class, 'updateStatus'])->name('update-status');
        Route::get('/property/{property}/buildings', [RoomsController::class, 'getBuildings'])->name('buildings');
        Route::get('/building/{building}/floors', [RoomsController::class, 'getFloors'])->name('floors');
        Route::get('/property/{property}/room-types', [RoomsController::class, 'getRoomTypes'])->name('room-types');
    });

    // Floor management routes
    Route::prefix('floors')->name('tenant.floors.')->group(function () {
        Route::get('/', [RoomsController::class, 'floorsIndex'])->name('index');
        Route::get('/create', [RoomsController::class, 'floorsCreate'])->name('create');
        Route::post('/', [RoomsController::class, 'floorsStore'])->name('store');
        Route::get('/{floor}', [RoomsController::class, 'floorsShow'])->name('show');
        Route::get('/{floor}/edit', [RoomsController::class, 'floorsEdit'])->name('edit');
        Route::put('/{floor}', [RoomsController::class, 'floorsUpdate'])->name('update');
        Route::delete('/{floor}', [RoomsController::class, 'floorsDestroy'])->name('destroy');
    });

    // Guest management routes
    Route::prefix('guests')->name('tenant.guests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\GuestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Tenant\GuestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Tenant\GuestController::class, 'store'])->name('store');
        Route::get('/search', [\App\Http\Controllers\Tenant\GuestController::class, 'search'])->name('search');
        Route::get('/{guest}', [\App\Http\Controllers\Tenant\GuestController::class, 'show'])->name('show');
        Route::get('/{guest}/edit', [\App\Http\Controllers\Tenant\GuestController::class, 'edit'])->name('edit');
        Route::put('/{guest}', [\App\Http\Controllers\Tenant\GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [\App\Http\Controllers\Tenant\GuestController::class, 'destroy'])->name('destroy');
    });

    // Reservation management routes
    Route::prefix('reservations')->name('tenant.reservations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\ReservationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Tenant\ReservationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Tenant\ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [\App\Http\Controllers\Tenant\ReservationController::class, 'show'])->name('show');
        Route::put('/{reservation}/status', [\App\Http\Controllers\Tenant\ReservationController::class, 'updateStatus'])->name('update-status');
        Route::get('/available-rooms/search', [\App\Http\Controllers\Tenant\ReservationController::class, 'getAvailableRooms'])->name('available-rooms');
    });

    // Folio management routes
    Route::prefix('folios')->name('tenant.folios.')->group(function () {
        Route::get('/{folio}', [\App\Http\Controllers\Tenant\FolioController::class, 'show'])->name('show');
        Route::post('/{folio}/charges', [\App\Http\Controllers\Tenant\FolioController::class, 'addCharge'])->name('add-charge');
        Route::post('/{folio}/payments', [\App\Http\Controllers\Tenant\FolioController::class, 'addPayment'])->name('add-payment');
        Route::post('/{folio}/invoice', [\App\Http\Controllers\Tenant\FolioController::class, 'generateInvoice'])->name('generate-invoice');
        Route::put('/{folio}/close', [\App\Http\Controllers\Tenant\FolioController::class, 'close'])->name('close');
    });

    // Housekeeping management routes
    Route::prefix('housekeeping')->name('tenant.housekeeping.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'store'])->name('store');
        Route::get('/{housekeeping}', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'show'])->name('show');
        Route::put('/{housekeeping}/status', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'updateStatus'])->name('update-status');
        Route::put('/{housekeeping}/assign', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'assign'])->name('assign');
        Route::post('/create-for-dirty-rooms', [\App\Http\Controllers\Tenant\HousekeepingController::class, 'createForDirtyRooms'])->name('create-for-dirty-rooms');
    });

    // User dashboard route
    Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user-dashboard/stats', [UserController::class, 'getDashboardStats'])->name('user.dashboard.stats');
});

// Add this route for the rejected dashboard
Route::get('/dashboard/rejected', [AuthController::class, 'showRejectedDashboard'])
    ->middleware('auth')
    ->name('dashboard.rejected');

// Superadmin routes
Route::group(['prefix' => 'superadmin'], function () {
    // Guest routes
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
        Route::post('/login', [SuperAdminAuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('auth:superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminAuthController::class, 'dashboard'])->name('superadmin.dashboard');
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');
        
        // Tenant management routes
        Route::get('/verify-accounts', [SuperadminController::class, 'verifyAccounts'])->name('superadmin.verify-accounts');
        Route::get('/view-accounts', [SuperadminController::class, 'viewAccounts'])->name('superadmin.view');
        
        // Tenant actions
        Route::get('/tenants/{tenant}/details', [SuperadminController::class, 'showTenantDetails']);
        Route::post('/tenants/{tenant}/approve', [SuperadminController::class, 'approveTenant']);
        Route::post('/tenants/{tenant}/reject', [SuperadminController::class, 'rejectTenant']);
        Route::get('/tenants/{tenant}/documents/{type}/download', [SuperadminController::class, 'downloadDocument']);
        Route::post('/superadmin/notifications/{notificationId}/mark-read', [SuperadminController::class, 'markNotificationAsRead'])->name('superadmin.markNotificationAsRead');
        Route::get('/tenants/{tenant}/documents/{documentType}', [SuperadminController::class, 'viewDocument'])->name('superadmin.tenant.document');
        Route::get('/tenants/{tenant}/documents/{documentType}/download', [SuperadminController::class, 'downloadDocument'])->name('superadmin.tenant.download');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Tenant routes are handled by the tenant.php file and are automatically
| loaded when a tenant domain is accessed. These routes are isolated
| per tenant using the stancl/tenancy package.
|
*/

// The tenant routes are defined in routes/tenant.php
// They are automatically loaded by the tenancy package when accessing tenant domains
