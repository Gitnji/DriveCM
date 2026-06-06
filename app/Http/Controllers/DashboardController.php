<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Page;
use App\Models\PracticalSession;
use App\Models\ReportValidation;
use App\Models\StudentApplication;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                // DASH-3 (D166) — secretary operational worklist.
                $stats = [
                    'sessions_today' => PracticalSession::whereDate('scheduled_at', today())->count(),
                    // Past sessions still 'scheduled' = forgot to mark attendance.
                    'sessions_unmarked' => PracticalSession::where('status', 'scheduled')
                        ->where('scheduled_at', '<', now())->count(),
                    'pending_applications' => StudentApplication::where('status', 'pending')->count(),
                    'students_this_month' => User::where('role', 'student')
                        ->where('created_at', '>=', now()->startOfMonth())->count(),
                ];

                // Today's schedule for the worklist.
                $today = PracticalSession::whereDate('scheduled_at', today())
                    ->with(['student:id,name', 'instructor:id,name'])
                    ->orderBy('scheduled_at')
                    ->get();
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