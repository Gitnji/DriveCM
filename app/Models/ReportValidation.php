<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ReportValidation extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null; // append-only (D93)

    protected $fillable = [
        'tenant_id', 'student_id', 'validated_by',
        'theory_minutes', 'practical_minutes',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}