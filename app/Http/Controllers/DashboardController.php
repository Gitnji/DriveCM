<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\PracticalSession;
use App\Models\ReportValidation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function tenant()
    {
        return view('dashboard.tenant', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function admin()
    {
        // Last 30 days of applications-per-day, oldest-first. Used for the sparkline.
        $start = now()->subDays(29)->startOfDay();
        $rawTrend = Tenant::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        // Fill missing days with zero so the sparkline has a continuous 30-point series.
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
                // DASH-1c.2 — schools with any user logged in within 7 days (D161).
                'active_week'    => Tenant::whereHas('users', fn ($q) =>
                    $q->where('last_login_at', '>=', now()->subDays(7))
                )->count(),
            ],
            // DASH-1c.3 — platform-wide activity totals (cross-tenant).
            'platform' => [
                'lessons'   => Lesson::withoutGlobalScopes()->count(),
                'sessions'  => PracticalSession::withoutGlobalScopes()
                    ->where('status', 'completed')->count(),
                'reports'   => ReportValidation::withoutGlobalScopes()->count(),
            ],
            // DASH-1c.4 — last 30 days of applications-per-day (sparkline data).
            'trend' => $trend,
            // DASH-1c.1 — recently activated schools.
            'recent_active' => Tenant::where('status', 'active')
                ->latest('updated_at')
                ->take(5)
                ->get(),
            // Recent applications (unchanged from DASH-1).
            'recent' => Tenant::latest('created_at')->take(5)->get(),
        ]);
    }
}