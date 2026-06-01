<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use App\Services\TenantSiteSettings;

class SiteSettingsController extends Controller
{
    public function edit(TenantSiteSettings $settings)
    {
        return view('site.settings.edit', [
            'settings' => $settings->get(),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request)
    {
        $data = $request->validated();

        $theme = [
            'primary_color' => $data['primary_color'] ?? '#0A3D62',
            'footer_show_email' => (bool) ($data['footer_show_email'] ?? false),
            'footer_show_phone' => (bool) ($data['footer_show_phone'] ?? false),
        ];

        // updateOrCreate — one row per tenant (the schema unique on tenant_id enforces this).
        // BelongsToTenant trait fills tenant_id on create; updateOrCreate's first array is the
        // 'find by' clause, second is the 'set to' clause.
        SiteSetting::updateOrCreate(
            [],  // empty find clause: with the trait's scope, find any (only one exists per tenant)
            [
                'theme' => $theme,
                'logo_upload_id' => $data['logo_upload_id'] ?? null,
            ]
        );

        return redirect()->route('site.settings.edit')
            ->with('status', __('Appearance saved.'));
    }
}