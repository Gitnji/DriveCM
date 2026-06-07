<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::guard('web')->user();
        $user->update($request->validated());

        AuditLog::create([
            'tenant_id'    => $user->tenant_id,
            'actor_type'   => 'user',
            'actor_id'     => (string) $user->id,
            'action'       => 'profile.updated',
            'subject_type' => 'user',
            'subject_id'   => (string) $user->id,
            'detail'       => ['changes' => array_keys($request->validated())],
        ]);

        return redirect()->route('profile.show')
            ->with('profile_status', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::guard('web')->user();

        if (Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password cannot be the same as your current password.',
            ], 'password');
        }

        $user->password = $request->input('password');
        $user->save();

        AuditLog::create([
            'tenant_id'    => $user->tenant_id,
            'actor_type'   => 'user',
            'actor_id'     => (string) $user->id,
            'action'       => 'password.changed',
            'subject_type' => 'user',
            'subject_id'   => (string) $user->id,
        ]);

        return redirect()->route('profile.show')
            ->with('password_status', 'Password updated.');
    }
}