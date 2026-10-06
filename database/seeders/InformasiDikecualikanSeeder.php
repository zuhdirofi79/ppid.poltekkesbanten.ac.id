<?php

namespace Database\Seeders;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use App\Support\InformasiDikecualikan;
use Illuminate\Database\Seeder;

class InformasiDikecualikanSeeder extends Seeder
{
    public function run(): void
    {
        $category = PublicInformationCategory::query()->where('slug', InformasiDikecualikan::SLUG)->first();
        if (! $category) {
            return;
        }

        $adminId = User::query()
            ->where('email', config('ppid.admin.email'))
            ->orWhere('email', env('SUPER_ADMIN_EMAIL'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        $slug = PublicDocument::makeCatalogSlug(InformasiDikecualikan::DOCUMENT_TITLE);
        $document = PublicDocument::withTrashed()->where('slug', $slug)->first();

        if ($document?->trashed()) {
            $document->restore();
        }

        if ($document && filled($document->year) && filled($document->internal_url)) {
            return;
        }

        $attributes = [
            'title' => InformasiDikecualikan::DOCUMENT_TITLE,
            'public_information_category_id' => $category->id,
            'description' => InformasiDikecualikan::DOCUMENT_DESCRIPTION,
            'year' => InformasiDikecualikan::YEAR,
            'source_type' => PublicDocument::SOURCE_INTERNAL,
            'internal_url' => InformasiDikecualikan::INTERNAL_PATH,
            'external_url' => null,
            'file_path' => null,
            'file_name' => InformasiDikecualikan::FILENAME,
            'file_type' => 'docx',
            'is_external' => false,
            'status' => 'published',
            'sort_order' => 10,
            'published_at' => $document?->published_at ?? now(),
            'updated_by' => $adminId,
        ];

        if (! $document) {
            $document = new PublicDocument([
                ...$attributes,
                'slug' => $slug,
                'created_by' => $adminId,
            ]);
        } else {
            $document->fill($attributes);
        }

        $document->syncSourceFlags();
        $document->save();
    }
}
