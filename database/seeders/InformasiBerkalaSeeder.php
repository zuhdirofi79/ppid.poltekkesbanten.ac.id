<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class InformasiBerkalaSeeder extends Seeder
{
    public function run(): void
    {
        $category = PublicInformationCategory::query()->where('slug', 'berkala')->first();
        if (! $category) {
            if ($this->command) {
                $this->command->warn('Kategori berkala belum ada. Jalankan CategorySeeder terlebih dahulu.');
            }

            return;
        }

        $docCategory = DocumentCategory::query()->where('slug', 'laporan')->first()
            ?? DocumentCategory::query()->orderBy('id')->first();

        $admin = User::query()
            ->where('email', config('ppid.admin.email'))
            ->orWhere('email', env('SUPER_ADMIN_EMAIL'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        foreach ($this->catalog() as $index => $item) {
            $slug = PublicDocument::makeCatalogSlug($item['title']);

            $document = PublicDocument::withTrashed()->where('slug', $slug)->first();

            if ($document?->trashed()) {
                if ($this->shouldLeaveUnavailableLakinDeleted($item, $category->id)) {
                    continue;
                }

                $document->restore();
            }

            if (! $document) {
                $document = PublicDocument::query()->create([
                    'title' => $item['title'],
                    'slug' => $slug,
                    'public_information_category_id' => $category->id,
                    'document_category_id' => $docCategory?->id,
                    'section_title' => $item['section_title'],
                    'description' => $item['admin_notice'] ?? null,
                    'year' => $item['year'] ?? null,
                    'source_type' => $item['source_type'],
                    'file_path' => null,
                    'external_url' => null,
                    'internal_url' => null,
                    'is_external' => false,
                    'status' => 'published',
                    'sort_order' => ($index + 1) * 10,
                    'published_at' => now(),
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ]);
            }

            $hasResource = filled($document->file_path)
                || filled($document->external_url)
                || filled($document->internal_url);

            $metadata = [
                'public_information_category_id' => $category->id,
                'document_category_id' => $docCategory?->id,
                'year' => $item['year'] ?? $document->year,
                'sort_order' => ($index + 1) * 10,
                'status' => 'published',
                'published_at' => $document->published_at ?? now(),
                'updated_by' => $admin?->id,
            ];

            if (! $hasResource) {
                $metadata['title'] = $item['title'];
                $metadata['section_title'] = $item['section_title'];
            }

            $document->fill($metadata);

            if (! $hasResource) {
                $document->source_type = $item['source_type'];
                $document->description = $item['admin_notice'] ?? $document->description;

                if (($item['source_type'] ?? null) === PublicDocument::SOURCE_EXTERNAL) {
                    $document->external_url = $item['external_url'] ?? null;
                    $document->is_external = true;
                    $document->internal_url = null;
                } elseif (($item['source_type'] ?? null) === PublicDocument::SOURCE_INTERNAL) {
                    $document->internal_url = $item['internal_url'] ?? null;
                    $document->external_url = null;
                    $document->is_external = false;
                } else {
                    $document->external_url = null;
                    $document->internal_url = null;
                    $document->is_external = false;
                }
            }

            $document->syncSourceFlags();
            $document->save();
        }

        $this->removeUnavailableLakinDuplicates();
        $this->normalizeLakipTitlesToLakin();
        $this->removeUnavailableTarifPenunjangPlaceholder();
    }

    /**
     * Nonaktifkan hanya placeholder kosong "Tarif Layanan Penunjang Akademik".
     * Dokumen SK Tarif BLU yang sudah punya berkas tidak disentuh.
     */
    private function removeUnavailableTarifPenunjangPlaceholder(): void
    {
        $title = 'Tarif Layanan Penunjang Akademik';
        $section = 'Pola Tarif Poltekkes Kemenkes Banten';

        $documents = PublicDocument::query()
            ->where('title', $title)
            ->where('slug', PublicDocument::makeCatalogSlug($title))
            ->where('section_title', $section)
            ->get();

        foreach ($documents as $document) {
            if ($document->isAvailable()) {
                continue;
            }

            $document->delete();
        }
    }

    /**
     * Placeholder LAKIN 2023/2024 yang sudah dihapus tidak dibuat ulang
     * jika dokumen tersedia untuk tahun yang sama sudah ada.
     *
     * @param  array<string, mixed>  $item
     */
    private function shouldLeaveUnavailableLakinDeleted(array $item, int $categoryId): bool
    {
        $year = $item['year'] ?? null;
        if (! in_array($year, [2023, 2024], true)) {
            return false;
        }

        if (($item['section_title'] ?? null) !== 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten') {
            return false;
        }

        return $this->hasAvailableLakinForYear($categoryId, (int) $year);
    }

    private function hasAvailableLakinForYear(int $categoryId, int $year): bool
    {
        $section = 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten';

        return PublicDocument::query()
            ->where('public_information_category_id', $categoryId)
            ->where('section_title', $section)
            ->where(function ($query) use ($year) {
                $query->where('year', $year)
                    ->orWhere('title', "LAKIN Poltekkes Kemenkes Banten {$year}")
                    ->orWhere('title', "LAKIP Poltekkes Kemenkes Banten {$year}");
            })
            ->get()
            ->contains(fn (PublicDocument $document) => $document->isAvailable());
    }

    /**
     * Samakan judul LAKIP (dokumen tersedia) menjadi LAKIN agar section publik konsisten.
     */
    private function normalizeLakipTitlesToLakin(): void
    {
        $section = 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten';

        foreach ([2023, 2024] as $year) {
            $documents = PublicDocument::query()
                ->where('section_title', $section)
                ->where(function ($query) use ($year) {
                    $query->where('title', "LAKIP Poltekkes Kemenkes Banten {$year}")
                        ->orWhere('slug', PublicDocument::makeCatalogSlug("LAKIP Poltekkes Kemenkes Banten {$year}"));
                })
                ->get();

            foreach ($documents as $document) {
                if (! $document->isAvailable()) {
                    continue;
                }

                $newTitle = "LAKIN Poltekkes Kemenkes Banten {$year}";
                $newSlug = PublicDocument::makeCatalogSlug($newTitle);

                PublicDocument::onlyTrashed()
                    ->where('slug', $newSlug)
                    ->where('id', '!=', $document->id)
                    ->forceDelete();

                $slugTaken = PublicDocument::withTrashed()
                    ->where('slug', $newSlug)
                    ->where('id', '!=', $document->id)
                    ->exists();

                $document->title = $newTitle;
                $document->year = $year;
                if (! $slugTaken) {
                    $document->slug = $newSlug;
                }
                $document->save();
            }
        }
    }

    /**
     * Hapus placeholder LAKIN 2023/2024 yang kosong hanya jika sudah ada dokumen tersedia untuk tahun yang sama.
     * Dokumen ber-PDF/URL tidak dihapus.
     */
    private function removeUnavailableLakinDuplicates(): void
    {
        $section = 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten';

        foreach ([2023, 2024] as $year) {
            $documents = PublicDocument::query()
                ->where('section_title', $section)
                ->where(function ($query) use ($year) {
                    $query->where('year', $year)
                        ->orWhere('title', "LAKIN Poltekkes Kemenkes Banten {$year}")
                        ->orWhere('title', "LAKIP Poltekkes Kemenkes Banten {$year}");
                })
                ->get();

            $hasAvailable = $documents->contains(fn (PublicDocument $document) => $document->isAvailable());
            if (! $hasAvailable) {
                continue;
            }

            foreach ($documents as $document) {
                if ($document->isAvailable()) {
                    continue;
                }

                $document->delete();
            }
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            [
                'section_title' => 'Akreditasi Poltekkes Kemenkes Banten',
                'title' => 'Akreditasi Poltekkes Banten',
                'source_type' => PublicDocument::SOURCE_INTERNAL,
                'internal_url' => '/halaman/akreditasi',
                'year' => null,
            ],
            [
                'section_title' => 'Kegiatan (RAK) Poltekkes Kemenkes Banten',
                'title' => 'RAK Poltekkes Banten 2020–2024',
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://poltekkesbanten.ac.id/wp-content/uploads/2026/06/RAK-Poltekkes-Banten-2020-2024.pdf',
                'year' => 2024,
            ],
            [
                'section_title' => 'Rencana Strategis Bisnis (RSB) Poltekkes Kemenkes Banten',
                'title' => 'RSB Poltekkes Banten 2022–2026',
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://poltekkesbanten.ac.id/wp-content/uploads/2026/06/RSB-Poltekkes-Banten-2022-2026.pdf',
                'year' => 2022,
            ],
            [
                'section_title' => 'Rencana Strategis Bisnis (RSB) Poltekkes Kemenkes Banten',
                'title' => 'RSB Poltekkes Banten 2025–2029',
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://poltekkesbanten.ac.id/wp-content/uploads/2026/06/RSB-2025-2029.pdf',
                'year' => 2025,
            ],
            [
                'section_title' => 'Rencana Kerja dan Anggaran',
                'title' => 'Rencana Kerja dan Anggaran 2026',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Rencana Kerja dan Anggaran 2026 (RBA Tahun 2026) wajib diunggah oleh Admin PPID.',
                'year' => 2026,
            ],
            [
                'section_title' => 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten',
                'title' => 'LAKIN Poltekkes Kemenkes Banten 2022',
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://drive.google.com/file/d/1CJk9JkThnTUZdYH9VeMb1EXagXGucnFu/view',
                'year' => 2022,
            ],
            [
                'section_title' => 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten',
                'title' => 'LAKIN Poltekkes Kemenkes Banten 2023',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen LAKIN Tahun 2023 wajib diunggah oleh Admin PPID.',
                'year' => 2023,
            ],
            [
                'section_title' => 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten',
                'title' => 'LAKIN Poltekkes Kemenkes Banten 2024',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen LAKIN Tahun 2024 wajib diunggah oleh Admin PPID.',
                'year' => 2024,
            ],
            [
                'section_title' => 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten',
                'title' => 'LAKIN Poltekkes Kemenkes Banten 2025',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen LAKIN / LAKIP Tahun 2025 wajib diunggah oleh Admin PPID.',
                'year' => 2025,
            ],
            [
                'section_title' => 'Perjanjian Kinerja Poltekkes Kemenkes Banten',
                'title' => 'Perjanjian Kinerja Tahun 2023',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Perjanjian Kinerja Tahun 2023 wajib diunggah oleh Admin PPID.',
                'year' => 2023,
            ],
            [
                'section_title' => 'Perjanjian Kinerja Poltekkes Kemenkes Banten',
                'title' => 'Perjanjian Kinerja Tahun 2024',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Perjanjian Kinerja Tahun 2024 wajib diunggah oleh Admin PPID.',
                'year' => 2024,
            ],
            [
                'section_title' => 'Perjanjian Kinerja Poltekkes Kemenkes Banten',
                'title' => 'Perjanjian Kinerja Tahun 2025',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Perjanjian Kinerja Tahun 2025 wajib diunggah oleh Admin PPID.',
                'year' => 2025,
            ],
            [
                'section_title' => 'Panduan Akademik',
                'title' => 'Panduan Akademik 2023/2024',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Panduan Akademik 2023/2024 wajib diunggah oleh Admin PPID.',
                'year' => 2023,
            ],
            [
                'section_title' => 'Laporan Keuangan',
                'title' => 'Laporan Keuangan Tahun 2025 (Audited)',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Ringkasan Laporan Keuangan Poltekkes Kemenkes Banten Tahun Anggaran 2025 wajib diunggah oleh Admin PPID.',
                'year' => 2025,
            ],
            [
                'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
                'title' => 'Tarif Layanan Akademik',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Tarif Layanan Akademik wajib diunggah oleh Admin PPID.',
                'year' => null,
            ],
            [
                'section_title' => 'Laporan PPID',
                'title' => 'Laporan PPID Tahun 2024',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'admin_notice' => 'Dokumen Laporan PPID Tahun 2024 wajib diunggah oleh Admin PPID.',
                'year' => 2024,
            ],
        ];
    }
}
