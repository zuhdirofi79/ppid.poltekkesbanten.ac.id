@php
    $chrome = $ppidAdminChrome ?? [
        'showBell' => false,
        'isProtected' => false,
        'missingCount' => 0,
        'badgeLabel' => '',
    ];

    $link = function (string $route, string $label, ?string $ability = null, string $pattern = null, ?string $badge = null) {
        if ($ability && ! auth()->user()?->can($ability)) {
            return '';
        }
        $active = $pattern ? request()->routeIs($pattern) : request()->routeIs($route);
        $class = 'block rounded-md px-3 py-2 '.($active ? 'bg-white/10 text-gold' : 'hover:bg-white/5');
        try {
            $href = route($route);
        } catch (\Throwable) {
            return '';
        }

        $badgeHtml = $badge !== null && $badge !== '' && $badge !== '0'
            ? ' <span class="ml-1 inline-flex rounded-full bg-amber-400/90 px-1.5 py-0.5 text-[10px] font-bold text-navy-dark">'.e($badge).'</span>'
            : '';

        return '<a href="'.e($href).'" class="'.$class.'">'.e($label).$badgeHtml.'</a>';
    };

    $missingBerkala = (string) ($chrome['badgeLabel'] ?? '');
@endphp

<div class="border-b border-white/10 px-5 py-5">
    <p class="font-heading text-sm font-bold">Admin PPID</p>
    <p class="text-xs text-white/70">Poltekkes Kemenkes Banten</p>
</div>
<nav class="space-y-1 p-3 text-sm" aria-label="Menu admin">
    {!! $link('admin.dashboard', 'Dashboard') !!}
    {!! $link('admin.ppid.dashboard', 'Dashboard PPID', 'manage-documents', 'admin.ppid.dashboard') !!}

    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">Informasi Publik</p>
    {!! $link('admin.informasi-publik.berkala', 'Informasi Berkala', 'manage-documents', 'admin.informasi-publik.*') !!}
    @can('manage-documents')
        <a
            href="{{ route('admin.informasi-publik.berkala', ['status' => 'missing']) }}"
            class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.informasi-publik.berkala') && request('status') === 'missing' ? 'bg-white/10 text-gold' : 'hover:bg-white/5' }}"
        >
            Dokumen Wajib Dilengkapi
            @if ($missingBerkala !== '')
                <span class="ml-1 inline-flex rounded-full bg-amber-400/90 px-1.5 py-0.5 text-[10px] font-bold text-navy-dark">{{ $missingBerkala }}</span>
            @endif
        </a>
    @endcan

    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">SAQ Monev 2026</p>
    {!! $link('admin.saq.index', 'Semua Indikator', 'manage-pages', 'admin.saq.*') !!}
    @can('manage-pages')
        <a href="{{ route('admin.saq.index', ['filter' => 'incomplete']) }}" class="block rounded-md px-3 py-2 hover:bg-white/5">Belum Lengkap</a>
        <a href="{{ route('admin.saq.index', ['filter' => 'needs_review']) }}" class="block rounded-md px-3 py-2 hover:bg-white/5">Perlu Review</a>
        <a href="{{ route('admin.saq.index', ['filter' => 'published']) }}" class="block rounded-md px-3 py-2 hover:bg-white/5">Sudah Dipublikasikan</a>
    @endcan

    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">Konten</p>
    {!! $link('admin.pages.index', 'Halaman', 'manage-pages', 'admin.pages.*') !!}
    @can('manage-pages')
        <a href="{{ route('admin.pages.create') }}" class="block rounded-md px-3 py-2 hover:bg-white/5">Tambah Halaman</a>
        <a href="{{ route('admin.leadership-profiles.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.leadership-profiles.*') ? 'bg-white/10 text-gold' : 'hover:bg-white/5' }}">Profil Pimpinan</a>
    @endcan
    {!! $link('admin.posts.index', 'Berita', 'manage-posts', 'admin.posts.*') !!}
    {!! $link('admin.announcements.index', 'Pengumuman', 'manage-announcements', 'admin.announcements.*') !!}
    {!! $link('admin.documents.index', 'Dokumen', 'manage-documents', 'admin.documents.*') !!}
    {!! $link('admin.academic-calendars.index', 'Kalender Akademik', 'manage-documents', 'admin.academic-calendars.*') !!}
    @can('manage-pages')
        <a href="{{ route('admin.media.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.media.*') ? 'bg-white/10 text-gold' : 'hover:bg-white/5' }}">Media Library</a>
        <a href="{{ route('admin.service-summaries.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.service-summaries.*') ? 'bg-white/10 text-gold' : 'hover:bg-white/5' }}">Ringkasan Layanan</a>
    @endcan
    {!! $link('admin.faqs.index', 'FAQ', 'manage-faqs', 'admin.faqs.*') !!}
    {!! $link('admin.units.index', 'Unit PPID', 'manage-units', 'admin.units.*') !!}
    {!! $link('admin.services.index', 'Layanan', 'manage-services', 'admin.services.*') !!}
    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">Sistem</p>
    {!! $link('admin.menus.edit', 'Menu', 'manage-menus', 'admin.menus.*') !!}
    {!! $link('admin.settings.edit', 'Pengaturan', 'manage-settings', 'admin.settings.*') !!}
    {!! $link('admin.users.index', 'Pengguna', 'manage-users', 'admin.users.*') !!}
    @if ($chrome['isProtected'] ?? false)
        {{-- PPID-PROTECTED-ADMIN-MANAGEMENT-DEPLOY-20260909-A --}}
        {!! $link('admin.ppid.admins.index', 'Manajemen Admin PPID', null, 'admin.ppid.admins.*') !!}
    @endif
    {!! $link('admin.profile.edit', 'Profil Saya', null, 'admin.profile.*') !!}
</nav>
