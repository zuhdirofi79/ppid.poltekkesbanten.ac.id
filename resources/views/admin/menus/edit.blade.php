@extends('admin.layouts.app')
@section('title', 'Menu Navigasi')
@section('content')
<h1 class="font-heading mb-2 text-2xl font-bold text-navy">Menu {{ $location === 'footer' ? 'Footer' : 'Utama' }}</h1>
<p class="mb-4 text-sm text-muted">Navbar publik memakai konfigurasi sampai Anda menerapkan menu database. Menu database di server mungkin lebih lama daripada menu yang sedang tampil.</p>
<div class="mb-4 flex gap-2 text-sm">
    <a href="{{ route('admin.menus.edit', ['location' => 'primary']) }}" class="rounded-md px-3 py-2 {{ $location === 'primary' ? 'bg-navy text-white' : 'bg-white text-navy' }}">Menu utama</a>
    <a href="{{ route('admin.menus.edit', ['location' => 'footer']) }}" class="rounded-md px-3 py-2 {{ $location === 'footer' ? 'bg-navy text-white' : 'bg-white text-navy' }}">Menu footer</a>
</div>
<form method="POST" action="{{ route('admin.menus.source') }}" class="mb-6 space-y-3 rounded-xl border border-line bg-white p-4 text-sm">
    @csrf
    @method('PUT')
    <p class="font-semibold text-navy">Sumber navbar publik: {{ $source === 'database' ? 'database' : 'konfigurasi' }}</p>
    <label class="mr-4"><input type="radio" name="public_source" value="config" @checked($source !== 'database')> Tetap konfigurasi</label>
    <label><input type="radio" name="public_source" value="database" @checked($source === 'database')> Pakai menu database</label>
    <label class="flex items-start gap-2"><input type="checkbox" name="confirm_database_nav" value="1"> Saya paham menu database akan menggantikan navbar config pada desktop dan mobile.</label>
    <button class="rounded-md bg-navy px-4 py-2 font-semibold text-white">Simpan sumber</button>
</form>

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
                        <form action="{{ route('admin.menus.items.move', $item) }}" method="POST" class="inline">@csrf<button name="direction" value="up">Naik</button></form>
                        <form action="{{ route('admin.menus.items.move', $item) }}" method="POST" class="inline">@csrf<button name="direction" value="down">Turun</button></form>
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
    <input type="hidden" name="location" value="{{ $location }}">
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
