@csrf
@php
    $status = old('status', $summary->exists && $summary->is_published ? 'published' : 'draft');
    $breakdown = old('rejection_reason_breakdown', $summary->rejection_reason_breakdown ?? []);
    if (! is_array($breakdown) || $breakdown === []) {
        $breakdown = [['reason' => '', 'count' => '']];
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
        <input id="fulfilled_full" name="fulfilled_full" type="number" min="0" value="{{ old('fulfilled_full', $summary->fulfilled_full) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
    </div>
    <div>
        <label for="fulfilled_partial" class="mb-1 block text-sm font-medium text-navy">Jumlah Dikabulkan Sebagian</label>
        <input id="fulfilled_partial" name="fulfilled_partial" type="number" min="0" value="{{ old('fulfilled_partial', $summary->fulfilled_partial) }}" class="w-full rounded-md border border-line px-3 py-2 text-sm">
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
    <div class="sm:col-span-2">
        <label for="notes" class="mb-1 block text-sm font-medium text-navy">Catatan</label>
        <textarea id="notes" name="notes" rows="3" class="w-full rounded-md border border-line px-3 py-2 text-sm">{{ old('notes', $summary->notes) }}</textarea>
    </div>
</div>

<button class="mt-4 min-h-11 rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
