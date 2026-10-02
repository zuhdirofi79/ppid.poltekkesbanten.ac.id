@extends('admin.layouts.app')
@section('title', $saq->code)
@section('content')
<p class="mb-2 text-sm"><a href="{{ route('admin.saq.index') }}" class="text-navy hover:underline">Kembali ke indikator</a></p>
<h1 class="font-heading text-2xl font-bold text-navy">{{ $saq->code }}</h1>
<p class="mt-1 text-muted">{{ $saq->section }} · {{ $saq->subsection }}</p>
<p class="mt-4 max-w-3xl text-navy">{{ $saq->question }}</p>
<p class="mt-2 max-w-3xl text-sm text-muted">{{ $saq->evidence_requirement }}</p>
<p class="mt-2 text-sm">Rute yang disarankan: <code>{{ $saq->recommended_public_route }}</code></p>
<p class="mt-2 text-sm">Status hitungan: <strong>{{ $assessment['status'] }}</strong> · bukti {{ $assessment['evidence_count'] }} · publik {{ $assessment['public_state'] }}</p>

@if ($assessment['public_url'])
    <label class="mt-4 block text-sm font-medium text-navy">URL publik untuk SAQ</label>
    <input readonly value="{{ $assessment['public_url'] }}" class="mt-1 w-full max-w-xl rounded-md border border-line px-3 py-2 text-sm" onclick="this.select()">
@endif

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('admin.saq.update', $saq) }}" class="space-y-4 rounded-xl border border-line bg-white p-5">
        @csrf
        @method('PUT')
        <h2 class="font-heading text-lg font-bold text-navy">Jawaban</h2>
        <label class="block text-sm">Jawaban
            <select name="answer" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                @foreach (['not_set' => 'Belum diisi', 'yes' => 'Ya', 'no' => 'Tidak'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('answer', $saq->answer) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">Status
            <select name="status" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                @foreach (['missing' => 'Belum diisi', 'draft' => 'Draft', 'needs_review' => 'Perlu review', 'complete' => 'Lengkap', 'published' => 'Terbit'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $assessment['status']) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">Catatan
            <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border border-line px-3 py-2">{{ old('notes', $saq->notes) }}</textarea>
        </label>
        <label class="flex items-start gap-2 text-sm">
            <input type="checkbox" name="redaction_confirmed" value="1" class="mt-1">
            <span>Pastikan dokumen tidak memuat data pribadi atau informasi yang dikecualikan.</span>
        </label>
        @error('redaction_confirmed')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        @error('status')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    </form>

    <form method="POST" action="{{ route('admin.saq.evidences.store', $saq) }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-line bg-white p-5">
        @csrf
        <h2 class="font-heading text-lg font-bold text-navy">Tambah data dukung</h2>
        <label class="block text-sm">Jenis
            <select name="evidence_type" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                <option value="page">Halaman</option>
                <option value="public_document">Dokumen publik</option>
                <option value="media">Berkas media</option>
                <option value="screenshot">Tangkapan layar</option>
                <option value="external_url">URL</option>
            </select>
        </label>
        <label class="block text-sm">Halaman
            <select name="page_id" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                <option value="">—</option>
                @foreach ($pages as $page)
                    <option value="{{ $page->id }}">{{ $page->title }} ({{ $page->status }})</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">Dokumen
            <select name="public_document_id" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                <option value="">—</option>
                @foreach ($documents as $document)
                    <option value="{{ $document->id }}">{{ $document->title }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm">URL
            <input name="external_url" class="mt-1 w-full rounded-md border border-line px-3 py-2">
        </label>
        <label class="block text-sm">Judul
            <input name="title" class="mt-1 w-full rounded-md border border-line px-3 py-2">
        </label>
        <label class="block text-sm">Berkas PNG, JPG, PDF, DOC, atau DOCX
            <input type="file" name="file" class="mt-1 block w-full text-sm">
        </label>
        <label class="block text-sm">Visibilitas
            <select name="visibility" class="mt-1 w-full rounded-md border border-line px-3 py-2">
                <option value="public">Publik</option>
                <option value="internal">Internal</option>
            </select>
        </label>
        <p class="text-xs text-muted">Untuk register, kontrak, notulensi, dan data pegawai, simpan sebagai internal sampai redaksi selesai.</p>
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Lampirkan</button>
    </form>
</div>

<h2 class="font-heading mb-3 mt-8 text-lg font-bold text-navy">Daftar data dukung</h2>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Judul</th><th class="px-4 py-3">Visibilitas</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($saq->evidences as $evidence)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3">{{ $evidence->evidence_type }}</td>
                    <td class="px-4 py-3">{{ $evidence->title ?: $evidence->page?->title ?: $evidence->document?->title ?: $evidence->external_url ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $evidence->visibility }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.saq.evidences.destroy', [$saq, $evidence]) }}" onsubmit="return confirm('Lepas data dukung ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Lepas</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada data dukung.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
