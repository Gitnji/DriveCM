<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name', 'email', 'phone', 'town',
        'desired_level_id', 'notes',
        'source', 'status',
        'submitted_at', 'reviewed_at', 'reviewed_by', 'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    // ---- Status helpers ----

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    // ---- Relationships ----

    public function desiredLevel()
    {
        return $this->belongsTo(Level::class, 'desired_level_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}