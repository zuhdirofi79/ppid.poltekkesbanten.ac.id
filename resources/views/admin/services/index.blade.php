@extends('admin.layouts.app')
@section('title', 'Layanan')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">Layanan / Formulir</h1>
    <a href="{{ route('admin.services.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah layanan</a>
</div>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Judul</th><th class="px-4 py-3">Eksternal</th><th class="px-4 py-3">Featured</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $service->title }}</td>
                    <td class="px-4 py-3 truncate max-w-xs">{{ $service->external_url ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $service->is_featured ? 'Ya' : 'Tidak' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.services.edit', $service) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada layanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $services->links() }}</div>
@endsection
