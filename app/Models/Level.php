<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'position', 'name', 'description'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }
}