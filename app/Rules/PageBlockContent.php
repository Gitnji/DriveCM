<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Closed-schema validation for page blocks (D44/D49 mirror for pages).
 * Each block must be a known type with exactly the fields its shape declares (D128).
 */
class PageBlockContent implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('Content must be an array of blocks.');
            return;
        }

        foreach ($value as $i => $block) {
            if (! is_array($block) || ! isset($block['type'])) {
                $fail("Block #{$i} is malformed.");
                return;
            }

            $type = $block['type'];
            $allowedKeys = ['type'];

            if ($type === 'hero') {
                $allowedKeys = ['type', 'heading', 'subtext', 'cta_text', 'cta_url', 'background_url'];
                foreach (['heading', 'subtext', 'cta_text', 'cta_url', 'background_url'] as $k) {
                    if (isset($block[$k]) && ! is_string($block[$k])) {
                        $fail("Block #{$i}: hero `$k` must be a string.");
                        return;
                    }
                }
            } elseif ($type === 'rich_text') {
                $allowedKeys = ['type', 'html'];
                if (isset($block['html']) && ! is_string($block['html'])) {
                    $fail("Block #{$i}: rich_text `html` must be a string.");
                    return;
                }
            } elseif ($type === 'image') {
                $allowedKeys = ['type', 'url', 'alt', 'caption'];
                foreach (['url', 'alt', 'caption'] as $k) {
                    if (isset($block[$k]) && ! is_string($block[$k])) {
                        $fail("Block #{$i}: image `$k` must be a string.");
                        return;
                    }
                }
            } else {
                $fail("Block #{$i}: unknown type `$type`.");
                return;
            }

            $extra = array_diff(array_keys($block), $allowedKeys);
            if ($extra) {
                $fail("Block #{$i}: unexpected keys (" . implode(',', $extra) . ').');
                return;
            }
        }
    }
}