<?php

namespace App\Console\Commands;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class DiagnoseBerkalaCommand extends Command
{
    protected $signature = 'ppid:diagnose-berkala';

    protected $description = 'Read-only diagnosis of Informasi Berkala admin vs public listing (does not modify data)';

    public function handle(): int
    {
        $this->info('================================================');
        $this->info('INFORMASI BERKALA DIAGNOSTIC');
        $this->info('================================================');
        $this->newLine();

        $category = PublicInformationCategory::query()->where('slug', 'berkala')->first();
        if (! $category) {
            $this->error('Category berkala NOT FOUND');

            return self::FAILURE;
        }

        $this->line('Category:');
        $this->line('ID .................. '.$category->id);
        $this->line('Slug ................ '.$category->slug);
        $this->line('Active .............. '.($category->is_active ? 'YES' : 'NO'));
        $this->newLine();

        $adminIds = PublicDocument::query()
            ->where('public_information_category_id', $category->id)
            ->catalogOrdered()
            ->pluck('id');

        $legacyPublicIds = PublicDocument::query()
            ->published()
            ->where('public_information_category_id', $category->id)
            ->pluck('id');

        $catalogPublicIds = PublicDocument::query()
            ->inBerkalaCatalog()
            ->pluck('id');

        $this->line('Admin IDs ........... '.$this->idList($adminIds->all()));
        $this->line('Legacy public IDs ... '.$this->idList($legacyPublicIds->all()).'  (published() scope)');
        $this->line('Catalog public IDs .. '.$this->idList($catalogPublicIds->all()).'  (inBerkalaCatalog)');

        $diff = $adminIds->diff($legacyPublicIds);
        $this->line('Admin-only vs published() ... '.$this->idList($diff->all()));
        $this->newLine();

        $targets = [
            'SK Tarif BLU Akademik',
            'SK Tarif BLU Penunjang Akademik Rev 9',
            'Tarif Layanan Akademik',
            'Tarif Layanan Penunjang Akademik',
            'RAK Poltekkes Banten 2020–2024',
            'LAKIN Poltekkes Kemenkes Banten 2022',
        ];

        $mismatch = false;

        foreach ($targets as $title) {
            $document = PublicDocument::query()
                ->where('title', $title)
                ->orWhere('slug', PublicDocument::makeCatalogSlug($title))
                ->first();

            $this->line('DOCUMENT:');
            $this->line($title);
            $this->newLine();

            if (! $document) {
                $this->line('DB record ........... NOT FOUND');
                $this->newLine();

                continue;
            }

            $inAdmin = $adminIds->contains($document->id);
            $inLegacyPublic = $legacyPublicIds->contains($document->id);
            $inCatalogPublic = $catalogPublicIds->contains($document->id);
            $exclusion = $document->publishedScopeExclusionReason();

            $this->line('DB record ........... FOUND id='.$document->id);
            $this->line('Category ............ '.($document->informationCategory?->slug ?: 'NULL'));
            $this->line('Section ............. '.$this->inspectString((string) $document->section_title));
            $this->line('Status .............. '.(string) $document->status);
            $this->line('Published at ........ '.($document->published_at?->toDateTimeString() ?: 'NULL'));
            $this->line('Source Type ......... '.(string) $document->source_type);
            $this->line('File Path ........... '.(filled($document->file_path) ? 'SET' : 'NULL'));
            $this->line('External URL ........ '.(filled($document->external_url) ? 'SET' : 'NULL'));
            $this->line('Internal URL ........ '.(filled($document->internal_url) ? 'SET' : 'NULL'));
            $this->line('Has Source .......... '.($document->isAvailable() ? 'YES' : 'NO'));
            $this->line('Public href ......... '.($document->publicHref() ? 'VALID' : 'NULL'));
            $this->line('Admin query ......... '.($inAdmin ? 'INCLUDED' : 'EXCLUDED'));
            $this->line('Public published() .. '.($inLegacyPublic ? 'INCLUDED' : 'EXCLUDED').($exclusion ? ' ('.$exclusion.')' : ''));
            $this->line('Public catalog ...... '.($inCatalogPublic ? 'INCLUDED' : 'EXCLUDED'));
            $this->line('Cache ............... N/A (public berkala page is not collection-cached)');
            $this->newLine();

            if ($inAdmin && ! $inCatalogPublic && $document->isAvailable()) {
                $mismatch = true;
            } elseif ($inAdmin && ! $inLegacyPublic && $document->isAvailable()) {
                $this->warn('NOTE: published() excludes this row, but the catalog public query includes it.');
                $this->newLine();
            }
        }

        $this->line('Public berkala route .... '.(Route::has('information.category') ? 'PASS' : 'FAIL'));
        $this->line('Admin berkala route ..... '.(Route::has('informasi-publik.berkala') || Route::has('admin.informasi-publik.berkala') ? 'PASS' : 'FAIL'));

        if ($mismatch) {
            $this->newLine();
            $this->error('MISMATCH: admin includes an available document that the public catalog query excludes.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /** @param  list<int|string>  $ids */
    private function idList(array $ids): string
    {
        return $ids === [] ? '[]' : '['.implode(', ', $ids).']';
    }

    private function inspectString(string $value): string
    {
        return "'".$value."' (len=".mb_strlen($value).')';
    }
}
