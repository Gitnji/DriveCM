<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'theme', 'logo_upload_id', 'nav_config'];

    protected $casts = [
        'theme' => 'array',
        'nav_config' => 'array',
    ];

    public function logo()
    {
        return $this->belongsTo(Upload::class, 'logo_upload_id');
    }
}