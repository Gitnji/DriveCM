<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'slug', 'is_home', 'status', 'position',
        'content', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_home' => 'boolean',
        'content' => 'array',   // jsonb <-> PHP array
    ];

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}