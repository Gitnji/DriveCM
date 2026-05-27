<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;

// --- Central: Super Admin auth ---
Route::get('/admin/login', [AdminLoginController::class, 'show'])
    ->name('admin.login')->middleware('guest:admin');
Route::post('/admin/login', [AdminLoginController::class, 'store'])
    ->middleware('guest:admin');
Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
    ->name('admin.login.destroy')->middleware('auth:admin');

// --- Tenant: user auth ---
Route::get('/login', [LoginController::class, 'show'])
    ->name('login')->middleware('guest:web');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest:web');
Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('login.destroy')->middleware('auth:web');

// --- Forced password change (either guard) ---
Route::middleware(['must.change.password'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])
        ->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])
        ->name('password.update');
});

// --- Dashboard placeholders (real dashboards come later; named routes needed now) ---
Route::get('/admin/dashboard', fn () => 'Admin dashboard placeholder')
    ->name('admin.dashboard')
    ->middleware(['auth:admin', 'must.change.password']);

Route::get('/dashboard', fn () => 'Tenant dashboard placeholder')
    ->name('dashboard')
    ->middleware(['auth:web', 'must.change.password']);