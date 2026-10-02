<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Support\SopLayananDocuments;
use Database\Seeders\CategorySeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SopLayananInformasiPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SopLayananInformasiPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
        $this->seed(CategorySeeder::class);
    }

    #[Test]
    public function sop_page_lists_seven_documents_without_failing_when_a_file_is_missing(): void
    {
        $response = $this->get('/halaman/'.SopLayananDocuments::SLUG);

        $response->assertOk();
        $response->assertSee('PPID-SOP-LAYANAN-INFORMASI-V1', false);
        $response->assertSee('Standar Operasional Prosedur Layanan Informasi Publik', false);
        $response->assertSee('SOP &amp; Layanan', false);
        $response->assertSee('Kumpulan Standar Operasional Prosedur (SOP)', false);
        $response->assertSee('Beranda', false);
        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertStringContainsString(
            'Standar Operasional Prosedur Layanan Informasi Publik — '.config('ppid.name'),
            $html
        );
        $this->assertStringNotContainsString('/home/', $html);

        foreach (SopLayananDocuments::definitions() as $document) {
            $response->assertSee($document['title'], false);
            $encoded = SopLayananDocuments::publicUrl('docs/'.$document['filename']);
            if (is_file(public_path('docs/'.$document['filename']))) {
                $response->assertSee($encoded, false);
                $response->assertSee('Buka PDF', false);
                $response->assertSee('Preview', false);
                $response->assertSee('Tutup Preview', false);
                $response->assertSee('Download PDF', false);
            } else {
                $response->assertDontSee($encoded, false);
            }
        }

        $anyMissing = collect(SopLayananDocuments::definitions())
            ->contains(fn (array $document) => ! is_file(public_path('docs/'.$document['filename'])));
        if ($anyMissing) {
            $response->assertSee('Dokumen tidak ditemukan', false);
            $response->assertSee('Dokumen belum dapat ditampilkan. Silakan hubungi Admin PPID.', false);
        }
    }

    #[Test]
    public function catalog_keeps_production_filenames_and_encodes_extra_spaces(): void
    {
        $definitions = SopLayananDocuments::definitions();

        $this->assertCount(7, $definitions);
        $this->assertSame('SOP Pengelolaan Permohonan Informasi.pdf', $definitions[0]['filename']);
        $this->assertSame('SOP Pengelolaan Keberatan atas Informasi.pdf', $definitions[1]['filename']);
        $this->assertSame('SOP Penanganan Sengketa Informasi Publik.pdf', $definitions[2]['filename']);
        $this->assertSame('SOP Penetapan dan Pemuktahiran DIP.pdf', $definitions[3]['filename']);
        $this->assertSame('SOP Pengujian tentang Konsekuensi.pdf', $definitions[4]['filename']);
        $this->assertSame('SOP   pendokumentasian informasi publik.pdf', $definitions[5]['filename']);
        $this->assertSame('SOP  pendokumentasian informasi yang dikecualikan.pdf', $definitions[6]['filename']);

        $root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'ppid-sop-'.uniqid('', true);
        mkdir($root.DIRECTORY_SEPARATOR.'docs', 0777, true);

        try {
            $warnings = [];
            Event::listen(MessageLogged::class, function (MessageLogged $event) use (&$warnings): void {
                if ($event->level === 'warning') {
                    $warnings[] = $event->context['filename'] ?? null;
                }
            });

            $missing = SopLayananDocuments::resolve($root);
            $this->assertFalse($missing[5]['available']);
            $this->assertNull($missing[5]['url']);
            $this->assertContains($definitions[5]['filename'], $warnings);
            $this->assertContains($definitions[6]['filename'], $warnings);

            foreach ($definitions as $document) {
                file_put_contents(
                    $root.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$document['filename'],
                    "%PDF-1.4\n",
                );
            }

            $present = SopLayananDocuments::resolve($root);
            $this->assertStringContainsString('SOP%20Pengelolaan%20Permohonan%20Informasi.pdf', $present[0]['url']);
            $this->assertStringContainsString('SOP%20Pengelolaan%20Keberatan%20atas%20Informasi.pdf', $present[1]['url']);
            $this->assertStringContainsString('SOP%20Penanganan%20Sengketa%20Informasi%20Publik.pdf', $present[2]['url']);
            $this->assertStringContainsString('SOP%20Penetapan%20dan%20Pemuktahiran%20DIP.pdf', $present[3]['url']);
            $this->assertStringContainsString('SOP%20Pengujian%20tentang%20Konsekuensi.pdf', $present[4]['url']);
            $this->assertStringContainsString('SOP%20%20%20pendokumentasian%20informasi%20publik.pdf', $present[5]['url']);
            $this->assertStringContainsString('SOP%20%20pendokumentasian%20informasi%20yang%20dikecualikan.pdf', $present[6]['url']);
            $this->assertStringNotContainsString('hstgr.io', $present[0]['url']);
        } finally {
            foreach ($definitions as $document) {
                $path = $root.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$document['filename'];
                if (is_file($path)) {
                    unlink($path);
                }
            }
            @rmdir($root.DIRECTORY_SEPARATOR.'docs');
            @rmdir($root);
        }
    }

    #[Test]
    public function sop_page_seeder_does_not_overwrite_an_admin_excerpt(): void
    {
        $page = Page::query()->where('slug', SopLayananDocuments::SLUG)->firstOrFail();
        $page->update(['excerpt' => 'Diubah oleh admin PPID.']);

        $this->seed(SopLayananInformasiPageSeeder::class);

        $this->assertSame('Diubah oleh admin PPID.', $page->fresh()->excerpt);
        $this->assertSame(1, Page::query()->where('slug', SopLayananDocuments::SLUG)->count());
    }

    #[Test]
    public function related_public_pages_stay_available(): void
    {
        $this->get('/halaman/kalender-akademik')->assertOk();
        $this->get('/profil/maklumat-pelayanan')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();
        $this->get('/halaman/visi-dan-misi')->assertOk();
    }
}
