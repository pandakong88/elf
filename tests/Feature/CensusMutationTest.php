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
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusMutationEntry;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusMutationService;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CensusMutationTest extends TestCase
{
    use RefreshDatabase;

    protected User $sekretarisPutra;
    protected User $musyrifUser;

    protected Dormitory $dormPutra;
    protected Room $roomA;
    protected Room $roomB;

    protected Person $santri1;
    protected Person $santri2;

    protected CensusV3Service $censusService;
    protected CensusMutationService $mutationService;
    protected DormitoryService $dormService;
    protected SantriStatusService $statusService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->censusService = app(CensusV3Service::class);
        $this->mutationService = app(CensusMutationService::class);
        $this->dormService = app(DormitoryService::class);
        $this->statusService = app(SantriStatusService::class);

        // Seed permissions
        $permissions = [
            'manage-sensus-v3',
            'input-census-v3',
            'approve-census-v3',
            'change-enrollment-status',
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

        // Dormitory & Rooms
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

        $this->roomB = Room::create([
            'id' => (string) Str::uuid(),
            'dormitory_id' => $this->dormPutra->id,
            'name' => 'Kamar A.102',
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

        // Santri 2
        $this->santri2 = Person::create([
            'id' => (string) Str::uuid(),
            'nik' => '35010100010002',
            'name' => 'Budi Santoso',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'id' => (string) Str::uuid(),
            'person_id' => $this->santri2->id,
            'organization_id' => $org->id,
            'role_type' => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'is_active' => true,
            'valid_from' => now()->subMonths(6),
        ]);
        SantriProfile::create([
            'person_id' => $this->santri2->id,
            'nis' => 'SN-1002',
            'status' => 'aktif',
        ]);
        RoomAssignment::create([
            'id' => (string) Str::uuid(),
            'person_id' => $this->santri2->id,
            'room_id' => $this->roomA->id,
            'valid_from' => now()->subMonths(6)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_can_create_mutation_campaign_with_census_type(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Mutasi Santri Awal Semester',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $this->assertDatabaseHas('census_campaigns', [
            'id' => $campaign->id,
            'census_type' => 'mutasi',
        ]);
        $this->assertNull($campaign->template_id);
    }

    public function test_mutation_service_gets_dormitory_occupants(): void
    {
        $data = $this->mutationService->getDormitoryOccupants($this->dormPutra->id);

        $this->assertArrayHasKey('rooms', $data);
        $this->assertCount(2, $data['rooms']);
        $this->assertEquals('Kamar A.101', $data['rooms'][0]['name']);
        $this->assertCount(2, $data['rooms'][0]['occupants']);
    }

    public function test_can_save_mutation_entries(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Mutasi Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        // Entry 1: Santri 1 pindah ke Room B
        $this->mutationService->saveEntry(
            $campaign->id,
            $this->dormPutra->id,
            $this->santri1->id,
            [
                'from_room_id' => $this->roomA->id,
                'action' => 'pindah_kamar',
                'to_room_id' => $this->roomB->id,
                'notes' => 'Pindah kamar atas permohonan santri',
            ],
            $this->musyrifUser->id
        );

        // Entry 2: Santri 2 ubah status jadi keluar_resmi
        $this->mutationService->saveEntry(
            $campaign->id,
            $this->dormPutra->id,
            $this->santri2->id,
            [
                'from_room_id' => $this->roomA->id,
                'action' => 'ubah_status',
                'new_enrollment_status' => 'keluar_resmi',
                'notes' => 'Boyong selesai masa belajar',
            ],
            $this->musyrifUser->id
        );

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri1->id,
            'action' => 'pindah_kamar',
            'to_room_id' => $this->roomB->id,
            'apply_status' => 'pending',
        ]);

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri2->id,
            'action' => 'ubah_status',
            'new_enrollment_status' => 'keluar_resmi',
            'apply_status' => 'pending',
        ]);
    }

    public function test_apply_approved_mutations_executes_moves_and_status_changes(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Mutasi Execution Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        // Save entry 1: pindah kamar to room B
        $this->mutationService->saveEntry(
            $campaign->id,
            $this->dormPutra->id,
            $this->santri1->id,
            [
                'from_room_id' => $this->roomA->id,
                'action' => 'pindah_kamar',
                'to_room_id' => $this->roomB->id,
            ],
            $this->musyrifUser->id
        );

        // Save entry 2: status change to keluar_resmi
        $this->mutationService->saveEntry(
            $campaign->id,
            $this->dormPutra->id,
            $this->santri2->id,
            [
                'from_room_id' => $this->roomA->id,
                'action' => 'ubah_status',
                'new_enrollment_status' => 'keluar_resmi',
            ],
            $this->musyrifUser->id
        );

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['status' => 'submitted']);

        // Apply mutations
        $results = $this->mutationService->applyApprovedMutations(
            $cd->id,
            $this->sekretarisPutra->id
        );

        $this->assertEquals(2, $results['applied']);
        $this->assertEquals(0, $results['failed']);

        // Verify Santri 1 is now in Room B
        $activeAssignment1 = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('is_active', true)
            ->first();
        $this->assertNotNull($activeAssignment1);
        $this->assertEquals($this->roomB->id, $activeAssignment1->room_id);

        // Old assignment should be inactive
        $oldAssignment1 = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('room_id', $this->roomA->id)
            ->first();
        $this->assertFalse((bool) $oldAssignment1->is_active);

        // Verify Santri 2 has status keluar_resmi
        $role2 = PersonRole::where('person_id', $this->santri2->id)
            ->where('role_type', 'santri')
            ->first();
        $this->assertEquals('keluar_resmi', $role2->enrollment_status);
    }

    public function test_livewire_mutation_sheet_can_render_and_save(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Sheet Livewire Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['assigned_to' => $this->musyrifUser->id]);

        $this->actingAs($this->musyrifUser);

        Livewire::test(\App\Livewire\Kepengasuhan\CensusMutationSheet::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->assertSee('Kamar A.101')
        ->assertSee('Ahmad Fauzi')
        ->call('setAction', $this->santri1->id, 'pindah_kamar')
        ->set("entries.{$this->santri1->id}.to_room_id", $this->roomB->id)
        ->call('saveDraft')
        ->assertHasNoErrors();

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri1->id,
            'action' => 'pindah_kamar',
            'to_room_id' => $this->roomB->id,
        ]);
    }

    public function test_livewire_mutation_sheet_drag_and_drop_actions(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus DnD Livewire Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['assigned_to' => $this->musyrifUser->id]);

        $this->actingAs($this->musyrifUser);

        // 1. Test moveSantri to roomB
        Livewire::test(\App\Livewire\Kepengasuhan\CensusMutationSheet::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->call('moveSantri', $this->santri1->id, 'room', $this->roomB->id)
        ->assertHasNoErrors();

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri1->id,
            'action' => 'pindah_kamar',
            'to_room_id' => $this->roomB->id,
        ]);

        // 2. Test moveSantri to boyong
        Livewire::test(\App\Livewire\Kepengasuhan\CensusMutationSheet::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->call('moveSantri', $this->santri1->id, 'boyong')
        ->assertHasNoErrors();

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri1->id,
            'action' => 'ubah_status',
            'new_enrollment_status' => 'keluar_resmi',
        ]);

        // 3. Test resetSantri
        Livewire::test(\App\Livewire\Kepengasuhan\CensusMutationSheet::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->call('resetSantri', $this->santri1->id)
        ->assertHasNoErrors();

        $this->assertDatabaseHas('census_mutation_entries', [
            'campaign_id' => $campaign->id,
            'person_id' => $this->santri1->id,
            'action' => 'tetap',
        ]);
    }

    public function test_livewire_mutation_review_can_approve_mutations(): void
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Review Livewire Test',
            'month' => 11,
            'year' => 2026,
            'census_type' => 'mutasi',
            'dormitory_ids' => [$this->dormPutra->id],
        ], $this->sekretarisPutra->id);

        // Submit dormitory first
        $this->mutationService->saveEntry(
            $campaign->id,
            $this->dormPutra->id,
            $this->santri1->id,
            [
                'from_room_id' => $this->roomA->id,
                'action' => 'pindah_kamar',
                'to_room_id' => $this->roomB->id,
            ],
            $this->musyrifUser->id
        );

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();
        $cd->update(['status' => 'submitted']);

        $this->actingAs($this->sekretarisPutra);

        Livewire::test(\App\Livewire\Kepengasuhan\CensusMutationReview::class, [
            'campaign' => $campaign->id,
            'dormitory' => $this->dormPutra->id,
        ])
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Kamar A.101')
        ->assertSee('Kamar A.102')
        ->call('approveAndApply')
        ->assertHasNoErrors();

        // Check assignment updated
        $activeAssignment = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('is_active', true)
            ->first();
        $this->assertEquals($this->roomB->id, $activeAssignment->room_id);
    }
}
