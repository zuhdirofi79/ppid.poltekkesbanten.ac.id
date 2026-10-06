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

        if ($slug === 'daftar-registrasi-pemohon') {
            $title = 'Daftar Registrasi Pemohon';
            $description = 'Daftar Register Permohonan Informasi Publik merupakan dokumen pencatatan permohonan informasi yang diterima oleh PPID Poltekkes Kemenkes Banten.';

            return view('pages.service', [
                'service' => array_merge($service, [
                    'slug' => $slug,
                    'title' => $title,
                    'excerpt' => $description,
                ]),
                'title' => $title,
                'description' => $description,
            ]);
        }

        if ($slug === 'pengaduan-penyalahgunaan-wewenang') {
            return redirect()->route('services.show', ['slug' => 'alur-pengajuan-sengketa'], 301);
        }

        if ($slug === 'alur-pengajuan-sengketa') {
            $title = 'Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran';
            $description = 'Politeknik Kesehatan Kementerian Kesehatan Banten berkomitmen mewujudkan tata kelola pemerintahan yang bersih, transparan, akuntabel, dan bebas dari penyalahgunaan wewenang. Masyarakat, mahasiswa, pegawai, maupun pihak lainnya dapat menyampaikan pengaduan apabila mengetahui atau mengalami dugaan pelanggaran yang dilakukan oleh pejabat atau pegawai di lingkungan Poltekkes Kemenkes Banten.';

            return view('pages.service', [
                'service' => array_merge($service, [
                    'slug' => $slug,
                    'title' => $title,
                    'excerpt' => $description,
                ]),
                'title' => $title,
                'description' => $description,
                'complaintFormUrl' => $this->safeHttpUrl(config('ppid.complaint_request.external_url')),
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
