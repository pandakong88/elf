<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeding penting — ikuti dependency order.
     */
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,       // 1. Struktur organisasi
            PositionSeeder::class,           // 2. Jabatan per unit
            MasterDataSeeder::class,         // 3. Data referensi global
            RolePermissionSeeder::class,     // 4. Roles, permissions & pengurus/staff user
            // RealTestingDataSeeder::class,    // 5. Dinonaktifkan — Data santri, kamar, asrama & kelas diinput manual
            CensusTemplateSeeder::class,     // 6. Template sensus default
            LandingPageContentSeeder::class, // 7. Landing Page CMS Content
            ActivitySeeder::class,           // 8. Activities with Images
            BillingConfigurationSeeder::class, // 9. Tarif resmi pedoman santri (Syahriah, Registrasi & Kitab)
            LandingPageContentSeeder::class, // 10. Landing Page CMS Content (re-run to ensure content is up-to-date)
        ]);
    }
}
