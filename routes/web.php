<?php

use App\Http\Controllers\SuperAdminAuthController;
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
        Route::get('/verify-accounts', function() { return view('Superadmin.VerifyAcc'); })->name('superadmin.verify');
        Route::get('/view-accounts', function() { return view('Superadmin.ViewAcc'); })->name('superadmin.view');
    });
});
