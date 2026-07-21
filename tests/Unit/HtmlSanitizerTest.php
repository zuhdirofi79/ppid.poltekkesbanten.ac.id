<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    #[Test]
    public function it_allows_safe_markup(): void
    {
        $html = '<h2>Judul</h2><p>Teks <strong>tebal</strong> dan <a href="/kontak">tautan</a>.</p>';

        $this->assertSame($html, HtmlSanitizer::clean($html));
    }

    #[Test]
    public function it_strips_script_and_event_handlers(): void
    {
        $dirty = '<p onclick="alert(1)">Halo</p><script>alert(2)</script><img src=x onerror="alert(3)"><a href="javascript:alert(4)">x</a>';

        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', strtolower($clean));
        $this->assertStringNotContainsString('<img', $clean);
        $this->assertStringContainsString('<p>', $clean);
        $this->assertStringContainsString('Halo', $clean);
    }
}
