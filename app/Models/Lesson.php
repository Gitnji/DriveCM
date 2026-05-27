<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'level_id', 'title', 'content',
        'position', 'status', 'pass_threshold', 'duration_minutes',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    protected $casts = [
        'content' => 'array',
    ];
}