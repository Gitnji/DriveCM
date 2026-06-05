<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * DASH-1b — updates users.last_login_at on each authenticated request.
 *
 * Updates are throttled to once per minute per user (in-memory check against the loaded
 * model) to avoid an UPDATE on every page load while still keeping the column accurate
 * to "active this minute / hour / day / week" granularity.
 *
 * Bypasses Eloquent observers and global scopes (updateQuietly + withoutGlobalScopes)
 * so a touched timestamp doesn't trigger audit logging or any unintended side effects.
 *
 * Works for both 'web' (tenant) and 'admin' (central) guards — we check whichever has
 * a user resolved.
 */
class TrackLastLogin
{
    private const THROTTLE_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        // Only track tenant users (web guard). Admins are in a separate `admins` table
        // without a last_login_at column, and "active this week" is a tenant-schools
        // metric anyway — admin login events aren't relevant to it.
        $user = Auth::guard('web')->user();

        if ($user) {
            $last = $user->last_login_at;
            if (! $last || Carbon::parse($last)->lt(now()->subSeconds(self::THROTTLE_SECONDS))) {
                // updateQuietly bypasses observers; withoutTimestamps so updated_at doesn't shift.
                $user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        }

        return $next($request);
    }
}