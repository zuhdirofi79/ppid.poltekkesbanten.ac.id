<?php

namespace App\Support;

/**
 * Sanitizes CMS HTML for safe public rendering (XSS defense).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'strong', 'em', 'b', 'i',
        'a', 'blockquote', 'br', 'hr', 'div', 'span', 'section',
        'details', 'summary',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $allowed = '<'.implode('><', self::ALLOWED_TAGS).'>';
        $clean = strip_tags($html, $allowed);

        // Remove event-handler attributes (onclick, onerror, …).
        $clean = preg_replace('/\s+on\w+\s*=\s*("|\')(.*?)\1/iu', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/iu', '', $clean) ?? $clean;

        // Remove dangerous presentation / embedding attributes.
        $clean = preg_replace('/\s+(style|srcdoc|src|formaction)\s*=\s*("|\')(.*?)\1/iu', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+(style|srcdoc|src|formaction)\s*=\s*[^\s>]+/iu', '', $clean) ?? $clean;

        // Neutralize javascript: / data: URLs in href.
        $clean = preg_replace(
            '/\shref\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*\1/iu',
            ' href="#"',
            $clean,
        ) ?? $clean;

        // Drop script/iframe remnants that may survive malformed markup.
        $clean = preg_replace('/<\/?(script|iframe|object|embed|link|meta|form)\b[^>]*>/iu', '', $clean) ?? $clean;

        return $clean;
    }
}
