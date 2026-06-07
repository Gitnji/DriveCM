<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'amount_xaf',
        'is_required',
        'levels_required_before_prompt',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amount_xaf'                     => 'integer',
        'is_required'                    => 'boolean',
        'levels_required_before_prompt'  => 'integer',
        'is_active'                      => 'boolean',
        'sort_order'                     => 'integer',
    ];

    /**
     * P1 — required types auto-prompt students at the configured threshold.
     * Optional types never block; only appear on the Payments page for voluntary opt-in.
     */
    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }
}