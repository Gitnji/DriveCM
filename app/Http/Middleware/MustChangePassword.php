<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user() ?? Auth::guard('admin')->user();

        if ($user && $user->must_change_password) {
            // Allow the change screen itself and logout, block everything else
            if (! $request->routeIs('password.change', 'password.update', 'login.destroy', 'admin.login.destroy')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}