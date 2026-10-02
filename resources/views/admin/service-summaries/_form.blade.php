@csrf
@php
    $status = old('status', $summary->exists && $summary->is_published ? 'published' : 'draft');
    $breakdown = old('rejection_reason_breakdown', $summary->rejection_reason_breakdown ?? []);
    if (! is_array($breakdown) || $breakdown === []) {
        $breakdown = [['reason' => '', 'count' => '']];
    }
    $periods = old('period_breakdown', $summary->period_breakdown ?? []);
    if (! is_array($periods) || $periods === []) {
        $periods = [['label' => '', 'received' => '', 'provided' => '', 'rejected' => '', 'in_progress' => '']];
    }
    $types = old('information_type_breakdown', $summary->information_type_breakdown ?? []);
    if (! is_array($types) || $types === []) {
        $types = [['label' => '', 'count' => '']];
    }
    $analysisText = old('analysis_text');
    if ($analysisText === null) {
        $paragraphs = $summary->analysis_paragraphs ?? [];
        $analysisText = is_array($paragraphs) ? implode("\n\n", $paragraphs) : '';
    }
    $improvementText = old('improvement_text');
    if ($improvementText === null) {
        $points = $summary->improvement_points ?? [];
        $improvementText = is_array($points) ? implode("\n", $points) : '';
    }
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="year" class="mb-1 block text-sm font-medium text-navy">Tahun Laporan <span class="text-red-600">*</span></label>
        <input id="year" name="year" type="number" min="2000" max="2100" required value="{{ old('year', $summary->year) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        @error('year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-navy">Status Publikasi <span class="text-red-600">*</span></label>
        <select id="status" name="status" class="w-full rounded-md border border-line px-3 py-2 text-sm">
            <option value="draft" @selected($status === 'draft')>Draft</option>
            <option value="published" @selected($status === 'published')>Published</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="total_received" class="mb-1 block text-sm font-medium text-navy">Jumlah Permohonan Informasi Publik Diterima</label>
        <input id="total_received" name="total_received" type="number" min="0" value="{{ old('total_received', $summary->total_received) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        @error('total_received')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="fulfilled_full" class="mb-1 block text-sm font-medium text-navy">Jumlah Dikabulkan Seluruhnya</label>
        <p class="mb-1 text-xs text-muted">Kosongkan jika laporan tidak memisahkan dikabulkan seluruhnya dan sebagian.</p>
        <input id="fulfilled_full" name="fulfilled_full" type="number" min="0" value="{{ old('fulfilled_full', $summary->fulfilled_full) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div>
        <label for="fulfilled_partial" class="mb-1 block text-sm font-medium text-navy">Jumlah Dikabulkan Sebagian</label>
        <input id="fulfilled_partial" name="fulfilled_partial" type="number" min="0" value="{{ old('fulfilled_partial', $summary->fulfilled_partial) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div>
        <label for="provided_total" class="mb-1 block text-sm font-medium text-navy">Jumlah Informasi Diberikan</label>
        <input id="provided_total" name="provided_total" type="number" min="0" value="{{ old('provided_total', $summary->provided_total) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        <p class="mt-1 text-xs text-muted">Isi bila laporan menyebut jumlah yang diberikan/dipenuhi tanpa memisahkan seluruhnya dan sebagian. Tingkat pemenuhan dihitung otomatis di halaman publik.</p>
        @error('provided_total')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="in_progress" class="mb-1 block text-sm font-medium text-navy">Dalam Proses</label>
        <input id="in_progress" name="in_progress" type="number" min="0" value="{{ old('in_progress', $summary->in_progress) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
        <p class="mt-1 text-xs text-muted">Isi 0 hanya bila laporan menyatakan tidak ada permohonan yang masih dalam proses.</p>
    </div>
    <div>
        <label for="rejected" class="mb-1 block text-sm font-medium text-navy">Jumlah Ditolak</label>
        <input id="rejected" name="rejected" type="number" min="0" value="{{ old('rejected', $summary->rejected) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label for="average_response_days" class="mb-1 block text-sm font-medium text-navy">Rata-rata Waktu Penyelesaian (Hari Kerja)</label>
        <input id="average_response_days" name="average_response_days" type="number" min="0" step="0.01" value="{{ old('average_response_days', $summary->average_response_days) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label for="response_time_description" class="mb-1 block text-sm font-medium text-navy">Deskripsi Waktu Pelayanan</label>
        <textarea id="response_time_description" name="response_time_description" rows="3" class="w-full rounded-md border border-line px-3 py-2 text-sm">{{ old('response_time_description', $summary->response_time_description) }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label for="rejection_reasons" class="mb-1 block text-sm font-medium text-navy">Alasan Penolakan</label>
        <textarea id="rejection_reasons" name="rejection_reasons" rows="4" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="Satu alasan tiap baris, bila ingin ditampilkan sebagai daftar.">{{ old('rejection_reasons', $summary->rejection_reasons) }}</textarea>
    </div>
    <div class="sm:col-span-2" x-data="{ rows: {{ \Illuminate\Support\Js::from($breakdown) }} }">
        <p class="mb-2 text-sm font-medium text-navy">Rincian jumlah per alasan</p>
        <template x-for="(row, index) in rows" :key="index">
            <div class="mb-2 grid gap-2 sm:grid-cols-[1fr_8rem_auto]">
                <input type="text" :name="`rejection_reason_breakdown[${index}][reason]`" x-model="row.reason" maxlength="200" placeholder="Alasan" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`rejection_reason_breakdown[${index}][count]`" x-model="row.count" placeholder="Jumlah" class="rounded-md border border-line px-3 py-2 text-sm">
                <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm" @click="rows.splice(index, 1)">Hapus</button>
            </div>
        </template>
        <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm font-semibold text-navy" @click="rows.push({ reason: '', count: '' })">Tambah Alasan</button>
        @error('rejection_reason_breakdown')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        @error('rejection_reason_breakdown.*.reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        @error('rejection_reason_breakdown.*.count')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2" x-data="{ rows: {{ \Illuminate\Support\Js::from($periods) }} }">
        <p class="mb-2 text-sm font-medium text-navy">Rekap per periode</p>
        <template x-for="(row, index) in rows" :key="index">
            <div class="mb-2 grid gap-2 lg:grid-cols-[1.2fr_repeat(4,6rem)_auto]">
                <input type="text" :name="`period_breakdown[${index}][label]`" x-model="row.label" maxlength="80" placeholder="Periode" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`period_breakdown[${index}][received]`" x-model="row.received" placeholder="Masuk" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`period_breakdown[${index}][provided]`" x-model="row.provided" placeholder="Diberikan" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`period_breakdown[${index}][rejected]`" x-model="row.rejected" placeholder="Ditolak" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`period_breakdown[${index}][in_progress]`" x-model="row.in_progress" placeholder="Proses" class="rounded-md border border-line px-3 py-2 text-sm">
                <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm" @click="rows.splice(index, 1)">Hapus</button>
            </div>
        </template>
        <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm font-semibold text-navy" @click="rows.push({ label: '', received: '', provided: '', rejected: '', in_progress: '' })">Tambah Periode</button>
        @error('period_breakdown')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2" x-data="{ rows: {{ \Illuminate\Support\Js::from($types) }} }">
        <p class="mb-2 text-sm font-medium text-navy">Jenis informasi yang diminta</p>
        <template x-for="(row, index) in rows" :key="index">
            <div class="mb-2 grid gap-2 sm:grid-cols-[1fr_8rem_auto]">
                <input type="text" :name="`information_type_breakdown[${index}][label]`" x-model="row.label" maxlength="200" placeholder="Jenis informasi" class="rounded-md border border-line px-3 py-2 text-sm">
                <input type="number" min="0" :name="`information_type_breakdown[${index}][count]`" x-model="row.count" placeholder="Jumlah" class="rounded-md border border-line px-3 py-2 text-sm">
                <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm" @click="rows.splice(index, 1)">Hapus</button>
            </div>
        </template>
        <button type="button" class="min-h-11 rounded-md border border-line px-3 text-sm font-semibold text-navy" @click="rows.push({ label: '', count: '' })">Tambah Jenis</button>
        @error('information_type_breakdown')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label for="analysis_text" class="mb-1 block text-sm font-medium text-navy">Analisis singkat</label>
        <textarea id="analysis_text" name="analysis_text" rows="5" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="Satu paragraf, pisahkan dengan baris kosong.">{{ $analysisText }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label for="improvement_text" class="mb-1 block text-sm font-medium text-navy">Upaya peningkatan layanan</label>
        <textarea id="improvement_text" name="improvement_text" rows="5" class="w-full rounded-md border border-line px-3 py-2 text-sm" placeholder="Satu upaya tiap baris.">{{ $improvementText }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label for="source_document_path" class="mb-1 block text-sm font-medium text-navy">Path dokumen sumber</label>
        <input id="source_document_path" name="source_document_path" type="text" maxlength="255" value="{{ old('source_document_path', $summary->source_document_path) }}" placeholder="docs/nama-laporan.docx" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label for="notes" class="mb-1 block text-sm font-medium text-navy">Catatan</label>
        <textarea id="notes" name="notes" rows="3" class="w-full rounded-md border border-line px-3 py-2 text-sm">{{ old('notes', $summary->notes) }}</textarea>
    </div>
</div>

<button class="mt-4 min-h-11 rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
