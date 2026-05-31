<?php

namespace App\Actions;

use Mews\Purifier\Facades\Purifier;

/**
 * Sanitize a page's block array (D60/D133). Mirrors SanitizeLessonBlocks.
 * Only the `rich_text` block carries HTML and gets Purifier'd; hero text is plain (D133).
 */
class SanitizePageBlocks
{
    public function execute(array $blocks): array
    {
        return array_map(function (array $block) {
            if (($block['type'] ?? null) === 'rich_text' && isset($block['html'])) {
                $block['html'] = Purifier::clean($block['html']);
            }
            return $block;
        }, $blocks);
    }
}