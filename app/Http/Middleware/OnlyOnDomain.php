<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 404s the request unless the host matches one of the allowed domains.
 * Used for D111 — restrict admin routes to admin.lvh.me / admin.drivecm.cm.
 *
 * Usage:  ->middleware('only.on.domain:admin.lvh.me,admin.drivecm.cm')
 */
class OnlyOnDomain
{
    public function handle(Request $request, Closure $next, ...$allowed): Response
    {
        if (! in_array($request->getHost(), $allowed, true)) {
            abort(404);
        }
        return $next($request);
    }
}