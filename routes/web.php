<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login',[AuthController::class,'login']);
// Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

Route::prefix('blade-admin')
    ->middleware('no.back')
    ->group(function () {

        // Admin login page
        Route::get('/login', [AdminAuthController::class, 'showLogin'])
            ->name('blade.admin.login');

        // Admin login submit
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->name('blade.admin.login.submit');

        // Protected admin area
        Route::middleware('admin.redirect')->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('blade.admin.dashboard');

            Route::post('/logout', [AdminAuthController::class, 'logout'])
                ->name('blade.admin.logout');
        });
    });
