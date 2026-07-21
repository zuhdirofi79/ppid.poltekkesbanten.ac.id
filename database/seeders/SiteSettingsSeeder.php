<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'PPID Poltekkes Kemenkes Banten', 'type' => 'text', 'label' => 'Nama situs'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Layanan informasi publik yang cepat, tepat, transparan, akuntabel, dan mudah diakses.', 'type' => 'textarea', 'label' => 'Tagline'],
            ['group' => 'contact', 'key' => 'address', 'value' => '[TODO: Alamat resmi Direktorat]', 'type' => 'textarea', 'label' => 'Alamat'],
            ['group' => 'contact', 'key' => 'email', 'value' => '[TODO: email-ppid@poltekkesbanten.ac.id]', 'type' => 'text', 'label' => 'Email'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '[TODO: nomor telepon]', 'type' => 'text', 'label' => 'Telepon'],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '[TODO: nomor WhatsApp]', 'type' => 'text', 'label' => 'WhatsApp'],
            ['group' => 'contact', 'key' => 'service_hours', 'value' => 'Senin–Jumat, 08.00–15.30 WIB (CONTOH)', 'type' => 'text', 'label' => 'Jam layanan'],
            ['group' => 'map', 'key' => 'map_embed_url', 'value' => null, 'type' => 'url', 'label' => 'Embed peta'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Pejabat Pengelola Informasi dan Dokumentasi Poltekkes Kemenkes Banten.', 'type' => 'textarea', 'label' => 'Meta description'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
