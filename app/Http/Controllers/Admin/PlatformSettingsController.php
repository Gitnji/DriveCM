<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePlatformSettingsRequest;
use App\Models\PlatformSetting;

class PlatformSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.platform-settings.edit', [
            'settings' => PlatformSetting::current(),
        ]);
    }

    public function update(UpdatePlatformSettingsRequest $request)
    {
        $settings = PlatformSetting::current();
        $settings->update($request->validated());

        // TODO: platform-level audit log (deferred — audit_logs table is tenant-scoped
        // via BelongsToTenant trait, doesn't fit platform-wide actions cleanly).

        return redirect()->route('admin.platform-settings.edit')
            ->with('status', 'Platform settings saved.');
    }
}