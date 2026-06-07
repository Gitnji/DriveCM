<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdatePaymentSettingsRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;

class PaymentSettingsController extends Controller
{
    public function edit()
    {
        $tenant = app(Tenancy::class)->tenant;

        return view('lms.payments.settings.edit', ['tenant' => $tenant]);
    }

    public function update(UpdatePaymentSettingsRequest $request)
    {
        $tenant = app(Tenancy::class)->tenant;
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $tenant) {
            // Bypass model save events — stancl's InvalidatesTenantsResolverCache trait
            // chokes during a tenant-context request. Query builder writes the columns
            // directly without triggering model observers. The central DB is the default
            // connection for the tenants table.
            DB::connection(config('tenancy.database.central_connection', 'pgsql'))
                ->table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'momo_number'          => $validated['momo_number'] ?? null,
                    'orange_number'        => $validated['orange_number'] ?? null,
                    'payment_instructions' => $validated['payment_instructions'] ?? null,
                    'updated_at'           => now(),
                ]);

            AuditLog::create([
                'tenant_id'    => $tenant->id,
                'actor_type'   => 'user',
                'actor_id'     => (string) Auth::guard('web')->id(),
                'action'       => 'payment_settings.updated',
                'subject_type' => 'tenant',
                'subject_id'   => (string) $tenant->id,
                'detail'       => ['changes' => array_keys($validated)],
            ]);
        });

        return redirect()->route('lms.payment-settings.edit')
            ->with('status', 'Payment settings saved.');
    }
}