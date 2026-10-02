<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\LeadershipProfile;
use App\Models\LhkpnDocument;
use App\Models\Page;
use App\Support\HtmlSanitizer;
use App\Support\InformationDesk;
use App\Support\ServiceSummaryReport;
use App\Support\SopLayananDocuments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        if ($slug === 'informasi-berkala') {
            return redirect()->route('information.category', ['slug' => 'berkala'], 301);
        }

        // Handle kalender-akademik-YYYY alias redirect
        if (preg_match('/^kalender-akademik-(\d{4})$/', $slug, $matches)) {
            return redirect()->route('pages.show', [
                'slug' => 'kalender-akademik',
                'tahun' => $matches[1],
            ], 301);
        }

        $page = Page::query()->published()->where('slug', $slug)->firstOrFail();

        $seoTitle = $page->meta_title ?: $page->title;
        $seoDescription = $page->meta_description
            ?: $page->excerpt
            ?: config('ppid.name_long');

        if ($slug === 'profil-pimpinan-satuan-kerja') {
            if (blank($page->meta_title) || str_contains((string) $page->meta_title, 'sedang disiapkan')) {
                $seoTitle = 'Profil Pimpinan Satuan Kerja';
            }
            if (blank($page->meta_description) || str_contains((string) $page->meta_description, 'sedang disiapkan')) {
                $seoDescription = 'Profil pimpinan Poltekkes Kemenkes Banten yang memuat nama, jabatan, riwayat pendidikan, dan riwayat pekerjaan.';
            }
            $excerpt = mb_strtolower((string) $page->excerpt);
            if (blank($page->excerpt) || str_contains($excerpt, 'sedang disiapkan') || ! str_contains($excerpt, 'pendidikan')) {
                $page->excerpt = 'Profil pimpinan Poltekkes Kemenkes Banten beserta riwayat pendidikan dan riwayat pekerjaan.';
            }
        }

        if ($slug === LhkpnDocument::PAGE_SLUG) {
            $seoTitle = 'LHKPN Pimpinan Tertinggi';
            $seoDescription = 'Laporan Harta Kekayaan Penyelenggara Negara (LHKPN) pimpinan dan pejabat terkait Poltekkes Kemenkes Banten berdasarkan dokumen resmi yang tersedia.';
            $page->excerpt = $seoDescription;
        }

        if ($slug === InformationDesk::SLUG) {
            if (blank($page->meta_title)) {
                $seoTitle = InformationDesk::TITLE;
            }
            if (blank($page->meta_description)) {
                $seoDescription = InformationDesk::META_DESCRIPTION;
            }
            if (blank($page->excerpt)) {
                $page->excerpt = InformationDesk::EXCERPT;
            }
        }

        $isProfil = $this->isProfilSlug($slug);
        $isDiumumkanBerkala = $this->isDiumumkanBerkalaSlug($slug);
        $isSopLayanan = $this->isSopLayananSlug($slug);
        $isInformationDesk = $slug === InformationDesk::SLUG;

        // Academic Calendar data
        $academicCalendars = collect();
        $selectedCalendar = null;
        $selectedYear = null;

        if ($slug === 'kalender-akademik') {
            $academicCalendars = AcademicCalendar::query()
                ->published()
                ->yearOrdered('desc')
                ->get();

            $requestedYear = $request->integer('tahun');

            if ($requestedYear > 0) {
                $selectedYear = $requestedYear;
                $selectedCalendar = $academicCalendars->firstWhere('year', $requestedYear);
            } elseif ($academicCalendars->isNotEmpty()) {
                $selectedCalendar = $academicCalendars->first();
                $selectedYear = $selectedCalendar->year;
            }

            // Update SEO for specific year
            if ($selectedCalendar) {
                $seoTitle = 'Kalender Akademik '.$selectedCalendar->year.' | '.config('ppid.name');
            }
        }

        return view('pages.show', [
            'page' => $page,
            'title' => $page->title,
            'seoTitle' => $seoTitle,
            'description' => $seoDescription,
            'excerpt' => $page->excerpt,
            'body' => HtmlSanitizer::clean($page->body),
            'publicAttachments' => $page->attachments()->with('media')->where('visibility', 'public')->orderBy('sort_order')->get(),
            'serviceSummaries' => $slug === 'ringkasan-layanan-informasi-publik'
                ? ServiceSummaryReport::publishedRows()
                : collect(),
            'serviceReport' => $slug === ServiceSummaryReport::SLUG
                ? ServiceSummaryReport::fromRequest($request)
                : null,
            'leadershipProfiles' => $slug === 'profil-pimpinan-satuan-kerja'
                ? LeadershipProfile::query()->with('photo')->published()->orderBy('sort_order')->orderBy('name')->get()
                : collect(),
            'academicCalendars' => $academicCalendars,
            'selectedCalendar' => $selectedCalendar,
            'selectedYear' => $selectedYear,
            'sopDocuments' => $slug === SopLayananDocuments::SLUG ? SopLayananDocuments::all() : [],
            'lhkpnDocuments' => $slug === LhkpnDocument::PAGE_SLUG ? $this->lhkpnDocuments() : collect(),
            'label' => $isInformationDesk
                ? 'Informasi'
                : ($isProfil
                    ? 'Profil PPID'
                    : ($isDiumumkanBerkala ? 'Diumumkan Berkala' : ($isSopLayanan ? 'SOP & Layanan' : null))),
            'breadcrumbs' => array_values(array_filter([
                ['label' => 'Beranda', 'url' => route('home')],
                $isProfil ? ['label' => 'Profil', 'url' => null] : null,
                $isDiumumkanBerkala ? ['label' => 'Diumumkan Berkala', 'url' => null] : null,
                $isSopLayanan ? ['label' => 'SOP & Layanan', 'url' => null] : null,
                $isInformationDesk ? ['label' => 'Informasi', 'url' => null] : null,
                ['label' => $this->breadcrumbLabel($slug, $page->title), 'url' => null],
            ])),
        ]);
    }

    private function lhkpnDocuments()
    {
        if (! Schema::hasTable('lhkpn_documents')) {
            return collect();
        }

        return LhkpnDocument::query()->with('mediaAsset')->published()->ordered()->get();
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

    private function isDiumumkanBerkalaSlug(string $slug): bool
    {
        $menu = collect(config('ppid.navigation', []))
            ->firstWhere('label', 'Diumumkan Berkala');

        $slugs = collect($menu['children'] ?? [])
            ->map(fn (array $child) => $child['params']['slug'] ?? null)
            ->filter()
            ->all();

        return in_array($slug, $slugs, true);
    }

    private function isSopLayananSlug(string $slug): bool
    {
        $menu = collect(config('ppid.navigation', []))
            ->firstWhere('label', 'SOP & Layanan');

        $slugs = collect($menu['children'] ?? [])
            ->map(fn (array $child) => $child['params']['slug'] ?? null)
            ->filter()
            ->all();

        return in_array($slug, $slugs, true);
    }

    private function breadcrumbLabel(string $slug, string $title): string
    {
        return match ($slug) {
            'alamat-kantor-satuan-kerja' => 'Alamat Kantor Lengkap Satuan Kerja',
            'visi-misi-satuan-kerja' => 'Visi dan Misi Satuan Kerja',
            'tugas-fungsi-satuan-kerja' => 'Tugas Fungsi Satuan Kerja',
            'struktur-organisasi-satuan-kerja' => 'Struktur Organisasi Satuan Kerja',
            'profil-pimpinan-satuan-kerja' => 'Profil Pimpinan Satuan Kerja',
            'lhkpn-pimpinan-tertinggi' => 'LHKPN Pimpinan Tertinggi',
            'program-kegiatan-prioritas-2026' => 'Program atau Kegiatan Prioritas',
            'kebijakan-mengikat-bagi-publik' => 'Kebijakan Mengikat bagi Publik',
            'kalender-akademik' => 'Kalender Akademik',
            'tata-cara-pengaduan-penyalahgunaan-wewenang' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang',
            'ringkasan-laporan-layanan-informasi' => 'Ringkasan Laporan Layanan Informasi',
            'standar-operasional-prosedur-layanan-informasi-publik' => 'Standar Operasional Prosedur Layanan Informasi Publik',
            InformationDesk::SLUG => InformationDesk::TITLE,
            'profil-singkat-ppid' => 'Profil Singkat PPID',
            'dasar-hukum-sk-ppid' => 'Dasar Hukum dan SK PPID',
            'visi-dan-misi' => 'Visi dan Misi',
            'tugas-fungsi-wewenang' => 'Tugas, Fungsi, dan Wewenang PPID',
            'struktur-organisasi' => 'Struktur Organisasi PPID',
            default => $this->shortTitle($title),
        };
    }

    private function shortTitle(string $title): string
    {
        return match (true) {
            str_contains($title, 'Profil Singkat') => 'Profil Singkat PPID',
            str_contains($title, 'Dasar Hukum') => 'Dasar Hukum dan SK PPID',
            str_contains($title, 'Visi dan Misi') => 'Visi dan Misi',
            str_contains($title, 'Tugas, Fungsi') => 'Tugas, Fungsi, dan Wewenang PPID',
            str_contains($title, 'Struktur Organisasi') => 'Struktur Organisasi PPID',
            default => $title,
        };
    }
}
