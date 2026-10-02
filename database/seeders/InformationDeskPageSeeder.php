<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\InformationDesk;
use Illuminate\Database\Seeder;

class InformationDeskPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => InformationDesk::SLUG],
            [
                'title' => InformationDesk::TITLE,
                'excerpt' => InformationDesk::EXCERPT,
                'meta_title' => InformationDesk::TITLE,
                'meta_description' => InformationDesk::META_DESCRIPTION,
                'body' => HtmlSanitizer::clean(InformationDesk::introHtml()),
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 21,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
