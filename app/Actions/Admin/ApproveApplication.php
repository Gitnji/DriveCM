<?php

namespace App\Actions\Admin;

use App\Actions\SeedTenantLevels;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class ApproveApplication
{
    public function __construct(private SeedTenantLevels $seedLevels) {}

    public function execute(Tenant $tenant, string $finalSubdomain, Admin $approvedBy): array
    {
        if ($tenant->status !== 'pending') {
            throw new \LogicException('Only a pending application can be approved.');
        }

        $tempPassword = Str::random(14);

        return DB::transaction(function () use ($tenant, $finalSubdomain, $approvedBy, $tempPassword) {
            // 1) Activate tenant.
            $tenant->update([
                'subdomain' => $finalSubdomain,
                'status' => 'active',
                'reviewed_at' => now(),
                'reviewed_by' => $approvedBy->id,
            ]);

            // D116 — register the subdomain in stancl's domains table for both dev (.lvh.me)
            // and production (.drivecm.cm). stancl's subdomain middleware reads from here.
            // D116 — register the subdomain in stancl's domains table for both dev (.lvh.me)
            // and production (.drivecm.cm). stancl's subdomain middleware reads from here.
            // D119 — register the BARE subdomain label. The subdomain middleware strips any
            // base domain (.lvh.me / .drivecm.cm) to the first label, so one bare-label row
            // serves both dev and prod.
            Domain::create([
                'domain' => $finalSubdomain,
                'tenant_id' => $tenant->id,
            ]);

            // 2) Create School Owner.
            $previousTenantId = session('tenant_id');
            session(['tenant_id' => $tenant->id]);

            $owner = User::create([
                'name' => $tenant->contact_name,
                'email' => $tenant->contact_email,
                'password' => $tempPassword,
                'role' => 'owner',
                'language' => 'en',
                'must_change_password' => true,
            ]);

            // 3) Seed levels.
            $this->seedLevels->execute($tenant);

            session(['tenant_id' => $previousTenantId]);

            // Audit log.
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