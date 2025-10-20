<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\Tenant\BuildingController;
use App\Http\Controllers\Tenant\DirectorController;
use App\Http\Controllers\Tenant\FolioController;
use App\Http\Controllers\Tenant\GuestController;
use App\Http\Controllers\Tenant\GroupBookingController;
use App\Http\Controllers\Tenant\HousekeeperController;
use App\Http\Controllers\Tenant\HousekeepingController;
use App\Http\Controllers\Tenant\InventoryController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\MaintenanceController;
use App\Http\Controllers\Tenant\PosController;
use App\Http\Controllers\Tenant\PropertyController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\ReservationController;
use App\Http\Controllers\Tenant\RoomsController;
use App\Http\Controllers\Tenant\RoomTypesController;
use App\Http\Controllers\Tenant\SupervisorController;
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
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard/pending', [AuthController::class, 'showPendingDashboard'])->name('dashboard.pending');
    Route::get('/dashboard', [AuthController::class, 'showDashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
        Route::get('/', [GuestController::class, 'index'])->name('index');
        Route::get('/create', [GuestController::class, 'create'])->name('create');
        Route::post('/', [GuestController::class, 'store'])->name('store');
        Route::get('/search', [GuestController::class, 'search'])->name('search');
        Route::get('/{guest}', [GuestController::class, 'show'])->name('show');
        Route::get('/{guest}/edit', [GuestController::class, 'edit'])->name('edit');
        Route::put('/{guest}', [GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('destroy');
    });

    // Reservation management routes
    Route::prefix('reservations')->name('tenant.reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::put('/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('update-status');
        Route::get('/available-rooms/search', [ReservationController::class, 'getAvailableRooms'])->name('available-rooms');
    });

    // Folio management routes
    Route::prefix('folios')->name('tenant.folios.')->group(function () {
        Route::get('/{folio}', [FolioController::class, 'show'])->name('show');
        Route::post('/{folio}/charges', [FolioController::class, 'addCharge'])->name('add-charge');
        Route::post('/{folio}/payments', [FolioController::class, 'addPayment'])->name('add-payment');
        Route::post('/{folio}/invoice', [FolioController::class, 'generateInvoice'])->name('generate-invoice');
        Route::put('/{folio}/close', [FolioController::class, 'close'])->name('close');
    });

    // Housekeeping management routes
    Route::prefix('housekeeping')->name('tenant.housekeeping.')->group(function () {
        Route::get('/', [HousekeepingController::class, 'index'])->name('index');
        Route::get('/create', [HousekeepingController::class, 'create'])->name('create');
        Route::post('/', [HousekeepingController::class, 'store'])->name('store');
        Route::get('/{housekeeping}', [HousekeepingController::class, 'show'])->name('show');
        Route::put('/{housekeeping}/status', [HousekeepingController::class, 'updateStatus'])->name('update-status');
        Route::put('/{housekeeping}/assign', [HousekeepingController::class, 'assign'])->name('assign');
        Route::post('/create-for-dirty-rooms', [HousekeepingController::class, 'createForDirtyRooms'])->name('create-for-dirty-rooms');
    });

    // Maintenance management routes
    Route::prefix('maintenance')->name('tenant.maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::put('/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('update-status');
        Route::put('/{maintenance}/assign', [MaintenanceController::class, 'assign'])->name('assign');
    });

    // Invoice routes
    Route::prefix('invoices')->name('tenant.invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
        Route::post('/{invoice}/email', [InvoiceController::class, 'email'])->name('email');
    });

    // POS routes
    Route::prefix('pos')->name('tenant.pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/create', [PosController::class, 'create'])->name('create');
        Route::post('/', [PosController::class, 'store'])->name('store');
        Route::get('/{pos}', [PosController::class, 'show'])->name('show');
        Route::post('/{pos}/payment', [PosController::class, 'processPayment'])->name('process-payment');
    });

    // Reports routes
    Route::prefix('reports')->name('tenant.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/guests', [ReportController::class, 'guests'])->name('guests');
        Route::get('/reservations', [ReportController::class, 'reservations'])->name('reservations');
        Route::get('/housekeeping', [ReportController::class, 'housekeeping'])->name('housekeeping');
    });

    Route::prefix('group-bookings')->name('tenant.group-bookings.')->group(function () {
        Route::get('/', [GroupBookingController::class, 'index'])->name('index');
        Route::post('/', [GroupBookingController::class, 'store'])->name('store');
        Route::get('/{groupBooking}', [GroupBookingController::class, 'show'])->name('show');
        Route::put('/{groupBooking}', [GroupBookingController::class, 'update'])->name('update');
        Route::post('/{groupBooking}/confirm', [GroupBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{groupBooking}/cancel', [GroupBookingController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('inventory')->name('tenant.inventory.')->group(function () {
        Route::get('/stock-levels', [InventoryController::class, 'stockLevels'])->name('stock-levels');
        Route::get('/low-stock', [InventoryController::class, 'lowStockAlert'])->name('low-stock');
        Route::get('/stock-movements', [InventoryController::class, 'stockMovements'])->name('stock-movements');
        Route::post('/stock-adjustments', [InventoryController::class, 'adjustStock'])->name('stock-adjustments');
        Route::post('/purchase-orders', [InventoryController::class, 'createPurchaseOrder'])->name('purchase-orders.store');
        Route::get('/purchase-orders', [InventoryController::class, 'getPurchaseOrders'])->name('purchase-orders.index');
        Route::get('/purchase-orders/{purchaseOrder}', [InventoryController::class, 'showPurchaseOrder'])->name('purchase-orders.show');
        Route::put('/purchase-orders/{purchaseOrder}', [InventoryController::class, 'updatePurchaseOrder'])->name('purchase-orders.update');
        Route::post('/goods-receipts', [InventoryController::class, 'receiveGoods'])->name('goods-receipts.store');
        Route::get('/vendors', [InventoryController::class, 'getVendors'])->name('vendors.index');
        Route::post('/vendors', [InventoryController::class, 'createVendor'])->name('vendors.store');
        Route::get('/warehouses', [InventoryController::class, 'getWarehouses'])->name('warehouses.index');
        Route::get('/report', [InventoryController::class, 'inventoryReport'])->name('report');
    });

    Route::prefix('director')->name('tenant.director.')->group(function () {
        Route::get('/dashboard', [DirectorController::class, 'dashboard'])->name('dashboard');
        Route::get('/purchase-orders/pending', [DirectorController::class, 'pendingApprovals'])->name('purchase-orders.pending');
        Route::post('/purchase-orders/{purchaseOrder}/approve', [DirectorController::class, 'approvePurchaseOrder'])->name('purchase-orders.approve');
        Route::post('/purchase-orders/{purchaseOrder}/reject', [DirectorController::class, 'rejectPurchaseOrder'])->name('purchase-orders.reject');
        Route::post('/stocktakes', [DirectorController::class, 'createStocktake'])->name('stocktakes.store');
        Route::get('/stocktakes', [DirectorController::class, 'getStocktakes'])->name('stocktakes.index');
        Route::get('/stocktakes/{stocktake}', [DirectorController::class, 'showStocktake'])->name('stocktakes.show');
        Route::put('/stocktakes/{stocktake}/start', [DirectorController::class, 'startStocktake'])->name('stocktakes.start');
        Route::put('/stocktakes/{stocktake}/complete', [DirectorController::class, 'completeStocktake'])->name('stocktakes.complete');
        Route::get('/financial-summary', [DirectorController::class, 'financialSummary'])->name('financial-summary');
        Route::get('/vendor-performance', [DirectorController::class, 'vendorPerformance'])->name('vendor-performance');
        Route::get('/inventory-valuation', [DirectorController::class, 'inventoryValuation'])->name('inventory-valuation');
    });

    Route::prefix('housekeeper')->name('tenant.housekeeper.')->group(function () {
        Route::get('/tasks', [HousekeeperController::class, 'myTasks'])->name('tasks.index');
        Route::get('/tasks/today', [HousekeeperController::class, 'todayTasks'])->name('tasks.today');
        Route::get('/tasks/{task}', [HousekeeperController::class, 'showTask'])->name('tasks.show');
        Route::put('/tasks/{task}/start', [HousekeeperController::class, 'startTask'])->name('tasks.start');
        Route::put('/tasks/{task}/complete', [HousekeeperController::class, 'completeTask'])->name('tasks.complete');
        Route::put('/tasks/{task}/progress', [HousekeeperController::class, 'updateTaskProgress'])->name('tasks.progress');
        Route::put('/tasks/{task}/room-status', [HousekeeperController::class, 'updateRoomStatus'])->name('tasks.room-status');
        Route::get('/statistics', [HousekeeperController::class, 'myStatistics'])->name('statistics');
        Route::post('/tasks/{task}/issues', [HousekeeperController::class, 'reportIssue'])->name('tasks.report-issue');
    });

    Route::prefix('supervisor')->name('tenant.supervisor.')->group(function () {
        Route::get('/tasks', [SupervisorController::class, 'index'])->name('tasks.index');
        Route::post('/tasks', [SupervisorController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [SupervisorController::class, 'show'])->name('tasks.show');
        Route::put('/tasks/{task}', [SupervisorController::class, 'update'])->name('tasks.update');
        Route::put('/tasks/{task}/verify', [SupervisorController::class, 'verifyTask'])->name('tasks.verify');
        Route::put('/tasks/{task}/cancel', [SupervisorController::class, 'cancelTask'])->name('tasks.cancel');
        Route::get('/housekeepers', [SupervisorController::class, 'getHousekeepers'])->name('housekeepers.index');
        Route::post('/inspections', [SupervisorController::class, 'createInspection'])->name('inspections.store');
        Route::post('/tasks/bulk-assign', [SupervisorController::class, 'bulkAssignTasks'])->name('tasks.bulk-assign');
    });

    // User dashboard route
    Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user-dashboard/stats', [UserController::class, 'getDashboardStats'])->name('user.dashboard.stats');
});

Route::get('/dashboard/rejected', [AuthController::class, 'showRejectedDashboard'])
    ->middleware(['auth'])
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
