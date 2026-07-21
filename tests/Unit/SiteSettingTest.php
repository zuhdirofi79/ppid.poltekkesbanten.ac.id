<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_retrieves_setting_value_with_cache(): void
    {
        SiteSetting::query()->create([
            'group' => 'general',
            'key' => 'site_name',
            'value' => 'PPID Poltekkes Kemenkes Banten',
            'type' => 'text',
            'label' => 'Nama situs',
        ]);

        $this->assertSame(
            'PPID Poltekkes Kemenkes Banten',
            SiteSetting::getValue('site_name'),
        );

        $this->assertSame('fallback', SiteSetting::getValue('missing', 'fallback'));
    }
}
