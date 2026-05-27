<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'path', 'original_name', 'mime', 'size', 'uploaded_by', 'lesson_id',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}