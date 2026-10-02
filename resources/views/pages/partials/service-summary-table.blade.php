@if ($rows->isNotEmpty())
    <section class="mx-auto max-w-3xl px-4 pb-10">
        <h2 class="font-heading text-xl font-bold text-navy">Ringkasan agregat</h2>
        <p class="mt-2 text-sm text-muted">Angka berikut agregat. Identitas pemohon tidak ditampilkan.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="py-2 pr-4">Tahun</th>
                        <th class="py-2 pr-4">Diterima</th>
                        <th class="py-2 pr-4">Dikabulkan seluruhnya</th>
                        <th class="py-2 pr-4">Dikabulkan sebagian</th>
                        <th class="py-2 pr-4">Ditolak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="py-2 pr-4">{{ $row->year }}</td>
                            <td class="py-2 pr-4">{{ $row->total_received ?? '—' }}</td>
                            <td class="py-2 pr-4">{{ $row->fulfilled_full ?? '—' }}</td>
                            <td class="py-2 pr-4">{{ $row->fulfilled_partial ?? '—' }}</td>
                            <td class="py-2 pr-4">{{ $row->rejected ?? '—' }}</td>
                        </tr>
                        @if ($row->rejection_reasons)
                            <tr>
                                <td colspan="5" class="pb-3 text-muted">Alasan penolakan: {{ $row->rejection_reasons }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
