<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use App\Support\StatistikKepegawaian;
use Illuminate\Database\Seeder;

class StatistikKepegawaianSeeder extends Seeder
{
    public function run(): void
    {
        $category = PublicInformationCategory::query()->where('slug', 'setiap-saat')->first();
        if (! $category) {
            if ($this->command) {
                $this->command->warn('Kategori setiap-saat belum ada. Jalankan CategorySeeder terlebih dahulu.');
            }

            return;
        }

        $docCategory = DocumentCategory::query()->where('slug', 'laporan')->first();
        $adminId = User::query()
            ->where('email', config('ppid.admin.email'))
            ->orWhere('email', env('SUPER_ADMIN_EMAIL'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        foreach (StatistikKepegawaian::definitions() as $definition) {
            $this->ensureChart($category->id, $docCategory?->id, $adminId, $definition);
        }
    }

    /**
     * @param  array{period: string, period_key: string, title: string, caption: string, path: string, alt: string, sort_order: int}  $definition
     */
    private function ensureChart(int $categoryId, ?int $documentCategoryId, ?int $adminId, array $definition): void
    {
        $slug = PublicDocument::makeCatalogSlug($definition['title']);
        $document = PublicDocument::withTrashed()->where('slug', $slug)->first();

        if ($document?->trashed()) {
            $document->restore();
        }

        $internalUrl = '/'.$definition['path'];

        if (! $document) {
            $document = new PublicDocument([
                'title' => $definition['title'],
                'slug' => $slug,
                'public_information_category_id' => $categoryId,
                'document_category_id' => $documentCategoryId,
                'section_title' => StatistikKepegawaian::SECTION,
                'description' => $definition['caption'],
                'year' => 2026,
                'source_type' => PublicDocument::SOURCE_INTERNAL,
                'internal_url' => $internalUrl,
                'external_url' => null,
                'file_path' => null,
                'file_name' => basename($definition['path']),
                'file_type' => 'jpeg',
                'is_external' => false,
                'status' => 'published',
                'sort_order' => $definition['sort_order'],
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

        if ($hasResource && filled($document->section_title)) {
            return;
        }

        $document->forceFill([
            'public_information_category_id' => $document->public_information_category_id ?: $categoryId,
            'section_title' => $document->section_title ?: StatistikKepegawaian::SECTION,
            'description' => $document->description ?: $definition['caption'],
            'year' => $document->year ?: 2026,
            'source_type' => PublicDocument::SOURCE_INTERNAL,
            'internal_url' => $document->internal_url ?: $internalUrl,
            'external_url' => null,
            'file_type' => $document->file_type ?: 'jpeg',
            'is_external' => false,
            'status' => $document->status ?: 'published',
            'sort_order' => $document->sort_order ?: $definition['sort_order'],
            'published_at' => $document->published_at ?? now(),
            'updated_by' => $adminId,
        ]);
        $document->syncSourceFlags();
        $document->save();
    }
}
