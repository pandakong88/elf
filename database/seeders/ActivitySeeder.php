<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\MasterData;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $rootOrg = Organization::where('slug', 'ponpes-al-fithroh')->firstOrFail();
        
        $kegiatanTypes = [
            'KAJIAN_KITAB' => [
                'name' => 'Kajian Rutin Shahih Bukhari',
                'description' => 'Kajian rutin Kitab Shahih Bukhari bersama KH. Ahmad Mamsyad diikuti oleh seluruh santri mukim putra dan putri bertempat di Aula Utama Madrasah.',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=800'
            ],
            'MUHADHARAH' => [
                'name' => 'Khitobah & Latihan Pidato Santri',
                'description' => 'Pelatihan ceramah/pidato tiga bahasa (Arab, Inggris, Indonesia) guna membentuk mental dakwah santri Al-Fithroh sejak dini dalam menyebarkan syiar Islam.',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800'
            ],
            'KERJA_BAKTI' => [
                'name' => 'Kerja Bakti Kebersihan Komplek',
                'description' => 'Kegiatan gotong royong membersihkan area asrama, komplek ibadah, dan lingkungan sekitar pondok pesantren oleh seluruh santri Al-Fithroh.',
                'image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=800'
            ]
        ];

        foreach ($kegiatanTypes as $code => $data) {
            $type = MasterData::where('category', 'jenis_kegiatan')->where('code', $code)->firstOrFail();

            $activity = Activity::create([
                'id' => Str::uuid()->toString(),
                'organization_id' => $rootOrg->id,
                'activity_type_id' => $type->id,
                'name' => $data['name'],
                'date' => now()->subDays(rand(1, 10)),
                'description' => $data['description'],
                'visibility' => 'umum',
            ]);

            // Attempt to add high-quality image from Unsplash
            try {
                $activity->addMediaFromUrl($data['image'])
                    ->toMediaCollection('photos');
            } catch (\Exception $e) {
                // Fail silently if there's no internet or curl error, to keep seeder robust
            }
        }
    }
}
