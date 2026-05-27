<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class Tenant extends BaseTenant
{
    // Our real columns are NOT stored in the JSON `data` blob — declare them
    // so stancl treats them as normal table columns, not virtual columns.
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'subdomain',
            'status',
            'contact_name',
            'contact_email',
            'contact_phone',
            'submitted_at',
            'reviewed_at',
            'reviewed_by',
            'rejection_reason',
        ];
    }

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // --- Lifecycle helpers (blueprint §1.1: helper methods on the model) ---

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}