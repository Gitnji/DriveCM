<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Theory LMS authoring gates (D5, D46).
        // Gates run against the 'web' guard user (tenant users).
        Gate::define('author-lessons', function (User $user) {
            return $user->canAuthorLessons(); // owner + instructor
        });

        Gate::define('manage-levels', function (User $user) {
            return $user->canAuthorLessons(); // owner + instructor (D46)
        });

        Gate::define('access-student-lessons', function (User $user) {
            return $user->isStudent();
        });

        // Practical lessons (D82) — instructor + secretary + owner can schedule & manage.
        Gate::define('schedule-practical', function (User $user) {
            return $user->isOwner() || $user->isInstructor() || $user->isSecretary();
        });

        // Ministry report (D92)
        Gate::define('preview-reports', function (User $user) {
            return $user->isOwner() || $user->isSecretary();
        });
        Gate::define('validate-reports', function (User $user) {
            return $user->isOwner(); // D12/D92 — owner only
        });

        // Tenant public site (D130) — owner only.
        Gate::define('manage-site', function (User $user) {
            return $user->isOwner();
        });
        // ENROLL-3 (D164) — student application review queue. Owner + secretary.
        Gate::define('review-enrollments', function (User $user) {
            return $user->isOwner() || $user->isSecretary();
        });
        // STAFF (D168) — owner manages instructors and secretaries.
        Gate::define('manage-staff', function (User $user) {
            return $user->isOwner();
        });
        // STUDENT (D169) — owner + secretary manage student roster.
        Gate::define('manage-students', function (User $user) {
            return $user->isOwner() || $user->isSecretary();
        });
        // FLOW A (P1/P5) — payment management. Owner configures the catalog; both
        // owner and secretary review payment submissions.
        Gate::define('manage-payments', function (User $user) {
            return $user->isOwner();
        });
        Gate::define('review-payments', function (User $user) {
            return $user->isOwner() || $user->isSecretary();
        });
    }
}
