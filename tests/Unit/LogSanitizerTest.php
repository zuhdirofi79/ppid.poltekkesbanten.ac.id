<?php

namespace Tests\Unit;

use App\Support\LogSanitizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LogSanitizerTest extends TestCase
{
    #[Test]
    public function it_redacts_password_patterns(): void
    {
        $input = 'DB_PASSWORD=secret123 password=hunter2 password: plain APP_KEY=base64:abc SUPER_ADMIN_PASSWORD=x';

        $out = LogSanitizer::sanitize($input);

        $this->assertStringContainsString('DB_PASSWORD=[REDACTED]', $out);
        $this->assertStringContainsString('password=[REDACTED]', $out);
        $this->assertStringContainsString('password:[REDACTED]', $out);
        $this->assertStringContainsString('APP_KEY=[REDACTED]', $out);
        $this->assertStringContainsString('SUPER_ADMIN_PASSWORD=[REDACTED]', $out);
        $this->assertStringNotContainsString('secret123', $out);
        $this->assertStringNotContainsString('hunter2', $out);
    }

    #[Test]
    public function it_redacts_authorization_and_bearer(): void
    {
        $authLine = "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.payload.sig\nCookie: session=abc; token=xyz";
        $authOut = LogSanitizer::sanitize($authLine);

        $this->assertStringContainsString('Authorization:[REDACTED]', $authOut);
        $this->assertStringContainsString('Cookie:[REDACTED]', $authOut);
        $this->assertStringNotContainsString('eyJhbGciOiJIUzI1NiJ9', $authOut);
        $this->assertStringNotContainsString('session=abc', $authOut);

        $bearerOnly = LogSanitizer::sanitize('token header Bearer eyJhbGciOiJIUzI1NiJ9.payload.sig trailing');
        $this->assertStringContainsString('Bearer [REDACTED]', $bearerOnly);
        $this->assertStringNotContainsString('eyJhbGciOiJIUzI1NiJ9', $bearerOnly);
    }

    #[Test]
    public function it_limits_trace_frames_without_arguments(): void
    {
        $exception = new \RuntimeException('boom password=secret');
        $frames = LogSanitizer::sanitizeTrace($exception, 20);

        $this->assertNotEmpty($frames);
        $this->assertLessThanOrEqual(20, count($frames));
        $joined = implode("\n", $frames);
        $this->assertStringNotContainsString('password=secret', LogSanitizer::sanitize($exception->getMessage()));
        $this->assertDoesNotMatchRegularExpression('/\(.*secret.*\)/', $joined);
    }
}
