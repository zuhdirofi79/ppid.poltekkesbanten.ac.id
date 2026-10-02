<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class InformasiSertaMertaSeeder extends Seeder
{
    public const TITLE = 'SK Penetapan PPID Poltekkes Kemenkes Banten Tahun 2025';

    public const SECTION = 'SK Penetapan PPID';

    public const INTERNAL_PATH = '/docs/SK PPID 2025.docx.pdf';

    public function run(): void
    {
        $category = PublicInformationCategory::query()->where('slug', 'serta-merta')->first();
        if (! $category) {
            if ($this->command) {
                $this->command->warn('Kategori serta-merta belum ada. Jalankan CategorySeeder terlebih dahulu.');
            }

            return;
        }

        $docCategory = DocumentCategory::query()->where('slug', 'kelembagaan')->first();
        $adminId = User::query()
            ->where('email', config('ppid.admin.email'))
            ->orWhere('email', env('SUPER_ADMIN_EMAIL'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        $slug = PublicDocument::makeCatalogSlug(self::TITLE);
        $document = PublicDocument::withTrashed()->where('slug', $slug)->first();

        if ($document?->trashed()) {
            $document->restore();
        }

        if (! $document) {
            $document = new PublicDocument([
                'title' => self::TITLE,
                'slug' => $slug,
                'public_information_category_id' => $category->id,
                'document_category_id' => $docCategory?->id,
                'section_title' => self::SECTION,
                'year' => 2025,
                'source_type' => PublicDocument::SOURCE_INTERNAL,
                'internal_url' => self::INTERNAL_PATH,
                'external_url' => null,
                'file_path' => null,
                'is_external' => false,
                'status' => 'published',
                'sort_order' => 10,
                'published_at' => now(),
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
            $document->syncSourceFlags();
            $document->save();

            return;
        }

        $hasResource = filled($document->file_path)
            || filled($document->external_url)
            || filled($document->internal_url);

        if ($hasResource) {
            return;
        }

        $document->forceFill([
            'public_information_category_id' => $document->public_information_category_id ?: $category->id,
            'section_title' => $document->section_title ?: self::SECTION,
            'year' => $document->year ?: 2025,
            'source_type' => PublicDocument::SOURCE_INTERNAL,
            'internal_url' => self::INTERNAL_PATH,
            'external_url' => null,
            'is_external' => false,
            'status' => $document->status ?: 'published',
            'published_at' => $document->published_at ?? now(),
            'updated_by' => $adminId,
        ]);
        $document->syncSourceFlags();
        $document->save();
    }
}
