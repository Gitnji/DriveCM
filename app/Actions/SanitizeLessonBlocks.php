<?php

namespace App\Actions;

use Mews\Purifier\Facades\Purifier;

class SanitizeLessonBlocks
{
    /**
     * Sanitize a lesson's block array (D60/D61).
     * Each text block's `html` is run through Purifier; other block types
     * are returned unchanged. Input is the validated block array.
     */
    public function execute(array $blocks): array
    {
        return array_map(function (array $block) {
            if (($block['type'] ?? null) === 'text' && isset($block['html'])) {
                $block['html'] = Purifier::clean($block['html']);
            }
            return $block;
        }, $blocks);
    }
}