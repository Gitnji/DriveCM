<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scopes a model to the current tenant.
 *
 * Current tenant is read from the session key 'tenant_id' (D25 — deferred
 * subdomain resolution, D24). When the subdomain batch lands, only the
 * source of the tenant id changes; this trait's consumers do not.
 *
 * - SELECTs are auto-scoped to the session tenant via a global scope.
 * - INSERTs auto-fill tenant_id from the session if not already set.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-scope all queries to the current tenant.
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = session('tenant_id');
            if ($tenantId !== null) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
            }
        });

        // Auto-fill tenant_id on create.
        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }
}