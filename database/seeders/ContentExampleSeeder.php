<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentExampleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        $category = PostCategory::query()->updateOrCreate(
            ['slug' => 'umum'],
            [
                'name' => 'Umum',
                'description' => 'Kategori berita umum (CONTOH).',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $posts = [
            [
                'title' => 'CONTOH: Pembukaan Layanan Informasi Publik PPID',
                'excerpt' => 'Contoh ringkasan berita. Konten resmi akan dikelola melalui panel admin.',
                'days' => 3,
            ],
            [
                'title' => 'CONTOH: Sosialisasi Keterbukaan Informasi Publik',
                'excerpt' => 'Contoh ringkasan berita kedua untuk tampilan kartu beranda.',
                'days' => 8,
            ],
            [
                'title' => 'CONTOH: Pemutakhiran Daftar Informasi Publik',
                'excerpt' => 'Contoh ringkasan berita ketiga. Akan diganti data dari database.',
                'days' => 14,
            ],
        ];

        foreach ($posts as $post) {
            $slug = Str::slug($post['title']);
            Post::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'post_category_id' => $category->id,
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'body' => '<p>'.$post['excerpt'].'</p><p>Ini adalah konten CONTOH untuk lingkungan development.</p>',
                    'status' => 'published',
                    'published_at' => now()->subDays($post['days']),
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ],
            );
        }

        $announcements = [
            [
                'title' => 'CONTOH: Jadwal Layanan PPID Minggu Ini',
                'excerpt' => 'Pengumuman contoh. Data resmi dikelola admin.',
                'pinned' => true,
            ],
            [
                'title' => 'CONTOH: Pemeliharaan Sistem Informasi',
                'excerpt' => 'Pengumuman contoh kedua untuk uji tampilan beranda.',
                'pinned' => false,
            ],
        ];

        foreach ($announcements as $item) {
            Announcement::query()->updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'body' => '<p>'.$item['excerpt'].'</p>',
                    'status' => 'published',
                    'is_pinned' => $item['pinned'],
                    'starts_at' => now()->subDay(),
                    'ends_at' => now()->addDays(14),
                    'published_at' => now()->subDay(),
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ],
            );
        }

        $faqs = [
            [
                'question' => 'CONTOH: Apa itu PPID?',
                'answer' => 'PPID adalah Pejabat Pengelola Informasi dan Dokumentasi. Jawaban ini contoh untuk development.',
            ],
            [
                'question' => 'CONTOH: Bagaimana cara mengajukan permohonan informasi?',
                'answer' => 'Gunakan menu Layanan → Permohonan Informasi. Formulir resmi akan dihubungkan oleh admin.',
            ],
            [
                'question' => 'CONTOH: Berapa lama waktu pelayanan informasi?',
                'answer' => 'Mengikuti ketentuan peraturan perundang-undangan terkait keterbukaan informasi publik. (CONTOH)',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::query()->updateOrCreate(
                ['slug' => Str::slug($faq['question'])],
                [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        Page::query()->updateOrCreate(
            ['slug' => 'maklumat-pelayanan'],
            [
                'title' => 'Maklumat Pelayanan',
                'excerpt' => 'Pernyataan kesanggupan Poltekkes Kemenkes Banten dalam menyelenggarakan pelayanan sesuai standar.',
                'body' => '<p>Halaman khusus tersedia di <a href="/profil/maklumat-pelayanan">/profil/maklumat-pelayanan</a>.</p>',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 10,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );
    }
}
