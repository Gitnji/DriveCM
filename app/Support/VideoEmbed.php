<?php

namespace App\Support;

class VideoEmbed
{
    /**
     * Convert a video URL to an embeddable URL.
     * Returns null if the URL shape isn't recognized — caller falls back to a link.
     */
    public static function toEmbedUrl(string $url): ?string
    {
        // YouTube: youtu.be/ID , youtube.com/watch?v=ID , youtube.com/shorts/ID
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo: vimeo.com/ID
        if (preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }
}