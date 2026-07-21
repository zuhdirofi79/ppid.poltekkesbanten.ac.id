<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Hostinger split deployment front controller
|--------------------------------------------------------------------------
|
| Upload this file to ~/public_html/index.php
|
|   /home/USER/
|   ├── ppid-poltekkesbanten/     ← Laravel root
|   └── public_html/              ← this file (document root)
|
| Do NOT replace public/index.php (local/dev) with this file.
|
*/

$laravelRoot = __DIR__.'/../ppid-poltekkesbanten';
$autoloadPath = $laravelRoot.'/vendor/autoload.php';
$bootstrapPath = $laravelRoot.'/bootstrap/app.php';
$logDirectory = $laravelRoot.'/storage/logs';
$logPath = $logDirectory.'/laravel.log';

$requestId = bin2hex(random_bytes(8));
$_SERVER['PPID_REQUEST_ID'] = $requestId;

$bootstrapLogWritten = false;

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (! is_dir($logDirectory)) {
    @mkdir($logDirectory, 0775, true);
}

if (! is_file($logPath)) {
    @touch($logPath);
}

if (is_file($logPath) && is_writable($logPath)) {
    ini_set('error_log', $logPath);
} elseif (is_dir($logDirectory) && is_writable($logDirectory)) {
    ini_set('error_log', $logPath);
}

/**
 * Sanitize bootstrap log messages — never leak credentials or absolute paths to logs/browsers.
 */
function sanitizeBootstrapMessage(string $message, int $maxLength = 2000): string
{
    $patterns = [
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

    $sanitized = preg_replace(array_keys($patterns), array_values($patterns), $message) ?? $message;

    if (strlen($sanitized) > $maxLength) {
        $sanitized = substr($sanitized, 0, $maxLength).'…[truncated]';
    }

    return $sanitized;
}

/**
 * Relative path for logs (basename fallback) — never expose full server paths in responses.
 */
function relativeBootstrapPath(?string $file, string $laravelRoot): string
{
    if ($file === null || $file === '') {
        return 'unknown';
    }

    $normalizedFile = str_replace('\\', '/', $file);
    $normalizedRoot = rtrim(str_replace('\\', '/', $laravelRoot), '/').'/';

    if (str_starts_with($normalizedFile, $normalizedRoot)) {
        return substr($normalizedFile, strlen($normalizedRoot));
    }

    return basename($normalizedFile);
}

/**
 * Safe request URI without query string (query may contain tokens).
 */
function bootstrapRequestMeta(): array
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);

    return [
        'method' => is_string($method) ? $method : 'CLI',
        'uri' => is_string($path) && $path !== '' ? $path : '/',
    ];
}

/**
 * Append a structured bootstrap log line to laravel.log.
 */
function writeBootstrapLog(string $level, string $message, array $context = []): void
{
    global $logPath, $requestId, $laravelRoot, $bootstrapLogWritten;

    $meta = bootstrapRequestMeta();
    $timestamp = date('c');
    $safeMessage = sanitizeBootstrapMessage($message);

    $safeContext = [];
    foreach ($context as $key => $value) {
        if (! is_scalar($value) && $value !== null) {
            continue;
        }
        $safeContext[(string) $key] = sanitizeBootstrapMessage((string) $value, 500);
    }

    $contextJson = $safeContext !== [] ? ' '.json_encode($safeContext, JSON_UNESCAPED_SLASHES) : '';

    $line = sprintf(
        "[%s] hostinger.%s: [HOSTINGER-BOOTSTRAP] rid=%s %s %s %s%s\n",
        $timestamp,
        strtolower($level),
        $requestId,
        $meta['method'],
        $meta['uri'],
        $safeMessage,
        $contextJson
    );

    $written = false;
    if (is_string($logPath) && (is_file($logPath) || is_dir(dirname($logPath)))) {
        $written = @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX) !== false;
    }

    if (! $written) {
        error_log(rtrim($line));
    } else {
        $bootstrapLogWritten = true;
    }
}

/**
 * Generic HTTP 500 — no paths, traces, or environment details.
 */
function respondBootstrapFailure(): void
{
    if (! headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-store');
    }

    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Service Unavailable</title></head>';
    echo '<body><h1>Terjadi kesalahan</h1><p>Silakan coba lagi nanti.</p></body></html>';
}

register_shutdown_function(static function () use ($laravelRoot, $requestId): void {
    static $handled = false;

    if ($handled) {
        return;
    }

    $error = error_get_last();
    if ($error === null) {
        return;
    }

    $fatalTypes = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR,
    ];

    if (! in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    $handled = true;

    $typeMap = [
        E_ERROR => 'E_ERROR',
        E_PARSE => 'E_PARSE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_USER_ERROR => 'E_USER_ERROR',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
    ];

    writeBootstrapLog('critical', 'Fatal PHP error before/during bootstrap', [
        'type' => $typeMap[$error['type']] ?? (string) $error['type'],
        'message' => sanitizeBootstrapMessage((string) ($error['message'] ?? '')),
        'file' => relativeBootstrapPath($error['file'] ?? null, $laravelRoot),
        'line' => (string) ($error['line'] ?? 0),
        'request_id' => $requestId,
        'memory' => (string) memory_get_usage(true),
    ]);

    if (! headers_sent() && http_response_code() < 400) {
        respondBootstrapFailure();
    }
});

if (! is_dir($laravelRoot)) {
    writeBootstrapLog('critical', 'Laravel root directory missing', [
        'hint' => 'expected sibling folder ppid-poltekkesbanten next to public_html',
    ]);
    respondBootstrapFailure();
    exit(1);
}

if (! is_writable($logDirectory) && is_dir($logDirectory)) {
    writeBootstrapLog('warning', 'storage/logs is not writable');
}

if (is_file($logPath) && ! is_writable($logPath)) {
    writeBootstrapLog('warning', 'laravel.log is not writable');
}

$maintenance = $laravelRoot.'/storage/framework/maintenance.php';
if (file_exists($maintenance)) {
    require $maintenance;
}

if (! is_file($autoloadPath)) {
    writeBootstrapLog('critical', 'Missing vendor/autoload.php — run composer install on app root');
    respondBootstrapFailure();
    exit(1);
}

if (! is_file($bootstrapPath)) {
    writeBootstrapLog('critical', 'Missing bootstrap/app.php');
    respondBootstrapFailure();
    exit(1);
}

try {
    require $autoloadPath;

    /** @var Application $app */
    $app = require_once $bootstrapPath;

    // Web root Hostinger is public_html (not app public/)
    $app->usePublicPath(__DIR__);

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    $traceLines = [];
    foreach (array_slice($e->getTrace(), 0, 20) as $frame) {
        $file = relativeBootstrapPath($frame['file'] ?? null, $laravelRoot);
        $line = $frame['line'] ?? 0;
        $fn = ($frame['class'] ?? '').($frame['type'] ?? '').($frame['function'] ?? '');
        $traceLines[] = sanitizeBootstrapMessage("{$file}:{$line} {$fn}");
    }

    writeBootstrapLog('critical', 'Uncaught Throwable during bootstrap/request', [
        'exception' => $e::class,
        'message' => sanitizeBootstrapMessage($e->getMessage()),
        'file' => relativeBootstrapPath($e->getFile(), $laravelRoot),
        'line' => (string) $e->getLine(),
        'trace' => implode(' | ', $traceLines),
    ]);

    respondBootstrapFailure();
    exit(1);
}
