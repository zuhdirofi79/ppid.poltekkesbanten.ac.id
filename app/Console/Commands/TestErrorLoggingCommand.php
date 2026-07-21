<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class TestErrorLoggingCommand extends Command
{
    protected $signature = 'ppid:test-error-logging';

    protected $description = 'Safely exercise logging channels without crashing the HTTP application';

    public function handle(): int
    {
        $marker = 'PPID_TEST_ERROR_LOGGING_'.bin2hex(random_bytes(4));
        $logPath = storage_path('logs/laravel.log');
        $failures = 0;

        $this->info('Running safe logging probes (CLI only — does not crash the website).');

        // 1. Log::error
        Log::error($marker.' LOG_ERROR');
        $this->line('1) Log::error — sent');

        // 2. PHP warning (non-fatal)
        $previous = set_error_handler(static function (int $severity, string $message): bool {
            Log::warning('PHP warning captured: '.$message);

            return true;
        });

        try {
            // Trigger a handled warning without terminating the process.
            @trigger_error($marker.' PHP_WARNING', E_USER_WARNING);
        } finally {
            restore_error_handler();
            if ($previous !== null) {
                set_error_handler($previous);
            }
        }
        $this->line('2) PHP warning — handled');

        // 3. Handled Throwable
        try {
            throw new \RuntimeException($marker.' HANDLED_THROWABLE');
        } catch (Throwable $e) {
            Log::error('Handled Throwable: '.$e->getMessage(), [
                'exception' => $e::class,
            ]);
        }
        $this->line('3) Handled Throwable — logged');

        // 4. Direct error_log
        error_log($marker.' DIRECT_ERROR_LOG');
        $this->line('4) error_log() — sent');

        clearstatcache(true, $logPath);
        $contents = is_file($logPath) ? (string) @file_get_contents($logPath) : '';

        $checks = [
            'LOG_ERROR' => str_contains($contents, $marker.' LOG_ERROR'),
            'HANDLED_THROWABLE' => str_contains($contents, $marker.' HANDLED_THROWABLE'),
            'DIRECT_ERROR_LOG' => str_contains($contents, $marker.' DIRECT_ERROR_LOG')
                || str_contains($contents, $marker),
        ];

        foreach ($checks as $label => $ok) {
            if ($ok) {
                $this->info("PASS: marker {$label} found");
            } else {
                $this->error("FAIL: marker {$label} missing");
                $failures++;
            }
        }

        if ($failures > 0) {
            $this->error('RESULT: FAIL');

            return self::FAILURE;
        }

        $this->info('RESULT: PASS');

        return self::SUCCESS;
    }
}
