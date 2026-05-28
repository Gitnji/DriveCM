<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\InvalidatesTenantsResolverCache;
use Stancl\Tenancy\Facades\Tenancy;

class Tenant extends Model implements TenantContract
{
    use HasDomains;
    use InvalidatesTenantsResolverCache;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'subdomain', 'status', 'data',
        'contact_name', 'contact_email', 'contact_phone',
        'applicant_town', 'desired_subdomain',
        'submitted_at', 'reviewed_at', 'reviewed_by', 'rejection_reason',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if (empty($tenant->id)) {
                $tenant->id = (string) Str::uuid();
            }
        });
    }

    // --- Stancl Tenant contract ---

    public function getTenantKeyName(): string
    {
        return 'id';
    }

    public function getTenantKey()
    {
        return $this->getKey();
    }

    public function getInternal(string $key)
    {
        $data = $this->data ?? [];
        return $data['tenancy'][$key] ?? null;
    }

    public function setInternal(string $key, $value)
    {
        $data = $this->data ?? [];
        $data['tenancy'][$key] = $value;
        $this->data = $data;
        return $this;
    }

    public function run(callable $callback)
    {
        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        return $tenancy->initialized
            ? $callback($this)
            : $tenancy->runForMultiple([$this], $callback);
    }
}