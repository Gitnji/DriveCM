<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantLoginRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Tenancy;

class LoginController extends Controller
{
    public function show()
    {
        // P2 — school is implicit from the subdomain. No selection needed.
        return view('auth.login', [
            'tenant' => app(Tenancy::class)->tenant,
        ]);
    }

    public function store(TenantLoginRequest $request)
    {
        // P2 — resolve tenant from the subdomain (stancl), not a user-submitted field.
        // This closes the security hole where a user on schoolA could authenticate against
        // schoolB by submitting that tenant_id.
        $tenant = app(Tenancy::class)->tenant;

        if (! $tenant || ! $tenant->isActive()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('This school is not active.')]);
        }

        $user = User::where('tenant_id', $tenant->id)
            ->where('email', $request->input('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('auth.failed')]);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $request->session()->put('tenant_id', $tenant->id);

        AuditLog::create([
            'tenant_id'  => $tenant->id,
            'actor_type' => 'user',
            'actor_id'   => $user->id,
            'action'     => 'user.login',
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            AuditLog::create([
                'tenant_id'  => $request->session()->get('tenant_id'),
                'actor_type' => 'user',
                'actor_id'   => $user->id,
                'action'     => 'user.logout',
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}