<?php

namespace Database\Seeders;

use App\Models\LeadershipProfile;
use Illuminate\Database\Seeder;

class LeadershipProfileSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->defaults() as $positionKey => $attributes) {
            $profile = LeadershipProfile::query()->firstOrCreate(
                ['position_key' => $positionKey],
                $attributes,
            );

            foreach (['short_summary', 'detail_body', 'education', 'work_history', 'photo_path', 'credentials'] as $field) {
                if ($profile->{$field} === null && array_key_exists($field, $attributes)) {
                    $profile->{$field} = $attributes[$field];
                }
            }

            if ($profile->isDirty()) {
                $profile->save();
            }
        }
    }

    /** @return array<string, array<string, mixed>> */
    private function defaults(): array
    {
        return [
            'director' => [
                'name' => 'Dr. Dahrizal, S.Kp., MPH',
                'credentials' => null,
                'position' => 'DIREKTUR POLTEKKES KEMENKES BANTEN',
                'short_summary' => 'Dr. Dahrizal diangkat menjadi Direktur Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 14 Maret 2025 oleh Direktur Jenderal Sumber Daya Manusia Kesehatan Kementerian Kesehatan Republik Indonesia. Beliau menjabat sebagai Direktur Poltekkes Kemenkes Banten untuk periode 2025–2029.',
                'detail_body' => implode("\n\n", [
                    'Dr. Dahrizal diangkat menjadi Direktur Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 14 Maret 2025 oleh Direktur Jenderal Sumber Daya Manusia Kesehatan Kementerian Kesehatan Republik Indonesia. Beliau menjabat sebagai Direktur Poltekkes Kemenkes Banten untuk periode 2025–2029.',
                    'Sebelumnya, beliau menjabat sebagai Wakil Direktur II Poltekkes Kemenkes Bengkulu pada periode 2020–2024. Dalam jabatan tersebut, beliau bertanggung jawab atas pengelolaan keuangan, kepegawaian, administrasi umum, sarana dan prasarana, serta tata kelola Badan Layanan Umum (BLU). Tanggung jawabnya juga mencakup perencanaan bisnis dan anggaran, pengembangan bisnis BLU, serta pengelolaan sistem informasi dan kehumasan institusi.',
                    'Pengalaman beliau dalam pengelolaan pendidikan kesehatan mencakup jabatan Direktur Akademi Keperawatan Sapta Bakti Bengkulu pada 2000–2001.',
                    'Di Poltekkes Kemenkes Bengkulu, beliau menjabat sebagai Kepala Unit Penelitian dan Pengabdian kepada Masyarakat pada 2010–2015, dengan tanggung jawab dalam perencanaan penelitian, pengabdian kepada masyarakat, publikasi, serta pengembangan sumber daya manusia dan institusi. Selanjutnya, beliau menjabat sebagai Ketua Jurusan Keperawatan pada 2015–2019, yang mencakup penyelenggaraan tridarma perguruan tinggi pada program Diploma Tiga, Sarjana Terapan, dan Pendidikan Profesi Ners.',
                    'Riwayat pendidikan beliau meliputi pendidikan Keperawatan di Universitas Padjadjaran, magister Kesehatan Masyarakat di Universitas Gadjah Mada, serta doktor di Universitas Islam Negeri Fatmawati Soekarno Bengkulu.',
                ]),
                'education' => implode("\n", [
                    'Pendidikan Keperawatan — Universitas Padjadjaran',
                    'Magister Kesehatan Masyarakat — Universitas Gadjah Mada',
                    'Doktor — Universitas Islam Negeri Fatmawati Soekarno Bengkulu',
                ]),
                'work_history' => implode("\n", [
                    'Direktur Poltekkes Kemenkes Banten — 2025–2029',
                    'Wakil Direktur II Poltekkes Kemenkes Bengkulu — 2020–2024',
                    'Ketua Jurusan Keperawatan Poltekkes Kemenkes Bengkulu — 2015–2019',
                    'Kepala Unit Penelitian dan Pengabdian kepada Masyarakat Poltekkes Kemenkes Bengkulu — 2010–2015',
                    'Direktur Akademi Keperawatan Sapta Bakti Bengkulu — 2000–2001',
                ]),
                'photo_path' => LeadershipProfile::DEFAULT_PHOTOS['director'],
                'sort_order' => 10,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now(),
            ],
            'vice-director-1' => [
                'name' => 'Kusniawati, S.Kep., Ners., M.Kep.',
                'credentials' => null,
                'position' => 'WAKIL DIREKTUR I POLTEKKES KEMENKES BANTEN',
                'short_summary' => 'Kusniawati, S.Kep., Ners., M.Kep. dilantik sebagai Wakil Direktur I Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 3 Juni 2025 oleh Direktur Poltekkes Kemenkes Banten untuk periode 2025–2029.',
                'detail_body' => implode("\n\n", [
                    'Kusniawati, S.Kep., Ners., M.Kep. dilantik sebagai Wakil Direktur I Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 3 Juni 2025 oleh Direktur Poltekkes Kemenkes Banten untuk periode 2025–2029.',
                    'Sebelumnya, beliau menjabat sebagai Ketua Jurusan Keperawatan Poltekkes Kemenkes Banten hingga tahun 2024. Selain memiliki pengalaman dalam pengelolaan jurusan, beliau merupakan dosen dengan jabatan fungsional Lektor yang mengajar di Jurusan Keperawatan Poltekkes Kemenkes Banten.',
                    'Saat ini, beliau sedang menempuh pendidikan doktor (S3) di Universitas Islam Negeri Syarif Hidayatullah Jakarta.',
                ]),
                'education' => 'Sedang menempuh pendidikan doktor (S3) di Universitas Islam Negeri Syarif Hidayatullah Jakarta.',
                'work_history' => implode("\n", [
                    'Wakil Direktur I Poltekkes Kemenkes Banten — 2025–2029',
                    'Ketua Jurusan Keperawatan Poltekkes Kemenkes Banten — hingga 2024',
                    'Dosen Jurusan Keperawatan Poltekkes Kemenkes Banten',
                    'Jabatan fungsional: Lektor',
                ]),
                'photo_path' => LeadershipProfile::DEFAULT_PHOTOS['vice-director-1'],
                'sort_order' => 20,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now(),
            ],
            'vice-director-2' => [
                'name' => 'Sendy Cokro Wibowo, S.E.',
                'credentials' => null,
                'position' => 'WAKIL DIREKTUR II POLTEKKES KEMENKES BANTEN',
                'short_summary' => 'Sendy Cokro Wibowo, S.E. dilantik sebagai Wakil Direktur II Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 7 Mei 2026 untuk periode 2026–2029. Pelantikan tersebut dilaksanakan di Jakarta oleh Direktur Jenderal Sumber Daya Manusia Kesehatan Kementerian Kesehatan Republik Indonesia.',
                'detail_body' => implode("\n\n", [
                    'Sendy Cokro Wibowo, S.E. dilantik sebagai Wakil Direktur II Politeknik Kesehatan Kementerian Kesehatan Banten pada tanggal 7 Mei 2026 untuk periode 2026–2029. Pelantikan tersebut dilaksanakan di Jakarta oleh Direktur Jenderal Sumber Daya Manusia Kesehatan Kementerian Kesehatan Republik Indonesia.',
                    'Sebelumnya, beliau menjabat sebagai Pelaksana Tugas (Plt.) Kepala Subbagian Administrasi Umum Poltekkes Kemenkes Banten hingga tahun 2025. Selanjutnya, beliau menjabat sebagai Pejabat Pembuat Komitmen (PPK) dan mengemban tugas tambahan sebagai Wakil Direktur II Poltekkes Kemenkes Banten yang dijalankan hingga saat ini.',
                    'Saat ini, beliau sedang menempuh pendidikan magister (S2) di salah satu perguruan tinggi negeri di Indonesia.',
                ]),
                'education' => implode("\n", [
                    'Sarjana Ekonomi',
                    'Sedang menempuh pendidikan magister (S2) di salah satu perguruan tinggi negeri di Indonesia.',
                ]),
                'work_history' => implode("\n", [
                    'Wakil Direktur II Poltekkes Kemenkes Banten — sejak 7 Mei 2026',
                    'Pejabat Pembuat Komitmen (PPK) Poltekkes Kemenkes Banten',
                    'Plt. Kepala Subbagian Administrasi Umum Poltekkes Kemenkes Banten — hingga 2025',
                ]),
                'photo_path' => LeadershipProfile::DEFAULT_PHOTOS['vice-director-2'],
                'sort_order' => 30,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now(),
            ],
        ];
    }
}
