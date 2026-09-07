<?php

namespace App\Http\Controllers;

use App\Models\ServiceLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        $serviceModel = ServiceLink::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        $service = $serviceModel
            ? [
                'slug' => $serviceModel->slug,
                'title' => $serviceModel->title,
                'excerpt' => $serviceModel->excerpt,
                'external_url' => $serviceModel->external_url,
            ]
            : collect(config('ppid.services', []))->firstWhere('slug', $slug);

        $service = is_array($service) ? $service : [];

        $title = $service['title'] ?? str($slug)->replace('-', ' ')->title()->toString();
        $description = $service['excerpt'] ?? 'Layanan informasi publik PPID Poltekkes Kemenkes Banten.';

        if ($slug === 'permohonan-informasi') {
            return view('pages.service', [
                'service' => array_merge($service, [
                    'slug' => $slug,
                    'title' => $title,
                    'excerpt' => $description,
                ]),
                'title' => $title,
                'description' => $description,
                'formUrl' => $this->safeHttpUrl(config('ppid.information_request.external_url')),
                'ppidEmail' => (string) config(
                    'ppid.information_request.email',
                    'humas@poltekkesbanten.ac.id'
                ),
            ]);
        }

        if ($slug === 'pengajuan-keberatan') {
            return view('pages.service', [
                'service' => array_merge($service, [
                    'slug' => $slug,
                    'title' => $title,
                    'excerpt' => $description,
                ]),
                'title' => $title,
                'description' => $description,
                'formUrl' => $this->safeHttpUrl(config('ppid.objection_request.external_url')),
            ]);
        }

        $external = $service['external_url'] ?? null;
        if (is_string($external) && $this->safeHttpUrl($external)) {
            return redirect()->away($external);
        }

        return view('pages.service', [
            'service' => $service,
            'title' => $title,
            'description' => $description,
        ]);
    }

    private function safeHttpUrl(mixed $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }
}
