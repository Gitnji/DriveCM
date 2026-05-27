<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'subdomain', 'status', 'data',
        // Application form fields (D95, D96, D102)
        'contact_name', 'contact_email', 'contact_phone',
        'applicant_town', 'desired_subdomain',
        // Review tracking (already in schema; REG-2 will write to these)
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
}