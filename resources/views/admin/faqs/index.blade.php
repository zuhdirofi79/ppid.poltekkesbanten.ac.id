@extends('admin.layouts.app')
@section('title', 'FAQ')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">FAQ</h1>
    <a href="{{ route('admin.faqs.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah FAQ</a>
</div>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Pertanyaan</th><th class="px-4 py-3">Aktif</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($faqs as $faq)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $faq->question }}</td>
                    <td class="px-4 py-3">{{ $faq->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-8 text-center text-muted">Belum ada FAQ.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $faqs->links() }}</div>
@endsection
