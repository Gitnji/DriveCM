<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PracticalSession extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'student_id', 'instructor_id',
        'scheduled_at', 'duration_minutes', 'status', 'notes',
        'theory_gate_overridden', 'completed_at', 'marked_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'theory_gate_overridden' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function isScheduled(): bool { return $this->status === 'scheduled'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
}