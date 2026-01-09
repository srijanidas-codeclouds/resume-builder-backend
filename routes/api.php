<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//admin routes (protected/only for admin)

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', 
    function(){
      return "dashboard";
    }
    // [AdminDashboardController::class, 'index']
)->middleware(['auth:sanctum', 'admin']);

});