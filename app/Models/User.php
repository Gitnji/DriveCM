<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\BelongsToTenant;

class User extends Authenticatable
{
    use Notifiable, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',                  // owner | secretary | instructor | student
        'language',              // en | fr
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'must_change_password' => 'boolean',
    ];

    // --- Role helpers (blueprint §1.1) ---

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Convenience: can this user author theory lessons? (D5 — owner + instructor)
    public function canAuthorLessons(): bool
    {
        return $this->isOwner() || $this->isInstructor();
    }
}