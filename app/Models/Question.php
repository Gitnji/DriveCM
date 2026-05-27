<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'lesson_id', 'prompt', 'type', 'position'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function isMcq(): bool
    {
        return $this->type === 'mcq';
    }

    public function isTrueFalse(): bool
    {
        return $this->type === 'true_false';
    }
}