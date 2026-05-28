<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bridges stancl's resolved tenant into the session, so the BelongsToTenant trait
 * (D37, which reads session('tenant_id')) keeps working with no change. D113/D117.
 * Must run AFTER InitializeTenancyBySubdomain.
 */
class WriteTenantSessionFromTenancy
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenancy = app(Tenancy::class);

        if ($tenancy->initialized && $tenancy->tenant) {
            $tenantId = $tenancy->tenant->getTenantKey();
            if (session('tenant_id') !== $tenantId) {
                session(['tenant_id' => $tenantId]);
            }
        }

        return $next($request);
    }
}