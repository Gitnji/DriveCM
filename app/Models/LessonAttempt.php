<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LessonAttempt extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null; // append-only — created_at only

    protected $fillable = ['tenant_id', 'lesson_id', 'user_id', 'score', 'passed', 'answers'];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array',
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