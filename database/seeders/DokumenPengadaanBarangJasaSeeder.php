<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use App\Support\DokumenPengadaanBarangJasa;
use Illuminate\Database\Seeder;

class DokumenPengadaanBarangJasaSeeder extends Seeder
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

        $docCategory = DocumentCategory::query()->where('slug', 'lainnya')->first();
        $adminId = User::query()
            ->where('email', config('ppid.admin.email'))
            ->orWhere('email', env('SUPER_ADMIN_EMAIL'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        $slug = PublicDocument::makeCatalogSlug(DokumenPengadaanBarangJasa::SECTION);
        $document = PublicDocument::withTrashed()->where('slug', $slug)->first();

        if ($document?->trashed()) {
            $document->restore();
        }

        if (! $document) {
            $document = new PublicDocument([
                'title' => DokumenPengadaanBarangJasa::SECTION,
                'slug' => $slug,
                'public_information_category_id' => $category->id,
                'document_category_id' => $docCategory?->id,
                'section_title' => DokumenPengadaanBarangJasa::SECTION,
                'description' => DokumenPengadaanBarangJasa::DESCRIPTION,
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => DokumenPengadaanBarangJasa::URL,
                'internal_url' => null,
                'file_path' => null,
                'is_external' => true,
                'status' => 'published',
                'sort_order' => 5,
                'published_at' => now(),
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);
            $document->syncSourceFlags();
            $document->save();

            return;
        }

        if (filled($document->external_url) && filled($document->section_title)) {
            return;
        }

        $document->forceFill([
            'public_information_category_id' => $document->public_information_category_id ?: $category->id,
            'section_title' => $document->section_title ?: DokumenPengadaanBarangJasa::SECTION,
            'description' => $document->description ?: DokumenPengadaanBarangJasa::DESCRIPTION,
            'source_type' => PublicDocument::SOURCE_EXTERNAL,
            'external_url' => $document->external_url ?: DokumenPengadaanBarangJasa::URL,
            'is_external' => true,
            'status' => $document->status ?: 'published',
            'sort_order' => $document->sort_order ?: 5,
            'published_at' => $document->published_at ?? now(),
            'updated_by' => $adminId,
        ]);
        $document->syncSourceFlags();
        $document->save();
    }
}
