<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Page;
use App\Models\PracticalSession;
use App\Models\ReportValidation;
use App\Models\StudentApplication;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function tenant()
    {
        $user = Auth::guard('web')->user();

        $stats = null;
        $today = null;

        if ($user) {
            if ($user->role === 'owner') {
                $stats = [
                    'students_total'  => User::where('role', 'student')->count(),
                    'students_active' => User::where('role', 'student')
                        ->where('last_login_at', '>=', now()->subDays(30))
                        ->count(),
                    'lessons_published' => Lesson::where('status', 'published')->count(),
                    'pages_published'   => Page::where('status', 'published')->count(),
                    'sessions_week'     => PracticalSession::whereBetween('scheduled_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])->count(),
                ];

            } elseif ($user->role === 'secretary') {
                $stats = [
                    'sessions_today' => PracticalSession::whereDate('scheduled_at', today())->count(),
                    'sessions_unmarked' => PracticalSession::where('status', 'scheduled')
                        ->where('scheduled_at', '<', now())->count(),
                    'pending_applications' => StudentApplication::where('status', 'pending')->count(),
                    'students_this_month' => User::where('role', 'student')
                        ->where('created_at', '>=', now()->startOfMonth())->count(),
                ];

                $today = PracticalSession::whereDate('scheduled_at', today())
                    ->with(['student:id,name', 'instructor:id,name'])
                    ->orderBy('scheduled_at')
                    ->get();

            } elseif ($user->role === 'instructor') {
                $stats = [
                    'my_sessions_today' => PracticalSession::where('instructor_id', $user->id)
                        ->whereDate('scheduled_at', today())->count(),
                    'my_needs_attention' => PracticalSession::where('instructor_id', $user->id)
                        ->where('status', 'scheduled')
                        ->where('scheduled_at', '<', now())->count(),
                    'my_sessions_week' => PracticalSession::where('instructor_id', $user->id)
                        ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
                        ->count(),
                    'my_students' => PracticalSession::where('instructor_id', $user->id)
                        ->distinct('student_id')->count('student_id'),
                ];

                $today = PracticalSession::where('instructor_id', $user->id)
                    ->whereDate('scheduled_at', today())
                    ->with(['student:id,name'])
                    ->orderBy('scheduled_at')
                    ->get();

            } elseif ($user->role === 'student') {
                // DASH-5 (D167) — student progress overview.
                $latestProgress = LessonProgress::where('user_id', $user->id)
                    ->with('lesson.level')
                    ->latest('updated_at')
                    ->first();

                $currentLevel = $latestProgress?->lesson?->level;

                // Lessons completed in the current level (or 0 if no level).
                $lessonsCompleted = 0;
                $lessonsTotal = 0;
                if ($currentLevel) {
                    $lessonsTotal = Lesson::where('level_id', $currentLevel->id)
                        ->where('status', 'published')
                        ->count();
                    $lessonsCompleted = LessonProgress::where('user_id', $user->id)
                        ->where('completed', true)
                        ->whereHas('lesson', fn ($q) => $q->where('level_id', $currentLevel->id))
                        ->count();
                }

                $sessionsCompleted = PracticalSession::where('student_id', $user->id)
                    ->where('status', 'completed')
                    ->count();

                $nextSession = PracticalSession::where('student_id', $user->id)
                    ->where('scheduled_at', '>=', now())
                    ->with('instructor:id,name')
                    ->orderBy('scheduled_at')
                    ->first();

                $stats = [
                    'current_level'      => $currentLevel,
                    'lessons_completed'  => $lessonsCompleted,
                    'lessons_total'      => $lessonsTotal,
                    'sessions_completed' => $sessionsCompleted,
                    'next_session'       => $nextSession,
                ];
            }
        }

        return view('dashboard.tenant', [
            'user'  => $user,
            'stats' => $stats,
            'today' => $today,
        ]);
    }

    public function admin()
    {
        $start = now()->subDays(29)->startOfDay();
        $rawTrend = Tenant::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $trend = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trend[] = (int) ($rawTrend[$day] ?? 0);
        }

        return view('dashboard.admin', [
            'admin' => Auth::guard('admin')->user(),
            'stats' => [
                'pending'        => Tenant::where('status', 'pending')->count(),
                'active'         => Tenant::where('status', 'active')->count(),
                'total_students' => User::withoutGlobalScopes()->where('role', 'student')->count(),
                'active_week'    => Tenant::whereHas('users', fn ($q) =>
                    $q->where('last_login_at', '>=', now()->subDays(7))
                )->count(),
            ],
            'platform' => [
                'lessons'   => Lesson::withoutGlobalScopes()->count(),
                'sessions'  => PracticalSession::withoutGlobalScopes()
                    ->where('status', 'completed')->count(),
                'reports'   => ReportValidation::withoutGlobalScopes()->count(),
            ],
            'trend' => $trend,
            'recent_active' => Tenant::where('status', 'active')
                ->latest('updated_at')
                ->take(5)
                ->get(),
            'recent' => Tenant::latest('created_at')->take(5)->get(),
        ]);
    }
}