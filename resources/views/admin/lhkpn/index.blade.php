@extends('admin.layouts.app')
@section('title', 'LHKPN Pimpinan')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">LHKPN Pimpinan</h1>
        <p class="mt-1 text-sm text-muted">Kelola dokumen LHKPN pimpinan dan pejabat terkait. Urutan mengikuti kolom urutan.</p>
    </div>
    <a href="{{ route('pages.show', 'lhkpn-pimpinan-tertinggi') }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-line px-4 py-2 text-sm font-semibold text-navy">Lihat Halaman Publik</a>
</div>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Urutan</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Jabatan</th>
                <th class="px-4 py-3">Tahun/Periode</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Dokumen</th>
                <th class="px-4 py-3">Publish</th>
                <th class="px-4 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($documents as $document)
                <tr class="border-b border-line/70 align-top">
                    <td class="px-4 py-3 font-heading text-lg font-bold text-navy">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-navy">{{ $document->person_name }}</td>
                    <td class="px-4 py-3 text-muted">{{ $document->position }}</td>
                    <td class="px-4 py-3">{{ $document->report_year ?: '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($document->requiresUpload())
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900">WAJIB DILENGKAPI</span>
                            <span class="mt-1 block text-xs text-amber-900">Dokumen belum tersedia</span>
                        @elseif ($document->isAvailable())
                            <span class="font-semibold text-green">Tersedia</span>
                        @else
                            <span class="text-muted">Tidak ditayangkan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($document->documentUrl())
                            <a href="{{ $document->documentUrl() }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-green hover:underline">Lihat berkas</a>
                            <a href="{{ $document->documentUrl() }}" download="{{ $document->downloadFilename() }}" class="mt-1 block text-xs text-navy hover:underline">Unduh</a>
                        @else
                            <span class="text-muted">Belum ada berkas</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.lhkpn.publish', $document) }}">
                            @csrf
                            <button class="text-sm font-semibold {{ $document->is_published ? 'text-green' : 'text-amber-800' }} hover:underline">
                                {{ $document->is_published ? 'Tayang' : 'Draft' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col items-start gap-2">
                            <a href="{{ route('admin.lhkpn.edit', $document) }}" class="font-semibold text-green hover:underline">Edit</a>
                            @if (! $document->isAvailable())
                                <form method="POST" action="{{ route('admin.lhkpn.upload', $document) }}" enctype="multipart/form-data" class="flex flex-col gap-2">
                                    @csrf
                                    <label class="text-xs font-semibold text-amber-900" for="file-{{ $document->id }}">Upload Dokumen</label>
                                    <input id="file-{{ $document->id }}" type="file" name="file" accept="application/pdf,.pdf" required class="max-w-[14rem] text-xs">
                                    <button class="rounded-md bg-navy px-3 py-1.5 text-xs font-semibold text-white">Upload Dokumen</button>
                                </form>
                            @else
                                <a href="{{ route('admin.lhkpn.edit', $document) }}#unggah" class="text-xs font-semibold text-navy hover:underline">Ganti Dokumen</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-muted">Belum ada data LHKPN. Jalankan seeder LhkpnDocumentSeeder.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
