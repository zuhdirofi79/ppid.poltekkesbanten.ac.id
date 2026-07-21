<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Page;
use App\Models\Post;
use App\Models\PublicDocument;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('profil.maklumat-pelayanan'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('posts.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => route('announcements.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => route('documents.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('information.index'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('faqs.index'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ]);

        foreach (Post::query()->published()->latest('published_at')->limit(200)->get(['slug', 'updated_at']) as $post) {
            $urls->push([
                'loc' => route('posts.show', $post->slug),
                'lastmod' => $post->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ]);
        }

        foreach (Page::query()->published()->limit(100)->get(['slug', 'updated_at']) as $page) {
            $urls->push([
                'loc' => route('pages.show', $page->slug),
                'lastmod' => $page->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ]);
        }

        foreach (PublicDocument::query()->published()->latest('published_at')->limit(200)->get(['slug', 'updated_at']) as $doc) {
            $urls->push([
                'loc' => route('documents.show', $doc->slug),
                'lastmod' => $doc->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ]);
        }

        foreach (Announcement::query()->published()->limit(100)->get(['slug', 'updated_at']) as $item) {
            $urls->push([
                'loc' => route('announcements.show', $item->slug),
                'lastmod' => $item->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ]);
        }

        $body = view('seo.sitemap', [
            'urls' => $urls,
        ])->render();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".ltrim($body);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
