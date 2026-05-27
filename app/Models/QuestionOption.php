<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'question_id', 'text', 'is_correct', 'position'];

    protected $casts = ['is_correct' => 'boolean'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}