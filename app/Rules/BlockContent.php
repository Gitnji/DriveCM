<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BlockContent implements ValidationRule
{
    /**
     * Allowed block types and their EXACT required field sets (D49 — closed schema).
     * 'type' is implicit; listed fields are the only other keys permitted.
     */
    private const SCHEMA = [
        'text'  => ['html'],
        'image' => ['url', 'alt'],
        'video' => ['embed_url'],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('Lesson content must be a list of blocks.');
            return;
        }

        foreach ($value as $i => $block) {
            $n = $i + 1;

            if (! is_array($block)) {
                $fail("Block {$n} is malformed.");
                return;
            }

            $type = $block['type'] ?? null;

            if (! is_string($type) || ! array_key_exists($type, self::SCHEMA)) {
                $fail("Block {$n} has an invalid type.");
                return;
            }

            $allowed = self::SCHEMA[$type];
            $expectedKeys = array_merge(['type'], $allowed);
            $actualKeys = array_keys($block);

            // Closed schema: no missing fields, no unknown fields (D49).
            sort($expectedKeys);
            sort($actualKeys);
            if ($expectedKeys !== $actualKeys) {
                $fail("Block {$n} ({$type}) has missing or unexpected fields.");
                return;
            }

            // Every value must be a non-empty string.
            foreach ($allowed as $field) {
                if (! is_string($block[$field]) || trim($block[$field]) === '') {
                    $fail("Block {$n} ({$type}): '{$field}' must be a non-empty string.");
                    return;
                }
            }
        }
    }
}