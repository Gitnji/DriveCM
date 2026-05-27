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
    }
}
