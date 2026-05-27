<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use BelongsToTenant;

    protected $table = 'lesson_progress'; // not the default 'lesson_progresses'

    protected $fillable = [
        'tenant_id', 'lesson_id', 'user_id',
        'best_score', 'completed', 'attempt_count', 'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}