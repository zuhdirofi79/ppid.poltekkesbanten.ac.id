@php
    $link = function (string $route, string $label, ?string $ability = null, string $pattern = null) {
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

        return '<a href="'.e($href).'" class="'.$class.'">'.e($label).'</a>';
    };
@endphp

<div class="border-b border-white/10 px-5 py-5">
    <p class="font-heading text-sm font-bold">Admin PPID</p>
    <p class="text-xs text-white/70">Poltekkes Kemenkes Banten</p>
</div>
<nav class="space-y-1 p-3 text-sm" aria-label="Menu admin">
    {!! $link('admin.dashboard', 'Dashboard') !!}
    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">Konten</p>
    {!! $link('admin.pages.index', 'Halaman', 'manage-pages', 'admin.pages.*') !!}
    {!! $link('admin.posts.index', 'Berita', 'manage-posts', 'admin.posts.*') !!}
    {!! $link('admin.announcements.index', 'Pengumuman', 'manage-announcements', 'admin.announcements.*') !!}
    {!! $link('admin.documents.index', 'Dokumen', 'manage-documents', 'admin.documents.*') !!}
    {!! $link('admin.faqs.index', 'FAQ', 'manage-faqs', 'admin.faqs.*') !!}
    {!! $link('admin.units.index', 'Unit PPID', 'manage-units', 'admin.units.*') !!}
    {!! $link('admin.services.index', 'Layanan', 'manage-services', 'admin.services.*') !!}
    <p class="px-3 pt-4 text-[10px] font-bold uppercase tracking-wider text-white/40">Sistem</p>
    {!! $link('admin.menus.edit', 'Menu', 'manage-menus', 'admin.menus.*') !!}
    {!! $link('admin.settings.edit', 'Pengaturan', 'manage-settings', 'admin.settings.*') !!}
    {!! $link('admin.users.index', 'Pengguna', 'manage-users', 'admin.users.*') !!}
</nav>
