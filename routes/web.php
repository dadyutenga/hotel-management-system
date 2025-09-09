<?php

use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\AuthController;
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
