<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HostingerBootstrapLoggingTest extends TestCase
{
    private function hostingerIndexSource(): string
    {
        return (string) file_get_contents(dirname(__DIR__, 2).'/deployment/hostinger/index.php');
    }

    private function publicIndexSource(): string
    {
        return (string) file_get_contents(dirname(__DIR__, 2).'/public/index.php');
    }

    #[Test]
    public function hostinger_index_points_php_error_log_to_laravel_log(): void
    {
        $src = $this->hostingerIndexSource();

        $this->assertStringContainsString("\$logPath = \$logDirectory.'/laravel.log'", $src);
        $this->assertStringContainsString("ini_set('error_log', \$logPath)", $src);
        $this->assertStringContainsString('[HOSTINGER-BOOTSTRAP]', $src);
        $this->assertStringContainsString('writeBootstrapLog', $src);
        $this->assertStringContainsString('register_shutdown_function', $src);
        $this->assertStringContainsString('E_PARSE', $src);
        $this->assertStringContainsString('E_ERROR', $src);
        $this->assertStringContainsString('usePublicPath', $src);
        $this->assertStringContainsString("\$laravelRoot = __DIR__.'/../ppid-poltekkesbanten'", $src);
        $this->assertStringNotContainsString('C:/', $src);
        $this->assertStringNotContainsString('chmod 777', $src);
        $this->assertStringNotContainsString('display_errors\', \'1\'', $src);
    }

    #[Test]
    public function hostinger_index_validates_autoload_and_bootstrap_before_require(): void
    {
        $src = $this->hostingerIndexSource();

        $this->assertStringContainsString('is_file($autoloadPath)', $src);
        $this->assertStringContainsString('is_file($bootstrapPath)', $src);
        $this->assertStringContainsString('respondBootstrapFailure', $src);
        $this->assertStringContainsString('catch (Throwable', $src);
        $this->assertStringNotContainsString('phpinfo()', $src);
        $this->assertStringNotContainsString('var_export($_ENV)', $src);
    }

    #[Test]
    public function hostinger_bootstrap_failure_response_has_no_stack_trace(): void
    {
        $src = $this->hostingerIndexSource();

        $this->assertMatchesRegularExpression(
            '/function respondBootstrapFailure\(\):\s*void\s*\{.*?echo\s*\'<!DOCTYPE html>/s',
            $src
        );
        $this->assertStringNotContainsString('getTraceAsString()', $src);
        $this->assertStringNotContainsString('APP_DEBUG', $src);
        $this->assertStringContainsString('Terjadi kesalahan', $src);
    }

    #[Test]
    public function public_index_remains_standard_laravel_front_controller(): void
    {
        $src = $this->publicIndexSource();

        $this->assertStringContainsString("require __DIR__.'/../vendor/autoload.php'", $src);
        $this->assertStringContainsString("require_once __DIR__.'/../bootstrap/app.php'", $src);
        $this->assertStringContainsString('handleRequest(Request::capture())', $src);
        $this->assertStringNotContainsString('ppid-poltekkesbanten', $src);
        $this->assertStringNotContainsString('HOSTINGER-BOOTSTRAP', $src);
        $this->assertStringNotContainsString('writeBootstrapLog', $src);
    }

    #[Test]
    public function shutdown_handler_covers_fatal_metadata_fields(): void
    {
        $src = $this->hostingerIndexSource();

        foreach (['type', 'message', 'file', 'line', 'request_id', 'memory'] as $field) {
            $this->assertStringContainsString("'{$field}'", $src, "Missing fatal metadata field: {$field}");
        }
    }

    #[Test]
    public function child_process_generic_500_hides_exception_details(): void
    {
        $script = <<<'PHP'
        <?php
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Service Unavailable</title></head>';
        echo '<body><h1>Terjadi kesalahan</h1><p>Silakan coba lagi nanti.</p></body></html>';
        PHP;

        $tmp = tempnam(sys_get_temp_dir(), 'ppid_boot_');
        $this->assertNotFalse($tmp);
        file_put_contents($tmp, $script);

        $php = PHP_BINARY;
        $output = [];
        $code = 0;
        exec(escapeshellarg($php).' '.escapeshellarg($tmp).' 2>&1', $output, $code);
        @unlink($tmp);

        $body = implode("\n", $output);
        $this->assertSame(0, $code);
        $this->assertStringContainsString('Terjadi kesalahan', $body);
        $this->assertStringNotContainsString('Stack trace', $body);
        $this->assertStringNotContainsString('vendor/', $body);
        $this->assertStringNotContainsString('APP_KEY', $body);
    }
}
