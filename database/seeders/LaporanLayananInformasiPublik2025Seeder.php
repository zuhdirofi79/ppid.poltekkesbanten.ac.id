<?php

namespace Database\Seeders;

use App\Models\InformationServiceSummary;
use Illuminate\Database\Seeder;

class LaporanLayananInformasiPublik2025Seeder extends Seeder
{
    public function run(): void
    {
        $existing = InformationServiceSummary::query()->where('year', 2025)->first();
        if ($existing && ($existing->total_received !== null || $existing->provided_total !== null)) {
            return;
        }

        $payload = [
            'year' => 2025,
            'total_received' => 35,
            'provided_total' => 33,
            'fulfilled_full' => null,
            'fulfilled_partial' => null,
            'rejected' => 2,
            'in_progress' => 0,
            'average_response_days' => null,
            'response_time_description' => "Data waktu penyelesaian individual maupun rata-rata waktu pemenuhan permohonan belum tersedia secara kuantitatif dalam Laporan Layanan Informasi Publik Tahun 2025.\n\nDokumentasi waktu penyelesaian permohonan menjadi salah satu aspek yang akan diperkuat agar kinerja kecepatan pelayanan dapat diukur dan dipublikasikan pada laporan berikutnya.",
            'rejection_reasons' => "Informasi termasuk kategori informasi yang dikecualikan sesuai Pasal 17 Undang-Undang Keterbukaan Informasi Publik.\nPermohonan tidak disertai identitas resmi pemohon.\nInformasi yang diminta belum tersedia atau belum final.",
            'rejection_reason_breakdown' => null,
            'period_breakdown' => [
                ['label' => 'Januari–Maret', 'received' => 5, 'provided' => 4, 'rejected' => 1, 'in_progress' => 0],
                ['label' => 'April–Juni', 'received' => 8, 'provided' => 8, 'rejected' => 0, 'in_progress' => 0],
                ['label' => 'Juli–September', 'received' => 12, 'provided' => 12, 'rejected' => 0, 'in_progress' => 0],
                ['label' => 'Oktober–Desember', 'received' => 10, 'provided' => 9, 'rejected' => 1, 'in_progress' => 0],
            ],
            'information_type_breakdown' => [
                ['label' => 'Data jumlah mahasiswa dan dosen', 'count' => 12],
                ['label' => 'Informasi beasiswa', 'count' => 8],
                ['label' => 'Informasi pengadaan barang/jasa', 'count' => 6],
                ['label' => 'Data kurikulum dan jadwal kuliah', 'count' => 5],
                ['label' => 'Surat Keterangan Aktif Mahasiswa', 'count' => 4],
            ],
            'analysis_paragraphs' => [
                'Pelaksanaan pelayanan informasi publik tahun 2025 menunjukkan capaian yang tinggi. Dari 35 permohonan informasi yang diterima, 33 permohonan berhasil diberikan kepada pemohon. Tingkat pemenuhan mencapai 94,3%.',
                'Jumlah permohonan tertinggi terjadi pada periode Juli–September sebanyak 12 permohonan.',
                'Data jumlah mahasiswa dan dosen merupakan jenis informasi yang paling banyak dimohonkan, yaitu sebanyak 12 permohonan.',
            ],
            'improvement_points' => [
                'Peningkatan kapasitas SDM PPID, khususnya terkait pelayanan informasi, klasifikasi informasi, dan keterbukaan informasi publik.',
                'Optimalisasi sistem teknologi informasi, termasuk pengembangan dan pemutakhiran halaman PPID pada website resmi Poltekkes Kemenkes Banten.',
                'Penguatan SOP pelayanan dan keberatan informasi.',
                'Sosialisasi secara rutin kepada mahasiswa, sivitas akademika, dan masyarakat mengenai mekanisme memperoleh informasi publik.',
                'Evaluasi pelayanan secara berkala, termasuk pemutakhiran Daftar Informasi Publik.',
                'Penguatan dokumentasi waktu penyelesaian permohonan sehingga kinerja kecepatan pelayanan dapat diukur dan dipublikasikan pada laporan berikutnya.',
            ],
            'notes' => 'Sebanyak 33 dari 35 permohonan informasi publik telah diberikan/dipenuhi. Tingkat pemenuhan 94,3%. Seluruh permohonan telah memperoleh tindak lanjut dan tidak terdapat permohonan dalam proses pada akhir periode.',
            'source_document_path' => 'docs/LAPORAN LAYANAN INFORMASI PUBLIK 2025.docx',
            'is_published' => true,
            'published_at' => now(),
        ];

        if ($existing) {
            $existing->update($payload);

            return;
        }

        InformationServiceSummary::query()->create($payload);
    }
}
