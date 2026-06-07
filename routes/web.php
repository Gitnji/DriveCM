<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// APEX — drivecm.cm / localhost / 127.0.0.1
// Apex marketing page (D112/D153) + public school application (D15, D101).
//
// IMPORTANT: The apex `/` and tenant `/` would collide on URI without ->domain(),
// because Laravel does NOT use middleware to disambiguate same-verb same-URI routes
// — the later declaration silently replaces the earlier one. ->domain() declares
// the route only matches on that host, so each host gets its own `/` route.
// =========================================================================

foreach (['localhost', '127.0.0.1', 'drivecm.cm'] as $apexHost) {
    Route::domain($apexHost)->group(function () {
        Route::get('/', [\App\Http\Controllers\ApexController::class, 'show'])->name('apex');

        Route::get('/apply', [\App\Http\Controllers\ApplicationController::class, 'create'])
            ->name('apply.create');
        Route::post('/apply', [\App\Http\Controllers\ApplicationController::class, 'store'])
            ->name('apply.store')
            ->middleware('throttle:5,1');
        Route::get('/apply/submitted', [\App\Http\Controllers\ApplicationController::class, 'submitted'])
            ->name('apply.submitted');
    });
}
// =========================================================================
// ADMIN — admin.lvh.me / admin.drivecm.cm (D111)
// =========================================================================

Route::middleware('only.on.domain:admin.lvh.me,admin.drivecm.cm')->group(function () {

    Route::get('/admin/login', [AdminLoginController::class, 'show'])
        ->name('admin.login')->middleware('guest:admin');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])
        ->middleware('guest:admin');
    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
        ->name('admin.login.destroy')->middleware('auth:admin');

    Route::middleware(['auth:admin', 'no.cache'])->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])
            ->name('admin.dashboard');

        Route::get('/admin/applications', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])
            ->name('admin.applications.index');
        // DASH-1d — MUST be before any /admin/applications/{tenant} route; otherwise the
        // wildcard captures `export.csv` as a tenant ID and the show route 404s.
        Route::get('/admin/applications/export.csv', [\App\Http\Controllers\Admin\ApplicationController::class, 'exportCsv'])
            ->name('admin.applications.export');
        Route::get('/admin/applications/{tenant}', [\App\Http\Controllers\Admin\ApplicationController::class, 'show'])
            ->name('admin.applications.show');
        Route::post('/admin/applications/{tenant}/approve', [\App\Http\Controllers\Admin\ApplicationController::class, 'approve'])
            ->name('admin.applications.approve');
        Route::get('/admin/applications/{tenant}/approved', [\App\Http\Controllers\Admin\ApplicationController::class, 'approved'])
            ->name('admin.applications.approved');
        Route::post('/admin/applications/{tenant}/reject', [\App\Http\Controllers\Admin\ApplicationController::class, 'reject'])
            ->name('admin.applications.reject');
    });
});

// =========================================================================
// TENANT — *.lvh.me / *.drivecm.cm (D109, D126)
// Routes are listed in the order they are matched:
//   1. PUBLIC home `/` (D125 — public, no auth)
//   2. Tenant auth — /login etc.
//   3. Forced password change
//   4. Authenticated app (auth:web sub-group)
//   5. PUBLIC catch-all `/{slug}` (D126 — MUST stay last)
// =========================================================================

Route::middleware(['tenant.only', 'tenant.resolve', 'tenant.session'])->group(function () {

    // 1. Public home (D125/D135 — unauthenticated)
    Route::get('/', [\App\Http\Controllers\Site\PublicPageController::class, 'home'])
        ->name('tenant.public.home');

    // Public student registration (ENROLL-2 — D164). Open form on tenant subdomain.
    Route::get('/register', [\App\Http\Controllers\StudentRegistrationController::class, 'create'])
        ->name('register.create');
    Route::post('/register', [\App\Http\Controllers\StudentRegistrationController::class, 'store'])
        ->name('register.store')
        ->middleware('throttle:5,1');
    Route::get('/register/submitted', [\App\Http\Controllers\StudentRegistrationController::class, 'submitted'])
        ->name('register.submitted');

    // 2. Tenant auth
    Route::get('/login', [LoginController::class, 'show'])
        ->name('login')->middleware('guest:web');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('guest:web');
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('login.destroy')->middleware('auth:web');

    // 3. Forced password change (tenant guard)
    Route::middleware(['must.change.password'])->group(function () {
        Route::get('/password/change', [PasswordChangeController::class, 'show'])
            ->name('password.change');
        Route::post('/password/change', [PasswordChangeController::class, 'update'])
            ->name('password.update');
    });

    // 4. Authenticated tenant app
    Route::middleware(['auth:web', 'must.change.password', 'no.cache'])->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'tenant'])
            ->name('dashboard');

        // Levels
        Route::get('/lms/levels', [\App\Http\Controllers\Lms\LevelController::class, 'index'])
            ->name('lms.levels.index')->middleware('can:manage-levels');
        Route::put('/lms/levels/{level}', [\App\Http\Controllers\Lms\LevelController::class, 'update'])
            ->name('lms.levels.update')->middleware('can:manage-levels');

        // STAFF (D168) — owner manages secretaries + instructors.
        Route::get('/lms/staff', [\App\Http\Controllers\Lms\StaffController::class, 'index'])
            ->name('lms.staff.index')->middleware('can:manage-staff');
        Route::get('/lms/staff/create', [\App\Http\Controllers\Lms\StaffController::class, 'create'])
            ->name('lms.staff.create')->middleware('can:manage-staff');
        Route::post('/lms/staff', [\App\Http\Controllers\Lms\StaffController::class, 'store'])
            ->name('lms.staff.store')->middleware('can:manage-staff');
        Route::get('/lms/staff/{staff}/created', [\App\Http\Controllers\Lms\StaffController::class, 'created'])
            ->name('lms.staff.created')->middleware('can:manage-staff');
        Route::get('/lms/staff/{staff}/edit', [\App\Http\Controllers\Lms\StaffController::class, 'edit'])
            ->name('lms.staff.edit')->middleware('can:manage-staff');
        Route::put('/lms/staff/{staff}', [\App\Http\Controllers\Lms\StaffController::class, 'update'])
            ->name('lms.staff.update')->middleware('can:manage-staff');
        Route::delete('/lms/staff/{staff}', [\App\Http\Controllers\Lms\StaffController::class, 'destroy'])
            ->name('lms.staff.destroy')->middleware('can:manage-staff');
        Route::post('/lms/staff/{id}/restore', [\App\Http\Controllers\Lms\StaffController::class, 'restore'])
            ->name('lms.staff.restore')->middleware('can:manage-staff');

        // L2 — self-service profile + voluntary password change (D170).
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])
            ->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])
            ->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        // Lessons
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

        // Uploads
        Route::get('/lms/uploads/test', fn () => view('lms.uploads.test'))
            ->name('lms.uploads.test')->middleware('can:author-lessons');
        Route::post('/lms/uploads', [\App\Http\Controllers\Lms\UploadController::class, 'store'])
            ->name('lms.uploads.store')->middleware('can:author-lessons');
        Route::get('/lms/uploads/{upload}', [\App\Http\Controllers\Lms\ServeUploadController::class, 'show'])
            ->name('lms.uploads.show');

        // Editor test harness
        Route::get('/lms/editor-test', fn () => view('lms.editor-test'))
            ->name('lms.editor.test')->middleware('can:author-lessons');

        // Questions
        Route::get('/lms/lessons/{lesson}/questions', [\App\Http\Controllers\Lms\QuestionController::class, 'index'])
            ->name('lms.questions.index')->middleware('can:author-lessons');
        Route::post('/lms/lessons/{lesson}/questions', [\App\Http\Controllers\Lms\QuestionController::class, 'store'])
            ->name('lms.questions.store')->middleware('can:author-lessons');
        Route::put('/lms/lessons/{lesson}/questions/{question}', [\App\Http\Controllers\Lms\QuestionController::class, 'update'])
            ->name('lms.questions.update')->middleware('can:author-lessons');
        Route::delete('/lms/lessons/{lesson}/questions/{question}', [\App\Http\Controllers\Lms\QuestionController::class, 'destroy'])
            ->name('lms.questions.destroy')->middleware('can:author-lessons');
        Route::post('/lms/lessons/{lesson}/questions/{question}/reorder/{direction}', [\App\Http\Controllers\Lms\QuestionController::class, 'reorder'])
            ->name('lms.questions.reorder')->middleware('can:author-lessons')
            ->where('direction', 'up|down');
        Route::post('/lms/lessons/{lesson}/reorder/{direction}', [\App\Http\Controllers\Lms\LessonController::class, 'reorder'])
            ->name('lms.lessons.reorder')->middleware('can:author-lessons')
            ->where('direction', 'up|down');

        // Practical
        Route::get('/lms/practical', [\App\Http\Controllers\Lms\PracticalSessionController::class, 'index'])
            ->name('lms.practical.index')->middleware('can:schedule-practical');
        Route::get('/lms/practical/create', [\App\Http\Controllers\Lms\PracticalSessionController::class, 'create'])
            ->name('lms.practical.create')->middleware('can:schedule-practical');
        Route::post('/lms/practical', [\App\Http\Controllers\Lms\PracticalSessionController::class, 'store'])
            ->name('lms.practical.store')->middleware('can:schedule-practical');
        Route::put('/lms/practical/{session}/mark', [\App\Http\Controllers\Lms\PracticalSessionController::class, 'mark'])
            ->name('lms.practical.mark')->middleware('can:schedule-practical');

        // Reports
        Route::get('/lms/reports', [\App\Http\Controllers\Lms\ReportController::class, 'index'])
            ->name('lms.reports.index')->middleware('can:preview-reports');
        Route::post('/lms/reports/{student}/validate', [\App\Http\Controllers\Lms\ReportController::class, 'validate'])
            ->name('lms.reports.validate')->middleware('can:validate-reports');
        Route::get('/lms/reports/{student}/export', [\App\Http\Controllers\Lms\ReportController::class, 'export'])
            ->name('lms.reports.export')->middleware('can:preview-reports');

        // Student
        Route::get('/my-lessons', [\App\Http\Controllers\StudentLessonController::class, 'index'])
            ->name('student.lessons.index')->middleware('can:access-student-lessons');
        Route::get('/my-lessons/{lesson}', [\App\Http\Controllers\StudentLessonController::class, 'show'])
            ->name('student.lessons.show')->middleware('can:access-student-lessons');
        Route::get('/my-lessons/{lesson}/test', [\App\Http\Controllers\StudentTestController::class, 'show'])
            ->name('student.test.show')->middleware('can:access-student-lessons');
        Route::post('/my-lessons/{lesson}/test', [\App\Http\Controllers\StudentTestController::class, 'submit'])
            ->name('student.test.submit')->middleware('can:access-student-lessons');
        Route::post('/my-lessons/{lesson}/finish', [\App\Http\Controllers\StudentTestController::class, 'finish'])
            ->name('student.test.finish')->middleware('can:access-student-lessons');
        Route::get('/my-lessons/{lesson}/result/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'result'])
            ->name('student.test.result')->middleware('can:access-student-lessons');
        Route::get('/my-practical', [\App\Http\Controllers\StudentLessonController::class, 'practical'])
            ->name('student.practical.index')->middleware('can:access-student-lessons');

        // Tenant public site management (CMS) — owner only
        Route::get('/site/pages', [\App\Http\Controllers\Site\PageController::class, 'index'])
            ->name('site.pages.index')->middleware('can:manage-site');
        Route::get('/site/pages/create', [\App\Http\Controllers\Site\PageController::class, 'create'])
            ->name('site.pages.create')->middleware('can:manage-site');
        Route::post('/site/pages', [\App\Http\Controllers\Site\PageController::class, 'store'])
            ->name('site.pages.store')->middleware('can:manage-site');
        Route::get('/site/pages/{page}/edit', [\App\Http\Controllers\Site\PageController::class, 'edit'])
            ->name('site.pages.edit')->middleware('can:manage-site');
        Route::put('/site/pages/{page}', [\App\Http\Controllers\Site\PageController::class, 'update'])
            ->name('site.pages.update')->middleware('can:manage-site');
        Route::delete('/site/pages/{page}', [\App\Http\Controllers\Site\PageController::class, 'destroy'])
            ->name('site.pages.destroy')->middleware('can:manage-site');
        Route::get('/site/pages/{page}/content', [\App\Http\Controllers\Site\PageController::class, 'editContent'])
            ->name('site.pages.edit-content')->middleware('can:manage-site');
        Route::put('/site/pages/{page}/content', [\App\Http\Controllers\Site\PageController::class, 'updateContent'])
            ->name('site.pages.update-content')->middleware('can:manage-site');
            // Tenant appearance (CMS-4)
        Route::get('/site/appearance', [\App\Http\Controllers\Site\SiteSettingsController::class, 'edit'])
            ->name('site.settings.edit')->middleware('can:manage-site');
        Route::put('/site/appearance', [\App\Http\Controllers\Site\SiteSettingsController::class, 'update'])
            ->name('site.settings.update')->middleware('can:manage-site');

        // ENROLL-3 — student application review queue (owner + secretary).
        Route::get('/lms/students/applications', [\App\Http\Controllers\Lms\EnrollmentController::class, 'index'])
            ->name('lms.enrollments.index')->middleware('can:review-enrollments');
        Route::get('/lms/students/applications/{application}', [\App\Http\Controllers\Lms\EnrollmentController::class, 'show'])
            ->name('lms.enrollments.show')->middleware('can:review-enrollments');
        Route::post('/lms/students/applications/{application}/approve', [\App\Http\Controllers\Lms\EnrollmentController::class, 'approve'])
            ->name('lms.enrollments.approve')->middleware('can:review-enrollments');
        Route::get('/lms/students/applications/{application}/approved', [\App\Http\Controllers\Lms\EnrollmentController::class, 'approved'])
            ->name('lms.enrollments.approved')->middleware('can:review-enrollments');
        Route::post('/lms/students/applications/{application}/reject', [\App\Http\Controllers\Lms\EnrollmentController::class, 'reject'])
            ->name('lms.enrollments.reject')->middleware('can:review-enrollments');

        // STUDENT (D169) — owner + secretary manage student roster.
        Route::get('/lms/students', [\App\Http\Controllers\Lms\StudentController::class, 'index'])
            ->name('lms.students.index')->middleware('can:manage-students');
        Route::get('/lms/students/{student}', [\App\Http\Controllers\Lms\StudentController::class, 'show'])
            ->name('lms.students.show')->middleware('can:manage-students');
        Route::get('/lms/students/{student}/edit', [\App\Http\Controllers\Lms\StudentController::class, 'edit'])
            ->name('lms.students.edit')->middleware('can:manage-students');
        Route::put('/lms/students/{student}', [\App\Http\Controllers\Lms\StudentController::class, 'update'])
            ->name('lms.students.update')->middleware('can:manage-students');
        Route::delete('/lms/students/{student}', [\App\Http\Controllers\Lms\StudentController::class, 'destroy'])
            ->name('lms.students.destroy')->middleware('can:manage-students');
        Route::post('/lms/students/{id}/restore', [\App\Http\Controllers\Lms\StudentController::class, 'restore'])
            ->name('lms.students.restore')->middleware('can:manage-students');

        // FLOW A P1 — payment types catalog (owner manages).
        Route::get('/lms/payment-types', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'index'])
            ->name('lms.payment-types.index')->middleware('can:manage-payments');
        Route::get('/lms/payment-types/create', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'create'])
            ->name('lms.payment-types.create')->middleware('can:manage-payments');
        Route::post('/lms/payment-types', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'store'])
            ->name('lms.payment-types.store')->middleware('can:manage-payments');
        Route::get('/lms/payment-types/{paymentType}/edit', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'edit'])
            ->name('lms.payment-types.edit')->middleware('can:manage-payments');
        Route::put('/lms/payment-types/{paymentType}', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'update'])
            ->name('lms.payment-types.update')->middleware('can:manage-payments');
        Route::delete('/lms/payment-types/{paymentType}', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'destroy'])
            ->name('lms.payment-types.destroy')->middleware('can:manage-payments');
        Route::post('/lms/payment-types/{id}/restore', [\App\Http\Controllers\Lms\PaymentTypeController::class, 'restore'])
            ->name('lms.payment-types.restore')->middleware('can:manage-payments');
        
        // P2 — tenant payment receiving info (owner only).
        Route::get('/lms/payment-settings', [\App\Http\Controllers\Lms\PaymentSettingsController::class, 'edit'])
            ->name('lms.payment-settings.edit')->middleware('can:manage-payments');
        Route::put('/lms/payment-settings', [\App\Http\Controllers\Lms\PaymentSettingsController::class, 'update'])
            ->name('lms.payment-settings.update')->middleware('can:manage-payments');
    });

    // 5. Public catch-all — MUST be the last route in this group.
    // The where() constraint matches the slug regex AND the routes above are checked first
    // (registration order wins), so /login, /dashboard, /lms/*, /my-lessons etc. are NOT
    // captured here. Drafts and unknown slugs 404 via the controller (D125).
    Route::get('/{slug}', [\App\Http\Controllers\Site\PublicPageController::class, 'show'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('tenant.public.show');
});