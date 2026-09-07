<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PublicDocument;
use App\Models\ServiceLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GlobalSearchService
{
    public const MIN_QUERY = 2;

    public const MAX_QUERY = 120;

    /**
     * @return array{
     *     query: string,
     *     total: int,
     *     groups: list<array{key: string, label: string, items: list<array<string, mixed>>}>
     * }
     */
    public function search(string $query, int $limitPerGroup = 12): array
    {
        $normalized = $this->normalize($query);

        if ($normalized === null) {
            return [
                'query' => trim($query),
                'total' => 0,
                'groups' => [],
            ];
        }

        $groups = array_values(array_filter([
            $this->group('services', 'Layanan', $this->searchServices($normalized, $limitPerGroup)),
            $this->group('documents', 'Dokumen Publik', $this->searchDocuments($normalized, $limitPerGroup)),
            $this->group('pages', 'Halaman', $this->searchPages($normalized, $limitPerGroup)),
            $this->group('posts', 'Berita', $this->searchPosts($normalized, $limitPerGroup)),
            $this->group('announcements', 'Pengumuman', $this->searchAnnouncements($normalized, $limitPerGroup)),
            $this->group('faqs', 'FAQ', $this->searchFaqs($normalized, $limitPerGroup)),
        ]));

        return [
            'query' => $normalized,
            'total' => collect($groups)->sum(fn (array $group) => count($group['items'])),
            'groups' => $groups,
        ];
    }

    /**
     * Flattened suggestions for the navbar panel.
     *
     * @return list<array<string, mixed>>
     */
    public function suggestions(string $query, int $limit = 10): array
    {
        $result = $this->search($query, 4);
        $items = [];

        foreach ($result['groups'] as $group) {
            foreach ($group['items'] as $item) {
                $items[] = [
                    'type' => $item['type'],
                    'group' => $group['label'],
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'available' => $item['available'],
                    'external' => $item['external'],
                    'badge' => $item['badge'],
                    'metadata' => $item['metadata'],
                    'category' => $item['metadata'],
                ];
                if (count($items) >= $limit) {
                    return $items;
                }
            }
        }

        return $items;
    }

    public function normalize(string $query): ?string
    {
        $query = trim(preg_replace('/\s+/u', ' ', $query) ?? '');

        if ($query === '' || mb_strlen($query) < self::MIN_QUERY) {
            return null;
        }

        if (mb_strlen($query) > self::MAX_QUERY) {
            $query = mb_substr($query, 0, self::MAX_QUERY);
        }

        return $query;
    }

    /** @param list<array<string, mixed>> $items */
    private function group(string $key, string $label, array $items): ?array
    {
        if ($items === []) {
            return null;
        }

        return compact('key', 'label', 'items');
    }

    /** @return list<array<string, mixed>> */
    private function searchServices(string $query, int $limit): array
    {
        $like = $this->like($query);
        $rows = ServiceLink::query()
            ->where('is_active', true)
            ->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                    ->orWhereRaw('LOWER(slug) LIKE ?', [$like]);
            })
            ->orderBy('sort_order')
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (ServiceLink $service) {
            return [
                'type' => 'service',
                'title' => $service->title,
                'description' => Str::limit((string) $service->excerpt, 140),
                'url' => route('services.show', ['slug' => $service->slug]),
                'badge' => 'Layanan',
                'metadata' => 'Layanan informasi publik',
                'available' => true,
                'external' => false,
                'fields' => [$service->title, (string) $service->excerpt, (string) $service->description, $service->slug],
            ];
        });
    }

    /** @return list<array<string, mixed>> */
    private function searchDocuments(string $query, int $limit): array
    {
        $like = $this->like($query);
        $year = ctype_digit($query) ? (int) $query : null;

        $rows = PublicDocument::query()
            ->published()
            ->with(['informationCategory', 'documentCategory'])
            ->where(function ($q) use ($like, $year) {
                $q->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(section_title, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(file_name, \'\')) LIKE ?', [$like]);

                if ($year !== null && $year >= 1990 && $year <= 2100) {
                    $q->orWhere('year', $year);
                }

                $q->orWhereHas('informationCategory', function ($cat) use ($like) {
                    $cat->whereRaw('LOWER(name) LIKE ?', [$like]);
                });
            })
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (PublicDocument $document) {
            $available = $document->isAvailable();
            $href = $document->publicHref();
            $category = $document->informationCategory?->name;
            $meta = array_values(array_filter([
                $document->section_title,
                $category,
                $document->year ? (string) $document->year : null,
            ]));

            return [
                'type' => 'document',
                'title' => $document->title,
                'description' => Str::limit((string) ($document->section_title ?: $category), 140),
                'url' => $available ? $href : null,
                'badge' => $available ? 'Tersedia' : 'Dokumen belum tersedia',
                'metadata' => implode(' • ', $meta),
                'available' => $available,
                'external' => $available && $document->opensInNewTab(),
                'fields' => [
                    $document->title,
                    (string) $document->section_title,
                    (string) $document->description,
                    (string) $document->file_name,
                    (string) $document->year,
                    (string) $category,
                ],
            ];
        });
    }

    /** @return list<array<string, mixed>> */
    private function searchPages(string $query, int $limit): array
    {
        $like = $this->like($query);
        $rows = Page::query()
            ->published()
            ->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(COALESCE(excerpt, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(body, '')) LIKE ?", [$like])
                    ->orWhereRaw('LOWER(slug) LIKE ?', [$like]);
            })
            ->orderBy('sort_order')
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (Page $page) {
            return [
                'type' => 'page',
                'title' => $page->title,
                'description' => Str::limit((string) $page->excerpt, 140),
                'url' => route('pages.show', ['slug' => $page->slug]),
                'badge' => 'Halaman',
                'metadata' => 'Halaman PPID',
                'available' => true,
                'external' => false,
                'fields' => [$page->title, (string) $page->excerpt, (string) $page->body, $page->slug],
            ];
        });
    }

    /** @return list<array<string, mixed>> */
    private function searchPosts(string $query, int $limit): array
    {
        $like = $this->like($query);
        $rows = Post::query()
            ->published()
            ->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(COALESCE(excerpt, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(body, '')) LIKE ?", [$like]);
            })
            ->latest('published_at')
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (Post $post) {
            return [
                'type' => 'post',
                'title' => $post->title,
                'description' => Str::limit((string) $post->excerpt, 140),
                'url' => route('posts.show', ['slug' => $post->slug]),
                'badge' => 'Berita',
                'metadata' => optional($post->published_at)?->translatedFormat('d F Y'),
                'available' => true,
                'external' => false,
                'fields' => [$post->title, (string) $post->excerpt, (string) $post->body],
            ];
        });
    }

    /** @return list<array<string, mixed>> */
    private function searchAnnouncements(string $query, int $limit): array
    {
        $like = $this->like($query);
        $rows = Announcement::query()
            ->published()
            ->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(COALESCE(excerpt, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(body, '')) LIKE ?", [$like]);
            })
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (Announcement $item) {
            return [
                'type' => 'announcement',
                'title' => $item->title,
                'description' => Str::limit((string) $item->excerpt, 140),
                'url' => route('announcements.show', ['slug' => $item->slug]),
                'badge' => 'Pengumuman',
                'metadata' => optional($item->published_at)?->translatedFormat('d F Y'),
                'available' => true,
                'external' => false,
                'fields' => [$item->title, (string) $item->excerpt, (string) $item->body],
            ];
        });
    }

    /** @return list<array<string, mixed>> */
    private function searchFaqs(string $query, int $limit): array
    {
        $like = $this->like($query);
        $rows = Faq::query()
            ->where('is_active', true)
            ->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(question) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(answer) LIKE ?', [$like]);
            })
            ->orderBy('sort_order')
            ->limit(40)
            ->get();

        return $this->rank($rows, $query, $limit, function (Faq $faq) {
            return [
                'type' => 'faq',
                'title' => $faq->question,
                'description' => Str::limit(strip_tags((string) $faq->answer), 140),
                'url' => route('faqs.index').'#'.$faq->slug,
                'badge' => 'FAQ',
                'metadata' => 'Pertanyaan umum',
                'available' => true,
                'external' => false,
                'fields' => [$faq->question, (string) $faq->answer],
            ];
        });
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @param  callable(mixed): array<string, mixed>  $mapper
     * @return list<array<string, mixed>>
     */
    private function rank(Collection $rows, string $query, int $limit, callable $mapper): array
    {
        $needle = mb_strtolower($query);

        return $rows
            ->map(function ($row) use ($mapper, $needle) {
                $item = $mapper($row);
                $item['relevance'] = $this->score($needle, $item['fields'] ?? []);
                unset($item['fields']);

                return $item;
            })
            ->sort(function (array $a, array $b) {
                return $b['relevance'] <=> $a['relevance'];
            })
            ->take($limit)
            ->values()
            ->all();
    }

    /** @param list<string> $fields */
    private function score(string $needle, array $fields): int
    {
        $title = mb_strtolower((string) ($fields[0] ?? ''));

        if ($title === $needle) {
            return 100;
        }
        if ($title !== '' && str_starts_with($title, $needle)) {
            return 80;
        }
        if ($title !== '' && str_contains($title, $needle)) {
            return 60;
        }

        foreach (array_slice($fields, 1) as $index => $field) {
            $haystack = mb_strtolower((string) $field);
            if ($haystack !== '' && str_contains($haystack, $needle)) {
                return max(15, 40 - ($index * 8));
            }
        }

        return 5;
    }

    private function like(string $query): string
    {
        $safe = str_replace(['\\', '%', '_'], ['', '', ''], mb_strtolower($query));

        return '%'.$safe.'%';
    }
}
