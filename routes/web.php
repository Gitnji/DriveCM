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
Route::get('/admin/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])
    ->name('admin.dashboard')
    ->middleware(['auth:admin', 'must.change.password', 'no.cache']);

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'tenant'])
    ->name('dashboard')
    ->middleware(['auth:web', 'must.change.password', 'no.cache']);



    // --- Theory LMS: authoring (owner + instructor) ---
Route::middleware(['auth:web', 'must.change.password', 'no.cache'])->group(function () {
    Route::get('/lms/levels', [\App\Http\Controllers\Lms\LevelController::class, 'index'])
        ->name('lms.levels.index')
        ->middleware('can:manage-levels');
    Route::put('/lms/levels/{level}', [\App\Http\Controllers\Lms\LevelController::class, 'update'])
        ->name('lms.levels.update')
        ->middleware('can:manage-levels');
        // Theory LMS: lesson authoring (owner + instructor)
    Route::get('/lms/lessons', [\App\Http\Controllers\Lms\LessonController::class, 'index'])
        ->name('lms.lessons.index')->middleware('can:author-lessons');
    Route::get('/lms/lessons/create', [\App\Http\Controllers\Lms\LessonController::class, 'create'])
        ->name('lms.lessons.create')->middleware('can:author-lessons');
    Route::post('/lms/lessons', [\App\Http\Controllers\Lms\LessonController::class, 'store'])
        ->name('lms.lessons.store')->middleware('can:author-lessons');
    Route::get('/lms/lessons/{lesson}/edit', [\App\Http\Controllers\Lms\LessonController::class, 'edit'])
        ->name('lms.lessons.edit')->middleware('can:author-lessons');
    Route::put('/lms/lessons/{lesson}', [\App\Http\Controllers\Lms\LessonController::class, 'update'])
        ->name('lms.lessons.update')->middleware('can:author-lessons');
    Route::delete('/lms/lessons/{lesson}', [\App\Http\Controllers\Lms\LessonController::class, 'destroy'])
        ->name('lms.lessons.destroy')->middleware('can:author-lessons');
        // Theory LMS: image uploads (owner + instructor)
    Route::get('/lms/uploads/test', fn () => view('lms.uploads.test'))
        ->name('lms.uploads.test')->middleware('can:author-lessons');
    Route::post('/lms/uploads', [\App\Http\Controllers\Lms\UploadController::class, 'store'])
        ->name('lms.uploads.store')->middleware('can:author-lessons');
    Route::get('/lms/uploads/{upload}', [\App\Http\Controllers\Lms\ServeUploadController::class, 'show'])
        ->name('lms.uploads.show')->middleware('can:author-lessons');
        Route::get('/lms/editor-test', fn () => view('lms.editor-test'))
        ->name('lms.editor.test')->middleware('can:author-lessons');
});