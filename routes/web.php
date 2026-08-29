<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function () {
    return view('pages.admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/registrations', function () {
    return view('pages.admin.registrations');
})->name('admin.registrations');

Route::get('/admin/user-management', function () {
    return view('pages.admin.user-management');
})->name('admin.user.management');