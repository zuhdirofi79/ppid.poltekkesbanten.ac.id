<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\VisiDanMisi;
use Illuminate\Database\Seeder;

/**
 * Memperbarui halaman visi-dan-misi sekali, lalu tidak menimpa suntingan admin.
 *
 * Record diganti hanya bila belum ada, body kosong, atau body masih berisi visi PPID lama.
 * Body yang sudah memuat visi Tahun 2044 — termasuk hasil edit admin — dibiarkan.
 */
class VisiDanMisiPageSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        $page = Page::query()->where('slug', VisiDanMisi::SLUG)->first();
        $body = HtmlSanitizer::clean(VisiDanMisi::bodyHtml());

        if ($page === null) {
            Page::query()->create([
                'title' => VisiDanMisi::TITLE,
                'slug' => VisiDanMisi::SLUG,
                'excerpt' => VisiDanMisi::EXCERPT,
                'meta_title' => VisiDanMisi::META_TITLE,
                'meta_description' => VisiDanMisi::META_DESCRIPTION,
                'body' => $body,
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 3,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ]);

            return;
        }

        if (! VisiDanMisi::isLegacyRecord($page)) {
            return;
        }

        $updates = ['body' => $body];

        if (blank($page->title) || $page->title === VisiDanMisi::LEGACY_TITLE) {
            $updates['title'] = VisiDanMisi::TITLE;
        }

        if (blank($page->excerpt) || $page->excerpt === VisiDanMisi::LEGACY_EXCERPT) {
            $updates['excerpt'] = VisiDanMisi::EXCERPT;
        }

        if (blank($page->meta_title) || $page->meta_title === VisiDanMisi::LEGACY_META_TITLE) {
            $updates['meta_title'] = VisiDanMisi::META_TITLE;
        }

        if (blank($page->meta_description) || str_contains((string) $page->meta_description, VisiDanMisi::LEGACY_META_MARKER)) {
            $updates['meta_description'] = VisiDanMisi::META_DESCRIPTION;
        }

        if ($page->published_at === null && $page->status === 'published') {
            $updates['published_at'] = now();
        }

        if ($admin !== null) {
            $updates['updated_by'] = $admin->id;
        }

        $page->update($updates);
    }
}
