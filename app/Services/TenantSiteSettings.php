<?php

namespace App\Services;

use App\Models\SiteSetting;

class TenantSiteSettings
{
    /**
     * Returns a flat array of effective settings for the current tenant, with defaults applied.
     * Keys: primary_color, logo_url, footer_show_email, footer_show_phone, record.
     * 'record' is the underlying SiteSetting model (or null) — useful for the editor's prefill.
     */
    public function get(): array
    {
        $record = SiteSetting::with('logo')->first(); // BelongsToTenant scopes to current tenant

        $theme = $record?->theme ?? [];

        return [
            'primary_color' => $theme['primary_color'] ?? '#0A3D62',  // DriveCM default (D138.3)
            'footer_show_email' => $theme['footer_show_email'] ?? true,
            'footer_show_phone' => $theme['footer_show_phone'] ?? true,
            'logo_url' => $record?->logo?->url,
            'record' => $record,
        ];
    }
}