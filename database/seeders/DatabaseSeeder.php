<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingsSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ServiceLinkSeeder::class,
            MenuSeeder::class,
            PageSeeder::class,
            ContentExampleSeeder::class,
        ]);
    }
}
