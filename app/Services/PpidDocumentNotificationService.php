<?php

namespace App\Services;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PpidDocumentNotificationService
{
    public const CACHE_KEY = 'ppid.berkala.missing-docs';

    public const CACHE_SECONDS = 45;

    /**
     * Katalog Informasi Berkala yang tampil di portal publik (published + kategori aktif).
     *
     * @return Collection<int, PublicDocument>
     */
    public function berkalaDocuments(): Collection
    {
        $category = PublicInformationCategory::query()
            ->where('slug', 'berkala')
            ->where('is_active', true)
            ->first();

        if (! $category) {
            return collect();
        }

        return PublicDocument::query()
            ->where('public_information_category_id', $category->id)
            ->published()
            ->catalogOrdered()
            ->get();
    }

    /**
     * @return Collection<int, PublicDocument>
     */
    public function missingDocuments(?int $limit = null): Collection
    {
        $missing = $this->berkalaDocuments()
            ->filter(fn (PublicDocument $document) => ! $document->isAvailable())
            ->values();

        if ($limit !== null) {
            return $missing->take($limit)->values();
        }

        return $missing;
    }

    public function missingCount(): int
    {
        return (int) Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, function () {
            return $this->missingDocuments()->count();
        });
    }

    /**
     * @return array{total: int, available: int, missing: int}
     */
    public function stats(): array
    {
        $all = $this->berkalaDocuments();
        $missing = $all->filter(fn (PublicDocument $document) => ! $document->isAvailable())->count();

        return [
            'total' => $all->count(),
            'available' => $all->count() - $missing,
            'missing' => $missing,
        ];
    }

    public function badgeLabel(?int $count = null): string
    {
        $count ??= $this->missingCount();

        if ($count <= 0) {
            return '';
        }

        return $count > 99 ? '99+' : (string) $count;
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
