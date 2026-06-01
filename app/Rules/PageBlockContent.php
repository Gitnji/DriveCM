<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Closed-schema validation for page blocks (D44; D141 per-type map; D145 children;
 * D150 grandchildren — children may themselves have children).
 *
 * Schema entry shape:
 *   required: [string fields]
 *   optional: [string fields]
 *   children: [
 *     required: [...], optional: [...],
 *     children: [...]   // OPTIONAL grandchild spec
 *   ]
 *
 * Grandchildren are NOT permitted to have great-grandchildren — depth is bounded to 2.
 */
class PageBlockContent implements ValidationRule
{
    private array $schemas = [
        'hero' => [
            'required' => [],
            'optional' => ['heading', 'subtext', 'cta_text', 'cta_url', 'background_url'],
        ],
        'rich_text' => [
            'required' => [],
            'optional' => ['html'],
        ],
        'image' => [
            'required' => [],
            'optional' => ['url', 'alt', 'caption'],
        ],
        'cta' => [
            'required' => [],
            'optional' => ['heading', 'subtext', 'button_text', 'button_url', 'background_color'],
        ],
        'gallery' => [
            'required' => [],
            'optional' => [],
            'children' => [
                'required' => [],
                'optional' => ['url', 'alt'],
            ],
        ],
        'cards' => [
            'required' => [],
            'optional' => [],
            'children' => [
                'required' => [],
                'optional' => ['icon', 'heading', 'text'],
            ],
        ],
        // CMS-5d (D150) — pricing: tiers with feature lists.
        'pricing' => [
            'required' => [],
            'optional' => [],
            'children' => [
                'required' => [],
                'optional' => ['icon', 'title', 'price', 'period', 'cta_text', 'cta_url'],
                'children' => [
                    'required' => [],
                    'optional' => ['text'],
                ],
            ],
        ],

        // CMS-5e (D151)
        'instructors' => [
            'required' => [],
            'optional' => [],
            'children' => [
                'required' => [],
                'optional' => ['photo_url', 'name', 'role', 'bio'],
            ],
        ],

        // CMS-5f (D152) — last block in the v2 builder set.
        'contact_map' => [
            'required' => [],
            'optional' => ['heading', 'address', 'phone', 'email', 'hours'],
        ],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('Content must be an array of blocks.');
            return;
        }

        foreach ($value as $i => $block) {
            if (! $this->validateNode($block, $this->schemas[$block['type'] ?? '?'] ?? null,
                                     "Block #{$i}", $block['type'] ?? null, $fail, 0)) return;
        }
    }

    /**
     * Validate one node (block or child) against its schema. Recurses into children if present.
     * $type is the parent's type at the outer level; null when recursing into children.
     */
    private function validateNode(mixed $node, ?array $schema, string $label, ?string $type, Closure $fail, int $depth): bool
    {
        if (! is_array($node) || ($type !== null && ! isset($node['type']))) {
            $fail("{$label} is malformed.");
            return false;
        }

        // Top-level nodes need a known type; children/grandchildren have no `type` field.
        if ($type !== null) {
            if (! $schema) {
                $fail("{$label}: unknown type `{$type}`.");
                return false;
            }
        }

        if (! $schema) {
            // Shouldn't happen at depth > 0 if call sites pass the right schema, but guard anyway.
            $fail("{$label}: missing schema.");
            return false;
        }

        $hasChildren = isset($schema['children']);
        $allowed = array_merge(
            $type !== null ? ['type'] : [],
            $schema['required'],
            $schema['optional']
        );
        if ($hasChildren) {
            $allowed[] = 'children';
        }

        $extra = array_diff(array_keys($node), $allowed);
        if ($extra) {
            $fail("{$label}: unexpected keys (" . implode(',', $extra) . ').');
            return false;
        }

        if (! $this->checkStringFields($node, $schema, $label, $fail)) {
            return false;
        }

        if ($hasChildren) {
            if (! isset($node['children'])) {
                // children optional in practice — an empty array is implied
                return true;
            }
            if (! is_array($node['children'])) {
                $fail("{$label}: `children` must be an array.");
                return false;
            }
            if ($depth >= 1 && isset($schema['children']['children'])) {
                $fail("{$label}: nesting beyond grandchildren is not allowed.");
                return false;
            }
            foreach ($node['children'] as $ci => $child) {
                if (! $this->validateNode($child, $schema['children'],
                                          "{$label} child #{$ci}", null, $fail, $depth + 1)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function checkStringFields(array $data, array $schema, string $label, Closure $fail): bool
    {
        foreach ($schema['required'] as $k) {
            if (! isset($data[$k]) || ! is_string($data[$k])) {
                $fail("{$label}: `{$k}` is required and must be a string.");
                return false;
            }
        }
        foreach ($schema['optional'] as $k) {
            if (isset($data[$k]) && ! is_string($data[$k])) {
                $fail("{$label}: `{$k}` must be a string.");
                return false;
            }
        }
        return true;
    }
}