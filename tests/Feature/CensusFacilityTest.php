<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Organization;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusFacilityItem;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\LockerService;
use App\Modules\Kepengasuhan\Services\CensusFacilityService;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CensusFacilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $sekretarisPutra;
    protected User $musyrifUser;

    protected Dormitory $dormPutra;
    protected Room $roomA;

    protected Person $santri1;

    protected CensusV3Service $censusService;
    protected LockerService $lockerService;
    protected CensusFacilityService $facilityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->censusService   = app(CensusV3Service::class);
        $this->lockerService   = app(LockerService::class);
        $this->facilityService = app(CensusFacilityService::class);

        // Seed permissions
        $permissions = [
            'manage-sensus-v3',
            'input-census-v3',
            'approve-census-v3',
            'manage-kamar',
            'manage-asrama',
        ];
        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }

        $roleSekretaris = Role::findOrCreate('sekretaris-putra', 'web');
        $roleSekretaris->syncPermissions($permissions);

        $roleMusyrif = Role::findOrCreate('musyrif', 'web');
        $roleMusyrif->syncPermissions(['input-census-v3']);

        // Users
        $this->sekretarisPutra = User::factory()->create([
            'email' => 'sekretaris.putra@test.local',
        ]);
        $this->sekretarisPutra->assignRole('sekretaris-putra');

        $this->musyrifUser = User::factory()->create([
            'email' => 'musyrif@test.local',
        ]);
        $this->musyrifUser->assignRole('musyrif');

        // Dormitory & Room
        $this->dormPutra = Dormitory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Komplek Al-Ghazali',
            'gender' => 'L',
            'is_active' => true,
        ]);

        $this->roomA = Room::create([
            'id' => (string) Str::uuid(),
            'dormitory_id' => $this->dormPutra->id,
            'name' => 'Kamar A.101',
            'capacity' => 4,
            'is_active' => true,
        ]);

        $org = Organization::create([
            'id' => (string) Str::uuid(),
            'name' => 'Pondok Utama',
            'slug' => 'pondok-utama',
            'type' => 'pondok',
        ]);

        // Santri 1
        $this->santri1 = Person::create([
            'id' => (string) Str::uuid(),
            'nik' => '35010100010001',
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'id' => (string) Str::uuid(),
            'person_id' => $this->santri1->id,
            'organization_id' => $org->id,
            'role_type' => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'is_active' => true,
            'valid_from' => now()->subMonths(6),
        ]);
        SantriProfile::create([
            'person_id' => $this->santri1->id,
            'nis' => 'SN-1001',
            'status' => 'aktif',
        ]);
        RoomAssignment::create([
            'id' => (string) Str::uuid(),
            'person_id' => $this->santri1->id,
            'room_id' => $this->roomA->id,
            'valid_from' => now()->subMonths(6)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_can_create_facility_campaign_with_census_type(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Fasilitas dan Lemari Awal Tahun',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'fasilitas',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $this->assertDatabaseHas('census_campaigns', [
            'id' => $campaign->id,
            'census_type' => 'fasilitas',
        ]);
        $this->assertNull($campaign->template_id);
    }

    public function test_locker_service_generate_assign_and_condition(): void
    {
        // 1. Generate lockers
        $lockers = $this->lockerService->generateLockersForRoom($this->roomA->id, 4);
        $this->assertCount(4, $lockers);
        $this->assertDatabaseHas('lockers', [
            'room_id' => $this->roomA->id,
            'code' => 'L-01',
            'condition' => 'baik',
        ]);

        $locker1 = $lockers[0];

        // 2. Assign locker
        $this->lockerService->assignLocker($locker1->id, $this->santri1->id);
        $this->assertDatabaseHas('lockers', [
            'id' => $locker1->id,
            'assigned_person_id' => $this->santri1->id,
        ]);

        // 3. Update condition
        $this->lockerService->updateCondition($locker1->id, 'rusak_ringan', 'Kunci lemari patah');
        $this->assertDatabaseHas('lockers', [
            'id' => $locker1->id,
            'condition' => 'rusak_ringan',
            'notes' => 'Kunci lemari patah',
        ]);

        // 4. Summary
        $summary = $this->lockerService->getDormitoryLockersSummary($this->dormPutra->id);
        $this->assertEquals(4, $summary['total']);
        $this->assertEquals(1, $summary['occupied']);
        $this->assertEquals(3, $summary['empty']);
        $this->assertEquals(1, $summary['by_condition']['rusak_ringan']);
    }

    public function test_census_facility_service_sheet_and_save(): void
    {
        $this->lockerService->generateLockersForRoom($this->roomA->id, 4);

        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Fasilitas Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'fasilitas',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        // 1. Sheet structure
        $sheet = $this->facilityService->getFacilityCensusSheet($this->dormPutra->id);
        $this->assertArrayHasKey('komplek_items', $sheet);
        $this->assertArrayHasKey('rooms', $sheet);
        $this->assertCount(1, $sheet['rooms']);
        $this->assertCount(4, $sheet['rooms'][0]['lockers']);

        $locker1 = $sheet['rooms'][0]['lockers']->first();

        // 2. Save facility entries
        // Entry Komplek
        $this->facilityService->saveFacilityEntry(
            $campaign->id,
            $this->dormPutra->id,
            [
                'item_type' => 'komplek',
                'item_name' => 'Kamar Mandi & WC Komplek',
                'condition' => 'rusak_ringan',
                'notes' => 'Kran bocor',
            ],
            $this->musyrifUser->id
        );

        // Entry Lemari
        $this->facilityService->saveFacilityEntry(
            $campaign->id,
            $this->dormPutra->id,
            [
                'item_type' => 'lemari',
                'room_id' => $this->roomA->id,
                'locker_id' => $locker1->id,
                'item_name' => 'Lemari L-01',
                'condition' => 'rusak_berat',
                'assigned_person_id' => $this->santri1->id,
                'notes' => 'Pintu lemari lepas',
            ],
            $this->musyrifUser->id
        );

        $this->assertDatabaseHas('census_facility_items', [
            'campaign_id' => $campaign->id,
            'item_type' => 'komplek',
            'condition' => 'rusak_ringan',
        ]);

        $this->assertDatabaseHas('census_facility_items', [
            'campaign_id' => $campaign->id,
            'locker_id' => $locker1->id,
            'condition' => 'rusak_berat',
            'assigned_person_id' => $this->santri1->id,
        ]);
    }

    public function test_apply_approved_facilities_updates_lockers(): void
    {
        $lockers = $this->lockerService->generateLockersForRoom($this->roomA->id, 2);
        $locker1 = $lockers[0];

        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Fasilitas Approval Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'fasilitas',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $this->facilityService->saveFacilityEntry(
            $campaign->id,
            $this->dormPutra->id,
            [
                'item_type' => 'lemari',
                'room_id' => $this->roomA->id,
                'locker_id' => $locker1->id,
                'item_name' => 'Lemari L-01',
                'condition' => 'rusak_ringan',
                'assigned_person_id' => $this->santri1->id,
                'notes' => 'Engsel goyang',
            ],
            $this->musyrifUser->id
        );

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['status' => 'submitted']);

        $results = $this->facilityService->applyApprovedFacilities($cd->id, $this->sekretarisPutra->id);

        $this->assertEquals(1, $results['applied']);
        $this->assertEquals(0, $results['failed']);

        // Verify locker updated
        $locker1->refresh();
        $this->assertEquals('rusak_ringan', $locker1->condition);
        $this->assertEquals($this->santri1->id, $locker1->assigned_person_id);
    }

    public function test_livewire_facility_sheet_and_review(): void
    {
        $this->lockerService->generateLockersForRoom($this->roomA->id, 2);

        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Facility Livewire Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'fasilitas',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['assigned_to' => $this->musyrifUser->id]);

        $this->actingAs($this->musyrifUser);

        // Test Sheet component
        Livewire::test(\App\Livewire\Kepengasuhan\CensusFacilitySheet::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->assertSee('Komplek Al-Ghazali')
        ->assertSee('Fasilitas Komplek')
        ->call('saveDraft')
        ->assertHasNoErrors();

        // Submit for review
        $cd->update(['status' => 'submitted']);

        $this->actingAs($this->sekretarisPutra);

        // Test Review component
        Livewire::test(\App\Livewire\Kepengasuhan\CensusFacilityReview::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->assertSee('Verifikasi Fasilitas')
        ->assertSee('Komplek Al-Ghazali')
        ->call('approveAndApply')
        ->assertHasNoErrors();
    }
}
