<?php

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

Route::get('/admin/dashboard', function () {
    abort_unless(Auth::user()->role === User::ROLE_ADMIN, 403);

    return view('pages.admin.dashboard');
})->middleware('auth')->name('admin.dashboard');

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

Route::get('/admin/registrations', function () {
    return view('pages.admin.registrations');
})->name('admin.registrations');


Route::get('/admin/user-management', function () {
    return view('pages.admin.user-management');
})->name('admin.user.management');
