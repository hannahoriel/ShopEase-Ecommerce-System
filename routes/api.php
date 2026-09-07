<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', fn (Request $request) => response()->json($request->user()));
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', fn (Request $request) => response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role,
        ]));

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/registrations', [RegistrationController::class, 'list']);
            Route::get('/registrations/approved', [RegistrationController::class, 'approvedArchive']);
            Route::get('/registrations/rejected', [RegistrationController::class, 'rejectedArchive']);
            Route::get('/registrations/{registration}', [RegistrationController::class, 'show']);
            Route::post('/registrations/{registration}/approve', [RegistrationController::class, 'approve']);
            Route::post('/registrations/{registration}/reject', [RegistrationController::class, 'reject']);
            Route::get('/users', [UserManagementController::class, 'list']);
            Route::get('/users/{user}', [UserManagementController::class, 'show']);
            Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);
        });
    });
});
