@extends('admin.layouts.app')
@section('title', 'Tambah LHKPN')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.lhkpn.index') }}" class="text-muted hover:text-navy">&larr;</a>
    <h1 class="font-heading text-2xl font-bold text-navy">Tambah LHKPN</h1>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.lhkpn.store') }}" class="max-w-2xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf

    <div>
        <label for="position_key" class="mb-1 block text-sm font-medium text-navy">Kunci jabatan</label>
        <input id="position_key" name="position_key" value="{{ old('position_key') }}" required pattern="[a-z0-9]+(?:-[a-z0-9]+)*" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="contoh-jabatan">
        <p class="mt-1 text-xs text-muted">Kunci ini tetap dipakai sebagai identitas record. Menyunting nama tidak mengubah kunci.</p>
    </div>

    <div>
        <label for="person_name" class="mb-1 block text-sm font-medium text-navy">Nama</label>
        <input id="person_name" name="person_name" value="{{ old('person_name') }}" required class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>

    <div>
        <label for="position" class="mb-1 block text-sm font-medium text-navy">Jabatan</label>
        <input id="position" name="position" value="{{ old('position') }}" required class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="group_key" class="mb-1 block text-sm font-medium text-navy">Kelompok</label>
            <select id="group_key" name="group_key" class="w-full rounded-md border border-line px-3 py-2 text-sm">
                <option value="pimpinan" @selected(old('group_key', $document->group_key) === 'pimpinan')>Pimpinan Direktorat</option>
                <option value="terkait" @selected(old('group_key', $document->group_key) === 'terkait')>Pejabat Terkait</option>
            </select>
        </div>
        <div>
            <label for="report_year" class="mb-1 block text-sm font-medium text-navy">Tahun/Periode</label>
            <input id="report_year" name="report_year" type="number" min="2000" max="2100" value="{{ old('report_year', $document->report_year) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="document_label" class="mb-1 block text-sm font-medium text-navy">Keterangan jenis dokumen</label>
            <input id="document_label" name="document_label" value="{{ old('document_label') }}" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="Tanda Terima / Dokumen e-LHKPN KPK">
        </div>
        <div>
            <label for="sort_order" class="mb-1 block text-sm font-medium text-navy">Urutan</label>
            <input id="sort_order" name="sort_order" type="number" min="0" max="9999" value="{{ old('sort_order', $document->sort_order) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label for="public_asset_path" class="mb-1 block text-sm font-medium text-navy">Path PDF yang sudah ada di situs</label>
        <input id="public_asset_path" name="public_asset_path" value="{{ old('public_asset_path') }}" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="docs/LHKPN_verif_KPK/NAMA_FILE.pdf">
        <p class="mt-1 text-xs text-muted">Kosongkan jika PDF akan diunggah. Jangan membuat berkas kosong.</p>
    </div>

    <div>
        <label for="external_url" class="mb-1 block text-sm font-medium text-navy">URL eksternal</label>
        <input id="external_url" name="external_url" type="url" value="{{ old('external_url') }}" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="https://">
    </div>

    <div>
        <label for="notes" class="mb-1 block text-sm font-medium text-navy">Catatan internal</label>
        <textarea id="notes" name="notes" rows="3" class="w-full rounded-md border border-line px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        <p class="mt-1 text-xs text-muted">Catatan ini tidak ditampilkan di kartu publik.</p>
    </div>

    <label class="flex items-center gap-2 text-sm text-navy">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $document->is_published))>
        Tayangkan di halaman publik
    </label>

    <div id="unggah" class="rounded-lg border border-line bg-surface p-4">
        <label for="file" class="mb-1 block text-sm font-medium text-navy">Upload Dokumen</label>
        <input id="file" name="file" type="file" accept="application/pdf,.pdf" class="block w-full text-sm">
        <p class="mt-1 text-xs text-muted">Hanya PDF, maksimal {{ (int) config('ppid.max_upload_mb', 20) }} MB.</p>
    </div>

    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
