<?php

use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/auth/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login.attempt');

Route::get('/auth/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/auth/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register.attempt');

Route::post('/auth/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    return match (Auth::user()->role) {
        User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        User::ROLE_BUYER => redirect()->route('buyer.dashboard'),
        User::ROLE_SELLER => redirect()->route('seller.dashboard'),
        User::ROLE_LOGISTICS => redirect()->route('logistics.dashboard'),
        User::ROLE_RIDER => redirect()->route('rider.dashboard'),
        default => abort(403, 'Your account does not have a valid role.'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        abort_unless(Auth::user()->role === User::ROLE_ADMIN, 403);

        return app(DashboardController::class)->index();
    })->name('admin.dashboard');

    Route::get('/admin/registrations', [RegistrationController::class, 'index'])
        ->name('admin.registrations');

    Route::get('/admin/registrations/{registration}', [RegistrationController::class, 'show'])
        ->name('admin.registrations.show');

    Route::post('/admin/registrations/{registration}/approve', [RegistrationController::class, 'approve'])
        ->name('admin.registrations.approve');

    Route::post('/admin/registrations/{registration}/reject', [RegistrationController::class, 'reject'])
        ->name('admin.registrations.reject');
});

foreach ([
    'buyer' => User::ROLE_BUYER,
    'seller' => User::ROLE_SELLER,
    'logistics' => User::ROLE_LOGISTICS,
    'rider' => User::ROLE_RIDER,
] as $dashboard => $role) {
    Route::get("/$dashboard/dashboard", function () use ($dashboard, $role) {
        abort_unless(Auth::user()->role === $role, 403);
        return view("pages.$dashboard.dashboard");
    })->middleware('auth')->name("$dashboard.dashboard");
}

Route::middleware('auth')->group(function () {
    Route::get('/admin/user-management', [UserManagementController::class, 'index'])
        ->name('admin.user.management');
    Route::get('/admin/user-management/list', [UserManagementController::class, 'list'])
        ->name('admin.user.management.list');
    Route::get('/admin/user-management/{user}', [UserManagementController::class, 'show'])
        ->name('admin.user.management.show');
    Route::patch('/admin/user-management/{user}/status', [UserManagementController::class, 'updateStatus'])
        ->name('admin.user.management.status');
});
