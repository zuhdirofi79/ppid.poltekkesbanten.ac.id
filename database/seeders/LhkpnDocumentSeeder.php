<?php

namespace Database\Seeders;

use App\Models\LhkpnDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

class LhkpnDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        foreach ($this->definitions() as $row) {
            $existing = LhkpnDocument::query()->where('position_key', $row['position_key'])->first();

            if (! $existing) {
                LhkpnDocument::query()->create([
                    ...$row,
                    'is_published' => true,
                    'published_at' => now(),
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]);

                continue;
            }

            if ($this->hasAnySource($existing) || blank($row['public_asset_path'])) {
                continue;
            }

            $existing->forceFill([
                'public_asset_path' => $row['public_asset_path'],
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'document_label' => filled($existing->document_label) ? $existing->document_label : $row['document_label'],
            ])->save();
        }
    }

    private function hasAnySource(LhkpnDocument $document): bool
    {
        return filled($document->media_asset_id)
            || filled($document->file_path)
            || filled($document->public_asset_path)
            || filled($document->external_url);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function definitions(): array
    {
        return [
            [
                'position_key' => 'direktur',
                'person_name' => 'Dr. Dahrizal, S.Kp., MPH',
                'position' => 'Direktur Poltekkes Kemenkes Banten',
                'group_key' => LhkpnDocument::GROUP_PIMPINAN,
                'sort_order' => 10,
                'report_year' => null,
                'document_label' => 'Lembar Penyerahan E-Filing LHKPN',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/DAHRIZAL.pdf',
            ],
            [
                'position_key' => 'wakil-direktur-1',
                'person_name' => 'Kusniawati, S.Kep., Ners., M.Kep',
                'position' => 'Wakil Direktur I Poltekkes Kemenkes Banten',
                'group_key' => LhkpnDocument::GROUP_PIMPINAN,
                'sort_order' => 20,
                'report_year' => null,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/Kusniawati, S.Kep., Ners., M.Kep _Tanda_Terima_LHKPN_3603176611740005_17-March-2026 15_15_21.pdf',
            ],
            [
                'position_key' => 'wakil-direktur-2',
                'person_name' => 'Sendy Cokro Wibowo, S.E.',
                'position' => 'Wakil Direktur II Poltekkes Kemenkes Banten',
                'group_key' => LhkpnDocument::GROUP_PIMPINAN,
                'sort_order' => 30,
                'report_year' => 2025,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/SENDY COKRO WIBOWO.pdf',
            ],
            [
                'position_key' => 'wakil-direktur-3',
                'person_name' => 'Asmawati, S.Kp., M.Kep',
                'position' => 'Wakil Direktur III Poltekkes Kemenkes Banten',
                'group_key' => LhkpnDocument::GROUP_PIMPINAN,
                'sort_order' => 40,
                'report_year' => null,
                'document_label' => null,
                'notes' => null,
                'source_type' => null,
                'public_asset_path' => null,
            ],
            [
                'position_key' => 'ketua-spi',
                'person_name' => 'Ahmad, SKM., M.Kes',
                'position' => 'Ketua Satuan Pengawas Internal',
                'group_key' => LhkpnDocument::GROUP_TERKAIT,
                'sort_order' => 50,
                'report_year' => 2025,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/AHMAD.pdf',
            ],
            [
                'position_key' => 'sekretaris-spi',
                'person_name' => 'Marlin Brigita',
                'position' => 'Sekretaris Satuan Pengawas Internal',
                'group_key' => LhkpnDocument::GROUP_TERKAIT,
                'sort_order' => 60,
                'report_year' => 2025,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/MARLIN BRIGITA.pdf',
            ],
            [
                'position_key' => 'kepala-pusat-pengembangan-pendidikan',
                'person_name' => 'Ida Lindawati',
                'position' => 'Kepala Pusat Pengembangan Pendidikan',
                'group_key' => LhkpnDocument::GROUP_TERKAIT,
                'sort_order' => 70,
                'report_year' => 2025,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/IDA LINDAWATI.pdf',
            ],
            [
                'position_key' => 'bendahara',
                'person_name' => 'Indah Tryana',
                'position' => 'Bendahara',
                'group_key' => LhkpnDocument::GROUP_TERKAIT,
                'sort_order' => 80,
                'report_year' => 2025,
                'document_label' => 'Tanda Terima / Dokumen e-LHKPN KPK',
                'notes' => null,
                'source_type' => LhkpnDocument::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/LHKPN_verif_KPK/INDAH TRYANA.pdf',
            ],
        ];
    }
};
