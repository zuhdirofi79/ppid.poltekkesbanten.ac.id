<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Post;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\ServiceLink;
use App\Models\Unit;
use App\Models\VisitorStatistic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomeContentService
{
    /**
     * @return array{
     *   categories: Collection,
     *   services: Collection,
     *   units: Collection,
     *   statistics: Collection,
     *   posts: Collection,
     *   announcements: Collection
     * }
     */
    public function forHomepage(): array
    {
        $categories = collect(config('ppid.information_categories', []));

        if ($this->ready('public_information_categories') && PublicInformationCategory::query()->exists()) {
            $categories = PublicInformationCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => [
                    'slug' => $c->slug,
                    'title' => $c->name,
                    'excerpt' => $c->excerpt,
                    'icon' => $c->icon ?? 'calendar',
                ]);
        }

        $services = ($this->ready('service_links') && ServiceLink::query()->exists())
            ? ServiceLink::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($s) => [
                    'slug' => $s->slug,
                    'title' => $s->title,
                    'excerpt' => $s->excerpt,
                    'external_url' => $s->external_url,
                    'is_featured' => $s->is_featured,
                    'sort_order' => $s->sort_order,
                ])
            : collect(config('ppid.services', []))->sortBy('sort_order')->values();

        $units = ($this->ready('units') && Unit::query()->exists())
            ? Unit::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($u) => [
                    'name' => $u->name,
                    'slug' => $u->slug,
                    'description' => $u->description,
                    'url' => $u->url,
                    'is_active' => $u->is_active,
                    'sort_order' => $u->sort_order,
                ])
            : collect(config('ppid.units', []))
                ->where('is_active', true)
                ->sortBy('sort_order')
                ->values();

        $posts = $this->ready('posts')
            ? Post::query()->published()->latest('published_at')->limit(3)->get()
            : collect();

        $announcements = $this->ready('announcements')
            ? Announcement::query()
                ->published()
                ->orderByDesc('is_pinned')
                ->latest('starts_at')
                ->limit(4)
                ->get()
            : collect();

        $statistics = $this->statistics($units->count());

        return compact('categories', 'services', 'units', 'statistics', 'posts', 'announcements');
    }

    private function statistics(int $unitCount): Collection
    {
        $yearVisits = ($this->ready('visitor_statistics'))
            ? (int) VisitorStatistic::query()->whereYear('date', now()->year)->sum('visits')
            : 0;

        $documentCount = ($this->ready('public_documents'))
            ? PublicDocument::query()->published()->count()
            : 0;

        return collect([
            [
                'label' => 'Permohonan Informasi',
                'value' => 0,
                'suffix' => '',
                'note' => 'Akan diisi data layanan',
            ],
            [
                'label' => 'Dokumen Publik',
                'value' => $documentCount,
                'suffix' => '',
                'note' => 'Terbit',
            ],
            [
                'label' => 'Pengunjung Tahun Ini',
                'value' => $yearVisits,
                'suffix' => '',
                'note' => $yearVisits > 0 ? 'Aktual' : 'Belum ada data',
            ],
            [
                'label' => 'Unit PPID Pelaksana',
                'value' => $unitCount,
                'suffix' => '',
                'note' => 'Direktorat + jurusan',
            ],
        ]);
    }

    private function ready(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
