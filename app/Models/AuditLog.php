<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Append-only: created_at only, no updated_at (matches the migration)
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'detail',
    ];

    protected $casts = [
        'detail' => 'array', // JSON column <-> PHP array
    ];
}