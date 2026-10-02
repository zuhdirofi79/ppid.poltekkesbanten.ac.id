@extends('admin.layouts.app')
@section('title', 'Laporan Layanan Informasi')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">Ringkasan Laporan Layanan Informasi</h1>
        <p class="mt-2 max-w-3xl text-sm text-muted">Isi hanya dari register resmi. Jangan memasukkan NIK, alamat, nomor telepon, atau email pemohon. Kosongkan angka yang belum diketahui. Nol hanya dipakai bila benar-benar tidak ada kejadian.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('pages.show', 'ringkasan-laporan-layanan-informasi') }}" target="_blank" rel="noopener" class="rounded-md border border-line px-4 py-2 text-sm font-semibold text-navy">Lihat Halaman Publik</a>
        <a href="{{ route('admin.service-summaries.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah Tahun</a>
    </div>
</div>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Tahun</th>
                <th class="px-4 py-3">Diterima</th>
                <th class="px-4 py-3">Dikabulkan</th>
                <th class="px-4 py-3">Sebagian</th>
                <th class="px-4 py-3">Ditolak</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summaries as $row)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-heading text-lg font-bold text-navy">{{ $row->year }}</td>
                    <td class="px-4 py-3">{{ $row->total_received ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->fulfilled_full ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->fulfilled_partial ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->rejected ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($row->is_published)
                            <span class="inline-flex rounded-full bg-green/10 px-2 py-0.5 text-xs font-semibold text-green">Published</span>
                        @else
                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.service-summaries.edit', $row) }}" class="text-green hover:underline">Edit</a>
                            <a href="{{ route('pages.show', ['slug' => 'ringkasan-laporan-layanan-informasi', 'tahun' => $row->year]) }}" target="_blank" rel="noopener" class="text-navy hover:underline">Pratinjau</a>
                            <form method="POST" action="{{ route('admin.service-summaries.publish', $row) }}">
                                @csrf
                                <button class="text-navy hover:underline">{{ $row->is_published ? 'Jadikan Draft' : 'Terbitkan' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.service-summaries.destroy', $row) }}" onsubmit="return confirm('Hapus laporan tahun {{ $row->year }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-muted">Belum ada laporan. Jangan mengarang angka.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
