<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResumeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Group routes with auth if needed
Route::middleware('auth:sanctum')->group(function () {
    
    // Resume routes
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::get('/resumes/{resume}', [ResumeController::class, 'show']);
    Route::put('/resumes/{resume}', [ResumeController::class, 'update']);
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy']);
    Route::post('/resumes/{resume}/duplicate', [ResumeController::class, 'duplicate']);
});

//admin routes (protected/only for admin)

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', 
    function(){
      return "dashboard";
    }
    // [AdminDashboardController::class, 'index']
)->middleware(['auth:sanctum', 'admin']);

});