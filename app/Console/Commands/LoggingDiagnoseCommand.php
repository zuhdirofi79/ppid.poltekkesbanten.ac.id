<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LoggingDiagnoseCommand extends Command
{
    protected $signature = 'ppid:logging-diagnose';

    protected $description = 'Diagnose Laravel logging path, permissions, and write a unique test marker';

    public function handle(): int
    {
        $failures = 0;
        $warnings = 0;

        $this->info('PPID logging diagnose');
        $this->line('Laravel: '.app()->version());
        $this->line('PHP: '.PHP_VERSION);
        $this->line('Environment: '.app()->environment());
        $this->line('Log channel: '.(string) config('logging.default'));
        $this->line('Log level: '.(string) config('logging.channels.single.level', config('logging.channels.'.config('logging.default').'.level', 'n/a')));

        $logPath = storage_path('logs/laravel.log');
        $logDir = storage_path('logs');
        $cacheDir = base_path('bootstrap/cache');

        $this->line('Resolved log path: '.$this->safePath($logPath));

        if (! is_dir($logDir)) {
            $this->error('FAIL: storage/logs does not exist');
            $failures++;
        } else {
            $this->info('PASS: storage/logs exists');
        }

        if (! is_dir($logDir) || ! is_writable($logDir)) {
            $this->error('FAIL: storage/logs is not writable');
            $failures++;
        } else {
            $this->info('PASS: storage/logs writable');
        }

        if (! is_file($logPath)) {
            $this->warn('WARN: laravel.log missing — attempting touch');
            $warnings++;
            @touch($logPath);
        }

        if (! is_file($logPath)) {
            $this->error('FAIL: laravel.log does not exist');
            $failures++;
        } else {
            $this->info('PASS: laravel.log exists');
        }

        if (! is_file($logPath) || ! is_writable($logPath)) {
            $this->error('FAIL: laravel.log is not writable');
            $failures++;
        } else {
            $this->info('PASS: laravel.log writable');
        }

        if (! is_dir($cacheDir) || ! is_writable($cacheDir)) {
            $this->error('FAIL: bootstrap/cache is not writable');
            $failures++;
        } else {
            $this->info('PASS: bootstrap/cache writable');
        }

        if (str_contains(str_replace('\\', '/', $logPath), '/public_html/')) {
            $this->error('FAIL: log path resolves under public_html');
            $failures++;
        } else {
            $this->info('PASS: log path is outside public_html');
        }

        $marker = 'LIVE_LOGGING_DIAGNOSTIC_TEST '.bin2hex(random_bytes(6));
        $before = is_file($logPath) ? (int) filesize($logPath) : -1;
        $mtimeBefore = is_file($logPath) ? (int) filemtime($logPath) : 0;

        try {
            Log::channel('single')->error($marker);
        } catch (\Throwable $e) {
            $this->error('FAIL: Log::error threw: '.$e::class);
            $failures++;
        }

        clearstatcache(true, $logPath);
        $after = is_file($logPath) ? (int) filesize($logPath) : -1;
        $mtimeAfter = is_file($logPath) ? (int) filemtime($logPath) : 0;
        $contents = is_file($logPath) ? (string) @file_get_contents($logPath) : '';

        if (str_contains($contents, $marker) && ($after > $before || $mtimeAfter >= $mtimeBefore)) {
            $this->info('PASS: diagnostic marker written');
        } else {
            $this->error('FAIL: diagnostic marker not found in laravel.log');
            $failures++;
        }

        if ($failures > 0) {
            $this->error("RESULT: FAIL ({$failures} failure(s), {$warnings} warning(s))");

            return self::FAILURE;
        }

        if ($warnings > 0) {
            $this->warn("RESULT: WARN ({$warnings} warning(s))");

            return self::SUCCESS;
        }

        $this->info('RESULT: PASS');

        return self::SUCCESS;
    }

    private function safePath(string $path): string
    {
        $base = str_replace('\\', '/', base_path());
        $normalized = str_replace('\\', '/', $path);

        if (str_starts_with($normalized, $base)) {
            return 'base_path'.substr($normalized, strlen($base));
        }

        return basename($normalized);
    }
}
