@extends('admin.layouts.app')
@section('title', 'Ringkasan Layanan Informasi')
@section('content')
<h1 class="font-heading mb-2 text-2xl font-bold text-navy">Ringkasan Layanan Informasi Publik</h1>
<p class="mb-6 max-w-3xl text-sm text-muted">Isi hanya dari register resmi. Jangan memasukkan NIK, alamat, nomor telepon, atau email pemohon. Halaman publik membaca angka ini pada slug ringkasan-layanan-informasi-publik.</p>

<form method="POST" action="{{ route('admin.service-summaries.store') }}" class="mb-8 grid gap-3 rounded-xl border border-line bg-white p-5 md:grid-cols-2">
    @csrf
    <label class="text-sm">Tahun<input type="number" name="year" required class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm">Diterima<input type="number" min="0" name="total_received" class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm">Dikabulkan seluruhnya<input type="number" min="0" name="fulfilled_full" class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm">Dikabulkan sebagian<input type="number" min="0" name="fulfilled_partial" class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm">Ditolak<input type="number" min="0" name="rejected" class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm">Rata-rata hari<input type="number" min="0" step="0.01" name="average_response_days" class="mt-1 w-full rounded-md border border-line px-3 py-2"></label>
    <label class="text-sm md:col-span-2">Uraian waktu<textarea name="response_time_description" rows="3" class="mt-1 w-full rounded-md border border-line px-3 py-2"></textarea></label>
    <label class="text-sm md:col-span-2">Alasan penolakan<textarea name="rejection_reasons" rows="3" class="mt-1 w-full rounded-md border border-line px-3 py-2"></textarea></label>
    <label class="text-sm md:col-span-2">Catatan<textarea name="notes" rows="3" class="mt-1 w-full rounded-md border border-line px-3 py-2"></textarea></label>
    <button class="w-fit rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan tahun</button>
</form>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Tahun</th>
                <th class="px-4 py-3">Diterima</th>
                <th class="px-4 py-3">Penuh</th>
                <th class="px-4 py-3">Sebagian</th>
                <th class="px-4 py-3">Ditolak</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($summaries as $row)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3">{{ $row->year }}</td>
                    <td class="px-4 py-3">{{ $row->total_received ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->fulfilled_full ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->fulfilled_partial ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $row->rejected ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-muted">Belum ada angka. Jangan mengarang data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
