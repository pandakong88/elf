<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\CensusPeriod;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\DormitoryCensus;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\RoomCensusDetail;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Services\CensusService;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Kepengasuhan\Services\GuardianService;
use App\Modules\Kepengasuhan\Services\SiblingService;
use App\Modules\Kepengasuhan\Services\CensusExcelService;
use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\SantriSibling;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CensusTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $musyrifUser;
    private Organization $org;
    private Dormitory $dormitory;
    private Room $room;
    private Person $santri1;
    private Person $santri2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\OrganizationSeeder::class);
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->org = Organization::where('slug', 'ponpes-al-fithroh')->firstOrFail();
        $this->adminUser   = User::where('email', 'admin@alfithroh.pondok')->firstOrFail();
        $this->musyrifUser = User::where('email', 'musyrif@alfithroh.pondok')->firstOrFail();

        // Buat asrama test
        $this->dormitory = Dormitory::create([
            'id'              => Str::uuid()->toString(),
            'name'            => 'Komplek Test Putra',
            'gender'          => 'L',
            'is_active'       => true,
            'organization_id' => $this->org->id,
        ]);

        $this->room = Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $this->dormitory->id,
            'name'         => 'Kamar 1',
            'capacity'     => 5,
            'is_active'    => true,
        ]);

        // Buat santri test
        $this->santri1 = $this->createSantri('Santri Satu', 'L');
        $this->santri2 = $this->createSantri('Santri Dua', 'L');

        // Assign ke kamar
        RoomAssignment::create([
            'id'         => Str::uuid()->toString(),
            'room_id'    => $this->room->id,
            'person_id'  => $this->santri1->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);
        RoomAssignment::create([
            'id'         => Str::uuid()->toString(),
            'room_id'    => $this->room->id,
            'person_id'  => $this->santri2->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function createSantri(string $name, string $gender): Person
    {
        $person = Person::create([
            'id'         => Str::uuid()->toString(),
            'nik'        => Str::random(16),
            'name'       => $name,
            'gender'     => $gender,
            'birth_date' => '2005-01-01',
        ]);

        PersonRole::create([
            'id'              => Str::uuid()->toString(),
            'person_id'       => $person->id,
            'organization_id' => $this->org->id,
            'role_type'       => 'santri',
            'valid_from'      => now()->toDateString(),
            'is_active'       => true,
        ]);

        return $person;
    }

    // =========================================================================
    // Tests: CensusPeriod Creation
    // =========================================================================

    public function test_admin_can_create_census_period(): void
    {
        $service = app(CensusService::class);
        $period = $service->createPeriod('Sensus Juli 2026', 7, 2026, $this->adminUser->id);

        $this->assertDatabaseHas('census_periods', [
            'name'   => 'Sensus Juli 2026',
            'month'  => 7,
            'year'   => 2026,
            'status' => 'draft',
        ]);

        // Harus ada 1 DormitoryCensus untuk asrama yang kita buat
        $this->assertDatabaseHas('dormitory_censuses', [
            'census_period_id' => $period->id,
            'dormitory_id'     => $this->dormitory->id,
            'status'           => 'pending',
        ]);
    }

    // =========================================================================
    // Tests: Period Activation
    // =========================================================================

    public function test_period_can_be_activated_from_draft(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);

        $service->startPeriod($period->id);

        $this->assertDatabaseHas('census_periods', ['id' => $period->id, 'status' => 'active']);
    }

    public function test_cannot_activate_already_active_period(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $this->expectException(\DomainException::class);
        $service->startPeriod($period->id); // Harusnya error
    }

    // =========================================================================
    // Tests: Room Census Saving
    // =========================================================================

    public function test_musyrif_can_save_room_census_draft(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'present', 'notes' => null],
            ['person_id' => $this->santri2->id, 'status' => 'sick',    'notes' => 'Demam tinggi'],
        ]);

        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri1->id,
            'status'              => 'present',
        ]);
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri2->id,
            'status'              => 'sick',
        ]);
    }

    // =========================================================================
    // Tests: Census Submit
    // =========================================================================

    public function test_census_can_be_submitted_after_all_santri_filled(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'present'],
            ['person_id' => $this->santri2->id, 'status' => 'present'],
        ]);

        $service->submitCensus($dc->id, $this->musyrifUser->id);

        $this->assertDatabaseHas('dormitory_censuses', ['id' => $dc->id, 'status' => 'submitted']);
    }

    public function test_submit_census_defaults_uncompleted_santri_to_present(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test Defaults', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        // Hanya isi santri1, santri2 sengaja dilewatkan (belum diisi)
        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'present'],
        ]);

        // Submit. Seharusnya tidak error, dan status santri2 otomatis diset 'present' oleh system
        $service->submitCensus($dc->id, $this->musyrifUser->id);

        $this->assertDatabaseHas('dormitory_censuses', ['id' => $dc->id, 'status' => 'submitted']);
        
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri2->id,
            'status'              => 'present',
        ]);
    }

    // =========================================================================
    // Tests: Census Approval
    // =========================================================================

    public function test_census_approval_syncs_profile_updates(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'present', 'profile_updates' => [
                'school_status' => 'Kuliah',
                'school_name'   => 'UIN Sunan Ampel',
                'major'         => 'Ilmu Komputer',
                'medical_history' => 'Asma',
            ]],
            ['person_id' => $this->santri2->id, 'status' => 'present'],
        ]);

        $service->submitCensus($dc->id, $this->musyrifUser->id);
        $service->approveCensus($dc->id);

        // Profile santri1 harus terisi
        $this->assertDatabaseHas('santri_profiles', [
            'person_id'      => $this->santri1->id,
            'school_status'  => 'Kuliah',
            'school_name'    => 'UIN Sunan Ampel',
            'major'          => 'Ilmu Komputer',
            'medical_history' => 'Asma',
        ]);

        // Status harus approved
        $this->assertDatabaseHas('dormitory_censuses', ['id' => $dc->id, 'status' => 'approved']);
    }

    public function test_census_approval_auto_unassigns_moved_santri(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'moved'], // santri1 pindah
            ['person_id' => $this->santri2->id, 'status' => 'present'],
        ]);

        $service->submitCensus($dc->id, $this->musyrifUser->id);
        $service->approveCensus($dc->id);

        // santri1 harus sudah tidak punya assignment aktif
        $this->assertDatabaseMissing('room_assignments', [
            'person_id' => $this->santri1->id,
            'is_active'  => true,
        ]);

        // santri2 masih punya assignment aktif
        $this->assertDatabaseHas('room_assignments', [
            'person_id' => $this->santri2->id,
            'is_active'  => true,
        ]);
    }

    // =========================================================================
    // Tests: Census Rejection
    // =========================================================================

    public function test_census_can_be_rejected_with_notes(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri1->id, 'status' => 'present'],
            ['person_id' => $this->santri2->id, 'status' => 'present'],
        ]);

        $service->submitCensus($dc->id, $this->musyrifUser->id);
        $service->rejectCensus($dc->id, 'Harap periksa kembali data santri.');

        $this->assertDatabaseHas('dormitory_censuses', [
            'id'     => $dc->id,
            'status' => 'pending',
            'notes'  => 'Harap periksa kembali data santri.',
        ]);
    }

    // =========================================================================
    // Tests: SantriProfile Flexible JSON
    // =========================================================================

    public function test_santri_profile_flexible_json_additional_info(): void
    {
        $profile = SantriProfile::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $this->santri1->id,
        ]);

        $profile->setAdditional('hobi', 'Badminton');
        $profile->setAdditional('ukuran_baju', 'M');
        $profile->save();

        $profile->refresh();

        $this->assertEquals('Badminton', $profile->getAdditional('hobi'));
        $this->assertEquals('M', $profile->getAdditional('ukuran_baju'));
        $this->assertNull($profile->getAdditional('tidak_ada'));
        $this->assertEquals('default', $profile->getAdditional('tidak_ada', 'default'));
    }

    // =========================================================================
    // Tests: DormitoryService Updates
    // =========================================================================

    public function test_dormitory_service_can_update_dormitory(): void
    {
        $service = app(DormitoryService::class);
        $updated = $service->updateDormitory($this->dormitory->id, ['name' => 'Komplek A (Updated)']);

        $this->assertEquals('Komplek A (Updated)', $updated->name);
    }

    public function test_dormitory_service_can_unassign_room(): void
    {
        $assignment = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('is_active', true)
            ->firstOrFail();

        $service = app(DormitoryService::class);
        $service->unassignRoom($assignment->id);

        $this->assertDatabaseHas('room_assignments', [
            'id'        => $assignment->id,
            'is_active' => false,
        ]);
    }

    public function test_dormitory_service_getSantriWithoutRoom(): void
    {
        // Buat santri baru yang tidak punya kamar
        $homeless = $this->createSantri('Santri Tanpa Kamar', 'L');

        $service = app(DormitoryService::class);
        $result  = $service->getSantriWithoutRoom('L');

        $ids = $result->pluck('id')->toArray();
        $this->assertContains($homeless->id, $ids);
        $this->assertNotContains($this->santri1->id, $ids); // santri1 sudah punya kamar
    }

    public function test_census_v2_bulk_confirmations(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test V2', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        // 1. Bulk confirm room
        $service->bulkConfirmRoom($dc->id, $this->room->id);
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri1->id,
            'status'              => 'present',
        ]);
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri2->id,
            'status'              => 'present',
        ]);

        // Clean up details
        RoomCensusDetail::where('dormitory_census_id', $dc->id)->delete();

        // 2. Bulk confirm all
        $service->bulkConfirmAll($dc->id);
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri1->id,
            'status'              => 'present',
        ]);
    }

    public function test_exception_based_census_submission(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test Exception', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        // Hanya isi exception untuk santri2 (sakit), santri1 kosong (tidak diisi musyrif)
        $service->saveRoomCensus($dc->id, $this->room->id, [
            ['person_id' => $this->santri2->id, 'status' => 'sick', 'notes' => 'Demam'],
        ]);

        // Submit. Seharusnya tidak error, karena sisa santri (santri1) otomatis diisi "present"
        $service->submitCensus($dc->id, $this->musyrifUser->id);

        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri1->id,
            'status'              => 'present',
        ]);
        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id'           => $this->santri2->id,
            'status'              => 'sick',
        ]);
    }

    public function test_guardian_service_and_creation_flow(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test Guardian', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        // Usulkan wali baru
        $service->saveRoomCensus($dc->id, $this->room->id, [
            [
                'person_id' => $this->santri1->id,
                'status' => 'present',
                'has_guardian_update' => true,
                'guardian_updates' => [
                    'name' => 'Bapak Ahmad',
                    'relationship' => 'wali_resmi',
                    'phone_primary' => '08123456789',
                    'address' => 'Jl. Test No. 12',
                    'city' => 'Surabaya',
                ]
            ],
            ['person_id' => $this->santri2->id, 'status' => 'present'],
        ]);

        $service->submitCensus($dc->id, $this->musyrifUser->id);
        $service->approveCensus($dc->id);

        // Harus membuat record di tabel guardians
        $this->assertDatabaseHas('guardians', [
            'name' => 'Bapak Ahmad',
            'phone_primary' => '08123456789',
            'address' => 'Jl. Test No. 12',
        ]);

        $guardian = Guardian::where('name', 'Bapak Ahmad')->firstOrFail();

        // Harus terhubung di pivot santri_guardians
        $this->assertDatabaseHas('santri_guardians', [
            'person_id' => $this->santri1->id,
            'guardian_id' => $guardian->id,
            'relationship' => 'wali_resmi',
            'is_primary' => true,
        ]);
    }

    public function test_sibling_service_and_flag_updates(): void
    {
        // Set nama ayah/ibu yang sama pada kedua santri
        $p1 = SantriProfile::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $this->santri1->id,
            'father_name' => 'Pak Slamet',
            'mother_name' => 'Bu Sumi',
        ]);
        $p2 = SantriProfile::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $this->santri2->id,
            'father_name' => 'Pak Slamet',
            'mother_name' => 'Bu Sumi',
        ]);

        $siblingService = app(SiblingService::class);
        
        // 1. Deteksi otomatis
        $detected = $siblingService->detectSiblingsByGuardian();
        $this->assertEquals(1, $detected);

        $relation = SantriSibling::firstOrFail();
        $this->assertFalse($relation->is_confirmed);

        // 2. Konfirmasi
        $siblingService->confirmSibling($relation->id, 'kembar', $this->adminUser->id);

        $relation->refresh();
        $this->assertTrue($relation->is_confirmed);
        $this->assertEquals('kembar', $relation->relationship);
        $this->assertTrue($relation->is_eligible_for_discount);

        // 3. Cek flag di profile
        $p1->refresh();
        $p2->refresh();
        $this->assertTrue($p1->has_active_sibling);
        $this->assertEquals(1, $p1->active_sibling_count);
        $this->assertTrue($p2->has_active_sibling);
        $this->assertEquals(1, $p2->active_sibling_count);
    }

    public function test_excel_parsing_and_importing(): void
    {
        $service = app(CensusService::class);
        $period  = $service->createPeriod('Test Excel', 7, 2026, $this->adminUser->id);
        $service->startPeriod($period->id);

        $dc = DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        $excelService = app(CensusExcelService::class);

        // 1. Generate template
        $filePath = $excelService->generateTemplate($dc->id);
        $this->assertFileExists($filePath);

        // 2. Mock parse (karena kita tidak mengedit file fisik Excel di dalam test, kita buat helper mock data)
        $mockParsed = [
            'details' => [
                [
                    'person_id' => $this->santri1->id,
                    'status' => 'present',
                    'notes' => 'Hadir biasa',
                    'profile_updates' => [
                        'blood_type' => 'AB',
                        'school_status' => 'sekolah_luar',
                        'school_name' => 'MAN Surabaya',
                    ],
                    'guardian_updates' => [],
                    'has_profile_update' => true,
                    'has_guardian_update' => false,
                ],
                [
                    'person_id' => $this->santri2->id,
                    'status' => 'sick',
                    'notes' => 'Flu',
                    'profile_updates' => [],
                    'guardian_updates' => [],
                    'has_profile_update' => false,
                    'has_guardian_update' => false,
                ],
            ],
            'total_santri' => 2,
            'total_confirmed' => 2,
            'total_exceptions' => 2,
        ];

        // 3. Import
        $excelService->importFromExcel($dc->id, $mockParsed, $filePath);

        $this->assertDatabaseHas('dormitory_censuses', [
            'id' => $dc->id,
            'import_source' => 'excel',
            'total_exceptions' => 2,
        ]);

        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id' => $this->santri1->id,
            'status' => 'present',
            'notes' => 'Hadir biasa',
            'has_profile_update' => true,
        ]);

        $this->assertDatabaseHas('room_census_details', [
            'dormitory_census_id' => $dc->id,
            'person_id' => $this->santri2->id,
            'status' => 'sick',
            'notes' => 'Flu',
            'has_profile_update' => false,
        ]);

        // Clean up temp file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
