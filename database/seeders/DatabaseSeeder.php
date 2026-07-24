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
            OrganizationSeeder::class,     // 1. Struktur organisasi
            PositionSeeder::class,         // 2. Jabatan per unit (bergantung pada organizations)
            MasterDataSeeder::class,       // 3. Data referensi global
            DummySantriSeeder::class,      // 4. Data santri dummy (bergantung pada organizations)
            RolePermissionSeeder::class,   // 5. Roles & permissions setup + admin user
            KepengasuhanSeeder::class,     // 6. Kepengasuhan domain data (Asrama, Kamar, Workflows) — DIGANTI oleh KompleKamarSeeder
            CensusTemplateSeeder::class,   // 7. Template sensus default (Standar, Wali, Khataman)
            LandingPageContentSeeder::class, // 8. Landing Page CMS Content
            ActivitySeeder::class,           // 9. Dummy Activities with Images
            MadrasahSeeder::class,           // 10. Kelas Madrasah & Enrollment Santri Mukim
            KompleKamarSeeder::class,        // 11. Reset & rebuild komplek A-D, assign santri mukim ke kamar
            SantriLajuSeeder::class,         // 12. Santri laju (3-4 per kelas, semua 13 kelas)
            IsiKamarSeeder::class,           // 13. Isi tiap kamar dengan 5-8 santri mukim
            BillingConfigurationSeeder::class, // 14. Konfigurasi Tarif Aktif (syahriah, kas komplek, kebersihan)
        ]);
    }
}
