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

    public function test_tunggakan_template_sheet_protection_and_cell_locks(): void
    {
        $export = new TunggakanImportTemplateExport(prefill: true);
        \Maatwebsite\Excel\Facades\Excel::store($export, 'temp_test_protection.xlsx', 'local');

        $path = storage_path('app/private/temp_test_protection.xlsx');
        if (!file_exists($path)) {
            $path = storage_path('app/temp_test_protection.xlsx');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        
        // Sheet 0: Isian Data Tunggakan
        $sheet0 = $spreadsheet->getSheet(0);
        $this->assertTrue($sheet0->getProtection()->isProtectionEnabled());
        $this->assertEquals('protected', $sheet0->getStyle('A2')->getProtection()->getLocked());
        $this->assertEquals('protected', $sheet0->getStyle('B2')->getProtection()->getLocked());
        $this->assertEquals('unprotected', $sheet0->getStyle('F2')->getProtection()->getLocked());

        // Sheet 1: Referensi Santri & NIS
        $sheet1 = $spreadsheet->getSheet(1);
        $this->assertTrue($sheet1->getProtection()->isProtectionEnabled());

        // Sheet 2: Petunjuk Pengisian
        $sheet2 = $spreadsheet->getSheet(2);
        $this->assertTrue($sheet2->getProtection()->isProtectionEnabled());

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function test_santri_import_manager_multi_dormitory_filter(): void
    {
        $org = \App\Modules\Core\Models\Organization::create(['name' => 'Pesantren Pusat 2', 'slug' => 'pesantren-pusat-2', 'type' => 'pondok']);
        $dormA = Dormitory::create(['name' => 'Komplek A', 'gender' => 'L', 'capacity' => 50]);
        $dormB = Dormitory::create(['name' => 'Komplek B', 'gender' => 'L', 'capacity' => 50]);
        $dormC = Dormitory::create(['name' => 'Komplek C', 'gender' => 'L', 'capacity' => 50]);

        $roomA = Room::create(['dormitory_id' => $dormA->id, 'name' => 'Kamar A1', 'capacity' => 10]);
        $roomB = Room::create(['dormitory_id' => $dormB->id, 'name' => 'Kamar B1', 'capacity' => 10]);
        $roomC = Room::create(['dormitory_id' => $dormC->id, 'name' => 'Kamar C1', 'capacity' => 10]);

        $santriA = Person::create(['name' => 'Santri Dari A', 'gender' => 'L']);
        $santriB = Person::create(['name' => 'Santri Dari B', 'gender' => 'L']);
        $santriC = Person::create(['name' => 'Santri Dari C', 'gender' => 'L']);

        foreach ([[$santriA, $roomA], [$santriB, $roomB], [$santriC, $roomC]] as [$s, $r]) {
            PersonRole::create([
                'person_id'         => $s->id,
                'organization_id'   => $org->id,
                'role_type'         => 'santri',
                'enrollment_status' => 'aktif',
                'presence_status'   => 'mukim',
                'is_active'         => true,
            ]);
            RoomAssignment::create([
                'person_id'  => $s->id,
                'room_id'    => $r->id,
                'valid_from' => now()->toDateString(),
                'is_active'  => true,
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(SantriImportManager::class)
            ->set('showTunggakanTemplateModal', true)
            ->set('templateDormitoryIds', [(string)$dormA->id, (string)$dormB->id])
            ->assertSee('Santri Dari A')
            ->assertSee('Santri Dari B')
            ->assertSee('2 Komplek')
            ->assertSee('2 Santri Ditemukan');
    }

    public function test_process_and_commit_tunggakan_import_flow(): void
    {
        $org = \App\Modules\Core\Models\Organization::create(['name' => 'Pesantren Pusat 3', 'slug' => 'pesantren-pusat-3', 'type' => 'pondok']);
        $santri = Person::create(['name' => 'Santri Import Test', 'gender' => 'L', 'nik' => '1234567890123456']);

        PersonRole::create([
            'person_id'         => $santri->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
            'is_active'         => true,
        ]);

        \App\Modules\Kepengasuhan\Models\SantriProfile::create([
            'person_id'       => $santri->id,
            'additional_info' => ['nis' => '2026999'],
        ]);

        // Buat file excel sementara yang merepresentasikan file hasil unduhan yang diisi pengurus
        $tempExport = new TunggakanImportTemplateExport(prefill: true);
        \Maatwebsite\Excel\Facades\Excel::store($tempExport, 'test_filled_tunggakan.xlsx', 'local');

        $path = storage_path('app/private/test_filled_tunggakan.xlsx');
        if (!file_exists($path)) {
            $path = storage_path('app/test_filled_tunggakan.xlsx');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Simulasikan pengurus mengisi nominal 150000 pada baris ke-2 (santri 2026999)
        $sheet->setCellValue('A2', '2026999');
        $sheet->setCellValue('B2', 'Santri Import Test');
        $sheet->setCellValue('C2', 'kebersihan');
        $sheet->setCellValue('D2', '2025');
        $sheet->setCellValue('F2', '150000');
        $sheet->setCellValue('G2', 'Tunggakan kas sampah tahun 2025');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($path);

        $fileContent = file_get_contents($path);
        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'test_filled_tunggakan.xlsx',
            $fileContent
        );

        Livewire::actingAs($this->admin)
            ->test(SantriImportManager::class)
            ->set('excelFile', $uploadedFile)
            ->call('processTunggakanImport')
            ->assertCount('tempValidTunggakan', 1)
            ->call('commitTunggakanImport');

        $this->assertDatabaseHas('bills', [
            'person_id'   => $santri->id,
            'bill_type'   => 'kebersihan',
            'period_year' => 2025,
            'amount'      => 150000,
            'status'      => 'unpaid',
            'notes'       => 'Tunggakan kas sampah tahun 2025',
        ]);

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
