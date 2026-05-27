<?php

namespace App\Actions;

use App\Models\Level;
use App\Models\Tenant;

class SeedTenantLevels
{
    /**
     * Seed the 5 default levels for a tenant (D33).
     * Idempotent: does nothing if the tenant already has levels.
     */
    public function execute(Tenant $tenant): void
    {
        // Guard: never double-seed.
        $existing = Level::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->count();

        if ($existing > 0) {
            return;
        }

        $defaults = [
            1 => 'Level 1',
            2 => 'Level 2',
            3 => 'Level 3',
            4 => 'Level 4',
            5 => 'Level 5',
        ];

        foreach ($defaults as $position => $name) {
            Level::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'position' => $position,
                'name' => $name,
            ]);
        }
    }
}