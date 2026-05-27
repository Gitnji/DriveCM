<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function show()
    {
        return view('auth.admin-login');
    }

    public function store(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::guard('admin')->attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('auth.failed')]);
        }

        $request->session()->regenerate();

        AuditLog::create([
            'actor_type' => 'admin',
            'actor_id' => Auth::guard('admin')->id(),
            'action' => 'admin.login',
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}