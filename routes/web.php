<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function () {
    return view('pages.admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/registrations', function () {
    return view('pages.admin.registrations');
})->name('admin.registrations');