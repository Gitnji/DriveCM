<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantLoginRequest;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show()
    {
        // Dev affordance (D25): list selectable schools.
        // Only active tenants can be logged into.
        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();

        return view('auth.login', ['tenants' => $tenants]);
    }

    public function store(TenantLoginRequest $request)
    {
        $tenant = Tenant::find($request->input('tenant_id'));

        // Tenant must be active (D15 lifecycle) — clear message, not a generic error
        if (! $tenant || ! $tenant->isActive()) {
            return back()
                ->withInput($request->only('email', 'tenant_id'))
                ->withErrors(['tenant_id' => __('This school is not active.')]);
        }

        // Authenticate against users scoped to this tenant (D25)
        $user = User::where('tenant_id', $tenant->id)
            ->where('email', $request->input('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput($request->only('email', 'tenant_id'))
                ->withErrors(['email' => __('auth.failed')]);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Store tenant context in session (D25)
        $request->session()->put('tenant_id', $tenant->id);

        AuditLog::create([
            'tenant_id' => $tenant->id,
            'actor_type' => 'user',
            'actor_id' => $user->id,
            'action' => 'user.login',
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            AuditLog::create([
                'tenant_id' => $request->session()->get('tenant_id'),
                'actor_type' => 'user',
                'actor_id' => $user->id,
                'action' => 'user.logout',
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}