@extends('admin.layouts.app')
@section('title', 'Menu Navigasi')
@section('content')
<h1 class="font-heading mb-2 text-2xl font-bold text-navy">Menu Navigasi Utama</h1>
<p class="mb-6 text-sm text-muted">Kelola item menu. Header publik masih dapat memakai config; menu DB siap untuk FASE berikutnya.</p>

<div class="mb-8 overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Label</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Target</th><th class="px-4 py-3">Urutan</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $item->label }}</td>
                    <td class="px-4 py-3">{{ $item->type }}</td>
                    <td class="px-4 py-3 text-muted">{{ $item->route_name ?? $item->url ?? $item->page_slug ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $item->sort_order }}</td>
                    <td class="px-4 py-3 text-right">
                        <form action="{{ route('admin.menus.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item beserta submenu?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                    </td>
                </tr>
                @foreach ($item->children as $child)
                    <tr class="border-b border-line/40 bg-surface/40">
                        <td class="px-4 py-2 pl-10 text-navy">↳ {{ $child->label }}</td>
                        <td class="px-4 py-2">{{ $child->type }}</td>
                        <td class="px-4 py-2 text-muted">{{ $child->route_name ?? $child->url ?? $child->page_slug }}</td>
                        <td class="px-4 py-2">{{ $child->sort_order }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('admin.menus.items.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-muted">Belum ada item. Jalankan seeder atau tambah di bawah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<form method="POST" action="{{ route('admin.menus.items.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    <h2 class="font-heading text-lg font-bold text-navy">Tambah item</h2>
    <x-admin.input label="Label" name="label" required />
    <x-admin.select label="Parent (opsional)" name="parent_id" placeholder="— Root —" :options="$items->pluck('label','id')->all()" />
    <x-admin.select label="Tipe" name="type" value="route" :options="['route'=>'Route','url'=>'URL','page'=>'Halaman (slug)']" required />
    <x-admin.input label="Nama route" name="route_name" help="Contoh: home, posts.index" />
    <x-admin.input label="URL" name="url" />
    <x-admin.input label="Slug halaman" name="page_slug" />
    <x-admin.input label="Urutan" name="sort_order" type="number" value="0" />
    <x-admin.checkbox name="is_active" label="Aktif" :checked="true" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah item</button>
</form>
@endsection
