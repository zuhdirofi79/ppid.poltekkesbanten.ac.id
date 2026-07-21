<?php

namespace Database\Seeders;

use App\Models\ServiceLink;
use Illuminate\Database\Seeder;

class ServiceLinkSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('ppid.services', []) as $service) {
            ServiceLink::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'title' => $service['title'],
                    'excerpt' => $service['excerpt'],
                    'external_url' => $service['external_url'] ?? null,
                    'is_external' => filled($service['external_url'] ?? null),
                    'is_featured' => $service['is_featured'] ?? false,
                    'is_active' => true,
                    'sort_order' => $service['sort_order'] ?? 0,
                ],
            );
        }
    }
}
