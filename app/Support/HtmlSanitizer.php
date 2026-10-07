<?php

namespace App\Support;

/**
 * Sanitizes CMS HTML for safe public rendering (XSS defense).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'strong', 'em', 'b', 'i', 'u',
        'a', 'blockquote', 'br', 'hr', 'div', 'span', 'section',
        'details', 'summary', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|style)\b[^>]*>.*$#is', '', $html) ?? $html;

        $allowed = '<'.implode('><', self::ALLOWED_TAGS).'>';
        $clean = strip_tags($html, $allowed);

        // Remove event-handler attributes (onclick, onerror, …).
        $clean = preg_replace('/\s+on\w+\s*=\s*("|\')(.*?)\1/iu', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/iu', '', $clean) ?? $clean;

        $clean = preg_replace('/\s+(style|srcdoc|srcset|formaction)\s*=\s*("|\')(.*?)\1/iu', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+(style|srcdoc|srcset|formaction)\s*=\s*[^\s>]+/iu', '', $clean) ?? $clean;

        $clean = preg_replace_callback('/<img\b[^>]*>/iu', function (array $match): string {
            if (! preg_match('/\ssrc\s*=\s*("|\')([^"\']+)\1/iu', $match[0], $src)) {
                return '';
            }

            $url = trim($src[2]);
            if (! self::isSafeMediaUrl($url)) {
                return '';
            }

            $alt = '';
            if (preg_match('/\salt\s*=\s*("|\')([^"\']*)\1/iu', $match[0], $altMatch)) {
                $alt = htmlspecialchars($altMatch[2], ENT_QUOTES, 'UTF-8');
            }

            return '<img src="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" alt="'.$alt.'">';
        }, $clean) ?? $clean;

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

    private static function isSafeMediaUrl(string $url): bool
    {
        if (preg_match('#^\s*(javascript|data|vbscript):#i', $url) || str_starts_with($url, '//')) {
            return false;
        }

        return str_starts_with($url, '/media/') || str_starts_with($url, '/storage/ppid/');
    }
}
