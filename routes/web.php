<?php

use App\Http\Controllers\SuperAdminAuthController;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
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
