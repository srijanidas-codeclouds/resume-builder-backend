<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminStatsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgetPassword'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/me', fn (Request $r) => $r->user());

// Group routes with auth.  if needed
Route::middleware('auth:sanctum','user.only')->prefix('users')->group(function () {
    
    // Resume routes
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::get('/resumes/{resume}', [ResumeController::class, 'show']);
    Route::match(['put', 'patch'], '/resumes/{resume}', [ResumeController::class, 'update']);
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy']);
    Route::post('/resumes/{resume}/duplicate', [ResumeController::class, 'duplicate']);
    Route::post('/resumes/{resume}/publish', [ResumeController::class, 'publish']);
});

Route::get('/resume-templates', function () {
    return response()->json([
        'success' => true,
        'data' => collect(config('resume_templates'))
            ->filter(fn ($tpl) => $tpl['status'] === 'active')
            ->map(fn ($tpl, $key) => [
                'id' => $key,
                'name' => $tpl['name'],
                'description' => $tpl['description'],
            ])
            ->values(),
    ]);
});


//admin routes (protected/only for admin)

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    Route::post('/users/bulk', [AdminUserController::class, 'bulkAction']);

    Route::get('/stats', [AdminStatsController::class, 'index']);
});