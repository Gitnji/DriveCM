<?php

namespace App\Actions\Admin;

use App\Actions\SeedTenantLevels;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApproveApplication
{
    public function __construct(private SeedTenantLevels $seedLevels) {}

    /**
     * Approve a pending application atomically (D97, D105).
     * Returns ['tenant' => Tenant, 'owner_email' => string, 'temp_password' => string].
     * The temp_password is the plaintext shown to the admin ONCE (D98, D104).
     */
    public function execute(Tenant $tenant, string $finalSubdomain, Admin $approvedBy): array
    {
        if ($tenant->status !== 'pending') {
            throw new \LogicException('Only a pending application can be approved.');
        }

        // D104 — generate a strong temp password, shown once.
        $tempPassword = Str::random(14);

        return DB::transaction(function () use ($tenant, $finalSubdomain, $approvedBy, $tempPassword) {
            // 1) Activate the tenant (D97) — status + live subdomain.
            $tenant->update([
                'subdomain' => $finalSubdomain,
                'status' => 'active',
                'reviewed_at' => now(),
                'reviewed_by' => $approvedBy->id,
            ]);

            // 2) Create the School Owner (D23 — must change password on first login).
            //    The BelongsToTenant trait reads session('tenant_id'); set it for this
            //    transaction so the owner row, and any tenant-scoped writes (like the
            //    levels in step 3), correctly attribute to this tenant.
            $previousTenantId = session('tenant_id');
            session(['tenant_id' => $tenant->id]);

            $owner = User::create([
                'name' => $tenant->contact_name,
                'email' => $tenant->contact_email,
                'password' => $tempPassword, // model's mutator hashes it
                'role' => 'owner',
                'language' => 'en',
                'must_change_password' => true,
            ]);

            // 3) Seed the 5 theory levels for the new tenant (D45).
            $this->seedLevels->execute($tenant);

            // Restore the prior session tenant context.
            session(['tenant_id' => $previousTenantId]);

            // Audit log (D15 — moderated; the approval itself is recorded).
            AuditLog::create([
                'tenant_id' => $tenant->id,
                'actor_type' => 'admin',
                'actor_id' => $approvedBy->id,
                'action' => 'application.approved',
                'subject_type' => 'tenant',
                'subject_id' => $tenant->id,
                'detail' => ['owner_user_id' => $owner->id, 'subdomain' => $finalSubdomain],
            ]);

            return [
                'tenant' => $tenant->fresh(),
                'owner_email' => $owner->email,
                'temp_password' => $tempPassword,
            ];
        });
    }
}