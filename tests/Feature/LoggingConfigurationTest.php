<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoggingConfigurationTest extends TestCase
{
    #[Test]
    public function default_log_channel_uses_storage_path_single_file(): void
    {
        $path = config('logging.channels.single.path');

        $this->assertSame(storage_path('logs/laravel.log'), $path);
        $this->assertStringContainsString(DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'logs', $path);
        $this->assertStringNotContainsString('public_html', str_replace('\\', '/', $path));
        $this->assertStringNotContainsString('/public/', str_replace('\\', '/', $path));
    }

    #[Test]
    public function logging_default_prefers_single_channel(): void
    {
        $this->assertContains(config('logging.default'), ['single', 'stack']);

        $configFile = file_get_contents(base_path('config/logging.php'));
        $this->assertStringContainsString("env('LOG_CHANNEL', 'single')", (string) $configFile);
    }

    #[Test]
    public function diagnostic_command_writes_marker_without_leaking_app_key(): void
    {
        $logPath = storage_path('logs/laravel.log');
        File::ensureDirectoryExists(dirname($logPath));

        if (! File::exists($logPath)) {
            File::put($logPath, '');
        }

        $before = File::get($logPath);

        $exit = Artisan::call('ppid:logging-diagnose');
        $output = Artisan::output();

        $this->assertSame(0, $exit, $output);
        $this->assertStringContainsString('RESULT: PASS', $output);
        $this->assertStringNotContainsString((string) config('app.key'), $output);
        $this->assertStringNotContainsString('APP_KEY=', $output);
        $this->assertStringNotContainsString('base64:', $output);

        $after = File::get($logPath);
        $this->assertStringContainsString('LIVE_LOGGING_DIAGNOSTIC_TEST', $after);
        $this->assertNotSame($before, $after);
    }

    #[Test]
    public function test_error_logging_command_records_markers(): void
    {
        $logPath = storage_path('logs/laravel.log');
        File::ensureDirectoryExists(dirname($logPath));

        if (! File::exists($logPath)) {
            File::put($logPath, '');
        }

        $exit = Artisan::call('ppid:test-error-logging');
        $output = Artisan::output();

        $this->assertSame(0, $exit, $output);
        $this->assertStringContainsString('RESULT: PASS', $output);

        $contents = File::get($logPath);
        $this->assertStringContainsString('PPID_TEST_ERROR_LOGGING_', $contents);
        $this->assertStringContainsString('LOG_ERROR', $contents);
        $this->assertStringContainsString('HANDLED_THROWABLE', $contents);
    }
}
