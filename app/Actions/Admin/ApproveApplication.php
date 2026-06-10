<?php

namespace App\Actions\Admin;

use App\Actions\SeedTenantLevels;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\PlatformSetting;
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
            $freeTrialDays = PlatformSetting::current()->free_trial_days;
            $now = now();

            // 1) Activate tenant + initialize billing state.
            // Stancl trait workaround: query builder bypasses Eloquent 'saved' event.
            DB::connection(config('tenancy.database.central_connection', 'pgsql'))
                ->table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'subdomain'                    => $finalSubdomain,
                    'status'                       => 'active',
                    'reviewed_at'                  => $now,
                    'reviewed_by'                  => $approvedBy->id,
                    'billing_status'               => 'active',
                    'current_billing_period_start' => $now,
                    'next_billing_due'             => $now->copy()->addDays($freeTrialDays),
                    'updated_at'                   => $now,
                ]);

            // Re-fetch the tenant fresh from DB so subsequent code sees the updated state.
            $tenant->refresh();

            // D116/D119 — register subdomain in stancl's domains table.
            Domain::create([
                'domain'    => $finalSubdomain,
                'tenant_id' => $tenant->id,
            ]);

            // 2) Create School Owner.
            $previousTenantId = session('tenant_id');
            session(['tenant_id' => $tenant->id]);

            $owner = User::create([
                'name'                 => $tenant->contact_name,
                'email'                => $tenant->contact_email,
                'password'             => $tempPassword,
                'role'                 => 'owner',
                'language'             => 'en',
                'must_change_password' => true,
            ]);

            // 3) Seed levels.
            $this->seedLevels->execute($tenant);

            session(['tenant_id' => $previousTenantId]);

            AuditLog::create([
                'tenant_id'    => $tenant->id,
                'actor_type'   => 'admin',
                'actor_id'     => $approvedBy->id,
                'action'       => 'application.approved',
                'subject_type' => 'tenant',
                'subject_id'   => $tenant->id,
                'detail'       => [
                    'owner_user_id'   => $owner->id,
                    'subdomain'       => $finalSubdomain,
                    'free_trial_days' => $freeTrialDays,
                ],
            ]);

            return [
                'tenant'        => $tenant->fresh(),
                'owner_email'   => $owner->email,
                'temp_password' => $tempPassword,
            ];
        });
    }
}