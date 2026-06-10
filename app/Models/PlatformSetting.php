<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'monthly_fee_xaf',
        'free_trial_days',
        'momo_number',
        'orange_number',
        'payment_instructions',
    ];

    protected $casts = [
        'monthly_fee_xaf' => 'integer',
        'free_trial_days' => 'integer',
    ];

    /**
     * FB1 — the platform_settings table is single-row. Always returns row id=1
     * (seeded by the migration). This is the canonical accessor used everywhere.
     */
    public static function current(): self
    {
        return self::firstOrFail();
    }
}