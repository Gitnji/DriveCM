<?php

namespace App\Actions\Admin;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class RejectApplication
{
    public function execute(Tenant $tenant, ?string $reason, Admin $rejectedBy): void
    {
        if ($tenant->status !== 'pending') {
            throw new \LogicException('Only a pending application can be rejected.');
        }

        DB::transaction(function () use ($tenant, $reason, $rejectedBy) {
            $tenant->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_at' => now(),
                'reviewed_by' => $rejectedBy->id,
            ]);

            AuditLog::create([
                'tenant_id' => $tenant->id,
                'actor_type' => 'admin',
                'actor_id' => $rejectedBy->id,
                'action' => 'application.rejected',
                'subject_type' => 'tenant',
                'subject_id' => $tenant->id,
                'detail' => ['reason' => $reason],
            ]);
        });
    }
}