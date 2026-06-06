<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
        abort_if($user === null, 403);

        // P1 — reject if the new password matches the current one.
        // Hash::check compares the plaintext new password against the stored hash.
        if (Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['password' => __('Your new password cannot be the same as your current password.')])
                ->withInput($request->only('password_confirmation')); // never re-fill password fields
        }

        $user->password = $request->input('password'); // 'hashed' cast hashes it
        $user->must_change_password = false;
        $user->save();

        AuditLog::create([
            'tenant_id' => $request->session()->get('tenant_id'),
            'actor_type' => $user instanceof \App\Models\Admin ? 'admin' : 'user',
            'actor_id' => $user->id,
            'action' => 'password.changed',
        ]);

        $redirect = $user instanceof \App\Models\Admin
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect($redirect)->with('status', __('Password updated.'));
    }
}