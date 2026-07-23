<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\HtmlSanitizer;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()->published()->where('slug', $slug)->firstOrFail();

        $seoTitle = $page->meta_title ?: $page->title;
        $seoDescription = $page->meta_description
            ?: $page->excerpt
            ?: config('ppid.name_long');

        $isProfil = $this->isProfilSlug($slug);

        return view('pages.show', [
            'page' => $page,
            'title' => $page->title,
            'seoTitle' => $seoTitle,
            'description' => $seoDescription,
            'excerpt' => $page->excerpt,
            'body' => HtmlSanitizer::clean($page->body),
            'label' => $isProfil ? 'Profil PPID' : null,
            'breadcrumbs' => array_values(array_filter([
                ['label' => 'Beranda', 'url' => route('home')],
                $isProfil ? ['label' => 'Profil', 'url' => null] : null,
                ['label' => $this->shortTitle($page->title), 'url' => null],
            ])),
        ]);
    }

    private function isProfilSlug(string $slug): bool
    {
        $profil = collect(config('ppid.navigation', []))
            ->firstWhere('label', 'Profil');

        $slugs = collect($profil['children'] ?? [])
            ->map(fn (array $child) => $child['params']['slug'] ?? null)
            ->filter()
            ->all();

        return in_array($slug, $slugs, true) || $slug === 'maklumat-pelayanan' || $slug === 'profil-singkat-ppid';
    }

    private function shortTitle(string $title): string
    {
        return match (true) {
            str_contains($title, 'Profil Singkat') => 'Profil Singkat PPID',
            str_contains($title, 'Dasar Hukum') => 'Dasar Hukum dan SK PPID',
            str_contains($title, 'Visi dan Misi') => 'Visi dan Misi',
            str_contains($title, 'Tugas, Fungsi') => 'Tugas, Fungsi, dan Wewenang PPID',
            default => $title,
        };
    }
}
