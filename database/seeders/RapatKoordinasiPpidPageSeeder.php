<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\RapatKoordinasiPpid;
use Illuminate\Database\Seeder;

class RapatKoordinasiPpidPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => RapatKoordinasiPpid::SLUG],
            [
                'title' => RapatKoordinasiPpid::TITLE,
                'excerpt' => RapatKoordinasiPpid::EXCERPT,
                'meta_title' => RapatKoordinasiPpid::TITLE,
                'meta_description' => RapatKoordinasiPpid::META_DESCRIPTION,
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 24,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
