<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\StatistikKeuangan;
use Illuminate\Database\Seeder;

class StatistikKeuanganPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => StatistikKeuangan::SLUG],
            [
                'title' => StatistikKeuangan::TITLE,
                'excerpt' => StatistikKeuangan::EXCERPT,
                'meta_title' => StatistikKeuangan::TITLE,
                'meta_description' => StatistikKeuangan::META_DESCRIPTION,
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 22,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
