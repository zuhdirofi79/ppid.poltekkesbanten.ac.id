<?php

namespace Database\Seeders;

use App\Models\SaqItem;
use Illuminate\Database\Seeder;

class SaqItemSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->registry() as $item) {
            SaqItem::query()->firstOrCreate(
                ['code' => $item['code']],
                $item,
            );
        }
    }

    /** @return list<array<string, mixed>> */
    private function registry(): array
    {
        $rows = [
            ['BERKALA-02', 'Diumumkan Berkala', 'Profil', 'Profil Pimpinan Satuan Kerja', 'Nama, jabatan, foto, riwayat pendidikan, dan riwayat pekerjaan.', 'page', '/halaman/profil-pimpinan-satuan-kerja', 20],
            ['BERKALA-03', 'Diumumkan Berkala', 'LHKPN', 'LHKPN Pimpinan Tertinggi Tahun Terakhir', 'Nama pejabat, jabatan, tahun LHKPN, status verifikasi, dan berkas pengumuman yang telah diverifikasi KPK.', 'page', '/halaman/lhkpn-pimpinan-tertinggi', 30],
            ['BERKALA-05', 'Diumumkan Berkala', 'Kalender', 'Kalender Kegiatan Tahun 2026', 'Jadwal, tanggal, nama kegiatan, unit penyelenggara, serta lokasi atau status bila tersedia.', 'page', '/halaman/kalender-kegiatan-2026', 50],
            ['BERKALA-09', 'Diumumkan Berkala', 'Kebijakan', 'Peraturan, Keputusan, dan/atau Kebijakan yang Mengikat Publik', 'Daftar peraturan, keputusan, atau kebijakan yang mengikat publik.', 'document', '/halaman/peraturan-keputusan-kebijakan-publik', 90],
            ['BERKALA-10', 'Diumumkan Berkala', 'Pengaduan', 'Tata Cara Pengaduan Penyalahgunaan Wewenang atau Pelanggaran', 'Kanal, langkah, persyaratan, dan perlindungan kerahasiaan. Jangan mengarang SLA.', 'page', '/layanan/pengaduan-penyalahgunaan-wewenang', 100],
            ['BERKALA-11A', 'Diumumkan Berkala', 'Layanan', 'Jumlah Permohonan Informasi Publik yang Diterima', 'Angka dari register resmi. Jangan menampilkan identitas pemohon.', 'page', '/halaman/ringkasan-layanan-informasi-publik', 111],
            ['BERKALA-11B', 'Diumumkan Berkala', 'Layanan', 'Waktu yang Diperlukan dalam Memenuhi Setiap Permohonan', 'Uraian waktu dari register resmi, tanpa identitas pemohon.', 'page', '/halaman/ringkasan-layanan-informasi-publik', 112],
            ['BERKALA-11C', 'Diumumkan Berkala', 'Layanan', 'Jumlah Permohonan yang Dikabulkan Sebagian atau Seluruhnya', 'Angka dikabulkan penuh dan sebagian dari register resmi.', 'page', '/halaman/ringkasan-layanan-informasi-publik', 113],
            ['BERKALA-11D', 'Diumumkan Berkala', 'Layanan', 'Alasan Penolakan Permohonan Informasi', 'Alasan penolakan agregat. Jangan menampilkan identitas pemohon.', 'page', '/halaman/ringkasan-layanan-informasi-publik', 114],
            ['LAYANAN-01', 'Pelayanan Informasi Publik', 'Dasar hukum', 'SK/Penetapan PPID', 'Hubungkan ke halaman atau dokumen SK yang sudah ada. Jangan membuat duplikat.', 'page', '/halaman/dasar-hukum-sk-ppid', 210],
            ['LAYANAN-02', 'Pelayanan Informasi Publik', 'DIP', 'Daftar Informasi Publik yang Dimutakhirkan 2026', 'Halaman dan PDF yang ditandatangani.', 'page', '/halaman/daftar-informasi-publik-2026', 220],
            ['LAYANAN-03', 'Pelayanan Informasi Publik', 'Laporan', 'Laporan Layanan Informasi Publik Tahun 2025', 'Halaman dan PDF laporan.', 'page', '/halaman/laporan-layanan-informasi-publik-2025', 230],
            ['LAYANAN-04', 'Pelayanan Informasi Publik', 'Koordinasi', 'Bukti Koordinasi Rutin PPID', 'Laporan kegiatan, notulensi, undangan, daftar hadir, atau foto. Bukti berisi data pribadi tetap internal.', 'page', '/halaman/koordinasi-rutin-ppid', 240],
            ['LAYANAN-05A', 'Pelayanan Informasi Publik', 'Fasilitas', 'Fasilitas Desk Information', 'Foto meja, kursi petugas, kursi tunggu, dan area penerimaan.', 'page', '/halaman/fasilitas-layanan-informasi-publik', 251],
            ['LAYANAN-05B', 'Pelayanan Informasi Publik', 'Formulir', 'Formulir dan Tanda Terima Permohonan Informasi', 'Formulir, templat tanda terima, serta petunjuk penggunaan.', 'page', '/layanan/permohonan-informasi', 252],
            ['LAYANAN-05C', 'Pelayanan Informasi Publik', 'Formulir', 'Formulir Keberatan', 'Formulir, templat PDF atau Word, serta petunjuk penggunaan.', 'page', '/layanan/pengajuan-keberatan', 253],
            ['LAYANAN-05D', 'Pelayanan Informasi Publik', 'Register', 'Daftar Register Permohonan', 'Register publik wajib disamarkan. Jangan menampilkan NIK, alamat, telepon, atau email pribadi.', 'page', '/halaman/daftar-register-permohonan', 254],
            ['LAYANAN-05E', 'Pelayanan Informasi Publik', 'Jadwal', 'Jadwal Pelayanan Informasi Publik', 'Jam layanan yang sudah tampil di kontak atau halaman khusus.', 'page', '/halaman/jadwal-pelayanan-informasi-publik', 255],
            ['PENYEDIAAN-01', 'Penyediaan Informasi Publik', 'Kebijakan', 'Peraturan/Kebijakan/Keputusan mengenai Pelayanan Informasi Publik', 'Kumpulan dokumen kebijakan pelayanan.', 'document', '/halaman/kebijakan-pelayanan-informasi-publik', 310],
            ['PENYEDIAAN-02', 'Penyediaan Informasi Publik', 'Dikecualikan', 'Informasi yang Dikecualikan', 'Hanya berdasarkan penetapan resmi dan dokumen yang ditandatangani.', 'page', '/halaman/informasi-dikecualikan', 320],
            ['PENYEDIAAN-03', 'Penyediaan Informasi Publik', 'SOP', 'SOP Pengelolaan Permohonan Informasi', 'Unggah PDF atau Word lewat dokumen publik. Simpan nomor, tanggal, tahun, status, penandatangan, dan versi.', 'document', '/dokumen-publik', 330],
            ['PENYEDIAAN-04', 'Penyediaan Informasi Publik', 'SOP', 'SOP Pengelolaan Keberatan atas Informasi', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 340],
            ['PENYEDIAAN-05', 'Penyediaan Informasi Publik', 'SOP', 'SOP Penanganan Sengketa Informasi Publik', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 350],
            ['PENYEDIAAN-06', 'Penyediaan Informasi Publik', 'SOP', 'SOP Penetapan dan Pemutakhiran DIP', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 360],
            ['PENYEDIAAN-07', 'Penyediaan Informasi Publik', 'SOP', 'SOP Pengujian tentang Konsekuensi', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 370],
            ['PENYEDIAAN-08', 'Penyediaan Informasi Publik', 'SOP', 'SOP Pendokumentasian Informasi Publik', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 380],
            ['PENYEDIAAN-09', 'Penyediaan Informasi Publik', 'SOP', 'SOP Pendokumentasian Informasi yang Dikecualikan', 'Unggah PDF atau Word lewat dokumen publik bila belum ada.', 'document', '/dokumen-publik', 390],
            ['PENYEDIAAN-10', 'Penyediaan Informasi Publik', 'PBJ', 'Surat Perjanjian dengan Pihak Ketiga mengenai PBJ beserta Dokumen Pendukung', 'Nama paket, tahun, pihak ketiga, jenis dokumen, tanggal, dan berkas. Nilai kontrak hanya bila memang boleh dipublikasikan. Lakukan redaksi sebelum publik.', 'document', '/halaman/pengadaan-barang-dan-jasa', 400],
            ['PENYEDIAAN-11', 'Penyediaan Informasi Publik', 'Renstra', 'Rencana Strategis Satuan Kerja yang Masih Berlaku', 'Gunakan dokumen RSB atau Renstra yang sudah ada. Jangan mengunggah duplikat.', 'document', '/informasi-publik/berkala', 410],
            ['PENYEDIAAN-12A', 'Penyediaan Informasi Publik', 'Statistik', 'Data Statistik Kepegawaian', 'Tabel agregat dan lampiran. Jangan menampilkan data pegawai pribadi.', 'page', '/halaman/statistik-kepegawaian', 421],
            ['PENYEDIAAN-12B', 'Penyediaan Informasi Publik', 'Statistik', 'Data Statistik Keuangan', 'Tabel agregat dan lampiran.', 'page', '/halaman/statistik-keuangan', 422],
            ['PENYEDIAAN-13', 'Penyediaan Informasi Publik', 'BMN', 'Data Perbendaharaan atau Inventaris BMN', 'Tabel atau dokumen yang sudah direview. Jangan menampilkan informasi keamanan, nomor inventaris sensitif, atau data pegawai pribadi.', 'page', '/halaman/data-perbendaharaan-inventaris-bmn', 430],
        ];

        return array_map(fn (array $row) => [
            'code' => $row[0],
            'year' => 2026,
            'section' => $row[1],
            'subsection' => $row[2],
            'question' => $row[3],
            'evidence_requirement' => $row[4],
            'recommended_content_type' => $row[5],
            'recommended_public_route' => $row[6],
            'answer' => 'not_set',
            'status' => 'missing',
            'requires_public' => true,
            'sort_order' => $row[7],
        ], $rows);
    }
}
