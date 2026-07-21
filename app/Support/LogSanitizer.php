<?php

namespace App\Support;

final class LogSanitizer
{
    private const MAX_LENGTH = 2000;

    private const PATTERNS = [
        '/DB_PASSWORD\s*=\s*\S+/i' => 'DB_PASSWORD=[REDACTED]',
        '/APP_KEY\s*=\s*\S+/i' => 'APP_KEY=[REDACTED]',
        '/SUPER_ADMIN_PASSWORD\s*=\s*\S+/i' => 'SUPER_ADMIN_PASSWORD=[REDACTED]',
        '/\bpassword\s*=\s*\S+/i' => 'password=[REDACTED]',
        '/\bpassword\s*:\s*\S+/i' => 'password:[REDACTED]',
        '/Authorization\s*:\s*[^\r\n]+/i' => 'Authorization:[REDACTED]',
        '/Bearer\s+[A-Za-z0-9\-._~+\/=]+/i' => 'Bearer [REDACTED]',
        '/Cookie\s*:\s*[^\r\n]+/i' => 'Cookie:[REDACTED]',
        '/\b(session|token|secret)\b\s*[:=]\s*\S+/i' => '$1=[REDACTED]',
        '#/[A-Za-z]:[/\\\\][^\s\'"]+#' => '[PATH]',
        '#/(?:home|var|usr|opt)/[^\s\'"]+#' => '[PATH]',
    ];

    public static function sanitize(string $message, int $maxLength = self::MAX_LENGTH): string
    {
        $sanitized = preg_replace(array_keys(self::PATTERNS), array_values(self::PATTERNS), $message) ?? $message;

        if (strlen($sanitized) > $maxLength) {
            return substr($sanitized, 0, $maxLength).'…[truncated]';
        }

        return $sanitized;
    }

    /**
     * Limited stack frames without function arguments.
     *
     * @return list<string>
     */
    public static function sanitizeTrace(\Throwable $e, int $maxFrames = 20): array
    {
        $lines = [];

        foreach (array_slice($e->getTrace(), 0, $maxFrames) as $frame) {
            $file = isset($frame['file']) ? basename((string) $frame['file']) : 'unknown';
            $line = $frame['line'] ?? 0;
            $fn = ($frame['class'] ?? '').($frame['type'] ?? '').($frame['function'] ?? '');
            $lines[] = self::sanitize("{$file}:{$line} {$fn}", 500);
        }

        return $lines;
    }
}
