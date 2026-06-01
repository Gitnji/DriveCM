<?php

namespace App\Support;

/**
 * Generates an initials placeholder for an instructor with no photo (D151.2).
 * Same name -> same color (deterministic via crc32). 6-color muted palette so
 * placeholders don't fight with the tenant primary or look chaotic.
 */
class InstructorAvatar
{
    private const PALETTE = [
        '#7B8FA1', // slate
        '#A89F91', // taupe
        '#6F8B7E', // sage
        '#9F8FA8', // muted plum
        '#A89F6F', // dusk gold
        '#6F8AA8', // dusty blue
    ];

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (empty($parts) || $parts[0] === '') return '?';
        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last);
    }

    public static function color(string $name): string
    {
        $hash = crc32(mb_strtolower(trim($name)));
        return self::PALETTE[$hash % count(self::PALETTE)];
    }
}