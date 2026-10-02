<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class PublicNavigation
{
    public static function items(): array
    {
        if (SiteSetting::getValue('navigation.public_source') !== 'database') {
            return config('ppid.navigation', []);
        }

        $built = Cache::remember('public_navigation_database', 3600, fn () => self::fromDatabase());

        return $built !== [] ? $built : config('ppid.navigation', []);
    }

    public static function forget(): void
    {
        Cache::forget('public_navigation_database');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function fromDatabase(): array
    {
        $menu = Menu::query()->where('location', 'primary')->where('is_active', true)->first();
        if (! $menu) {
            return [];
        }

        $publishedSlugs = Page::query()->published()->pluck('slug')->all();

        return $menu->items()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (MenuItem $item) => self::mapItem($item, $publishedSlugs))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $publishedSlugs
     * @return array<string, mixed>|null
     */
    private static function mapItem(MenuItem $item, array $publishedSlugs): ?array
    {
        $children = [];
        foreach ($item->children as $child) {
            $childMapped = self::target($child, $publishedSlugs);
            if ($childMapped !== null) {
                $children[] = $childMapped;
            }
        }

        $mapped = self::target($item, $publishedSlugs);
        if ($mapped === null && $children === []) {
            return null;
        }

        if ($mapped === null) {
            $mapped = [
                'label' => $item->label,
                'route' => null,
            ];
        }

        if ($children !== []) {
            $mapped['children'] = $children;
        }

        return $mapped;
    }

    /**
     * @param  list<string>  $publishedSlugs
     * @return array<string, mixed>|null
     */
    private static function target(MenuItem $item, array $publishedSlugs): ?array
    {
        if ($item->type === 'page') {
            if (! in_array($item->page_slug, $publishedSlugs, true)) {
                return null;
            }

            return [
                'label' => $item->label,
                'route' => 'pages.show',
                'params' => ['slug' => $item->page_slug],
            ];
        }

        if ($item->type === 'url') {
            $url = (string) $item->url;
            if (! preg_match('#^https?://#i', $url)) {
                return null;
            }

            return [
                'label' => $item->label,
                'url' => $url,
            ];
        }

        if ($item->type === 'route' && filled($item->route_name)) {
            return [
                'label' => $item->label,
                'route' => $item->route_name,
                'params' => $item->route_params ?? [],
            ];
        }

        return null;
    }
}
