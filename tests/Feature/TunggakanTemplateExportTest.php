<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Livewire\System\SantriImportManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TunggakanImportTemplateExport;

class TunggakanTemplateExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name'  => 'Super Admin',
        ]);
        $this->admin->assignRole('super-admin');
    }

    public function test_download_tunggakan_template_route_success(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get(route('system.tunggakan.download-template', [
            'gender'          => 'L',
            'presence_status' => 'mukim',
            'order_by'        => 'komplek',
            'bill_type'       => 'kebersihan',
            'year'            => 2025,
            'prefill'         => 1,
        ]));

        $response->assertOk();

        Excel::assertDownloaded('Template_Tunggakan_Putra_Mukim_Urut_Komplek_kebersihan_2025.xlsx', function (TunggakanImportTemplateExport $export) {
            return count($export->sheets()) === 3;
        });
    }

    public function test_santri_import_manager_template_modal_filters(): void
    {
        $org = \App\Modules\Core\Models\Organization::create(['name' => 'Pesantren Pusat', 'slug' => 'pesantren-pusat', 'type' => 'pondok']);
        $dorm = Dormitory::create(['name' => 'Asrama Al-Falah', 'gender' => 'L', 'capacity' => 50]);
        $room = Room::create(['dormitory_id' => $dorm->id, 'name' => 'Kamar 01', 'capacity' => 10]);

        $santri = Person::create([
            'name' => 'Muhammad Ali',
            'gender' => 'L',
        ]);

        PersonRole::create([
            'person_id'         => $santri->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
            'is_active'         => true,
        ]);

        RoomAssignment::create([
            'person_id'  => $santri->id,
            'room_id'    => $room->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SantriImportManager::class)
            ->set('showTunggakanTemplateModal', true)
            ->set('templatePresenceStatus', 'mukim')
            ->set('templateOrderBy', 'komplek')
            ->assertSee('Muhammad Ali')
            ->assertSee('Asrama Al-Falah')
            ->assertSee('1 Santri Ditemukan')
            ->set('templatePresenceStatus', 'laju')
            ->assertSee('0 Santri Ditemukan')
            ->assertSee('Tidak ada santri yang sesuai kriteria filter saat ini.');
    }
}
