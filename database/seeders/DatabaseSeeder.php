<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PpidAdminSeeder::class,
            SiteSettingsSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ServiceLinkSeeder::class,
            MenuSeeder::class,
            PageSeeder::class,
            InformasiBerkalaSeeder::class,
            InformasiSertaMertaSeeder::class,
            StatistikKepegawaianSeeder::class,
            DokumenPengadaanBarangJasaSeeder::class,
            InformasiDikecualikanSeeder::class,
            AcademicCalendarSeeder::class,
            LhkpnDocumentSeeder::class,
            ContentExampleSeeder::class,
        ]);
    }
}
