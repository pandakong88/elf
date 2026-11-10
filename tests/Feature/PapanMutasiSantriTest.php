<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Livewire\Kepengasuhan\PapanMutasiSantri;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PapanMutasiSantriTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $sekretarisPutra;
    protected User $sekretarisPutri;
    protected User $unauthorizedUser;

    protected Dormitory $dormA;
    protected Dormitory $dormB;
    protected Dormitory $dormPutri;
    protected Room $roomA1;
    protected Room $roomB1;
    protected Room $roomP1;

    protected Person $santri1;
    protected Person $santri2;
    protected Person $santriPutri;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'manage-sensus-v3',
            'manage-asrama',
            'manage-kamar',
            'input-census-v3',
            'change-enrollment-status',
            'change-presence-status',
            'create-person',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $putraRole = Role::firstOrCreate(['name' => 'sekretaris-putra', 'guard_name' => 'web']);
        $putraRole->syncPermissions(['manage-sensus-v3', 'manage-asrama', 'manage-kamar', 'input-census-v3', 'change-enrollment-status']);

        $putriRole = Role::firstOrCreate(['name' => 'sekretaris-putri', 'guard_name' => 'web']);
        $putriRole->syncPermissions(['manage-sensus-v3', 'manage-asrama', 'manage-kamar', 'input-census-v3', 'change-enrollment-status']);

        $this->adminUser = User::factory()->create(['name' => 'Admin Pusat']);
        $this->adminUser->assignRole($adminRole);

        $this->sekretarisPutra = User::factory()->create(['name' => 'Sekretaris Putra']);
        $this->sekretarisPutra->assignRole($putraRole);

        $this->sekretarisPutri = User::factory()->create(['name' => 'Sekretaris Putri']);
        $this->sekretarisPutri->assignRole($putriRole);

        $this->unauthorizedUser = User::factory()->create(['name' => 'Unauthorized User']);

        // Dormitories: Komplek A (L), Komplek B (L), Komplek C (P)
        $this->dormA = Dormitory::create([
            'name'      => 'Komplek A - Sunan Ampel',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $this->dormB = Dormitory::create([
            'name'      => 'Komplek B - Sunan Bonang',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $this->dormPutri = Dormitory::create([
            'name'      => 'Komplek Putri - Fatimah',
            'gender'    => 'P',
            'is_active' => true,
        ]);

        // Rooms
        $this->roomA1 = Room::create([
            'dormitory_id' => $this->dormA->id,
            'name'         => 'Kamar A-01',
            'capacity'     => 10,
            'is_active'    => true,
        ]);

        $this->roomB1 = Room::create([
            'dormitory_id' => $this->dormB->id,
            'name'         => 'Kamar B-01',
            'capacity'     => 8,
            'is_active'    => true,
        ]);

        $this->roomP1 = Room::create([
            'dormitory_id' => $this->dormPutri->id,
            'name'         => 'Kamar P-01',
            'capacity'     => 12,
            'is_active'    => true,
        ]);

        $org = \App\Modules\Core\Models\Organization::create([
            'name' => 'Pondok Utama',
            'slug' => 'pondok-utama',
            'type' => 'pondok',
        ]);

        // Santri 1 (in room A1)
        $this->santri1 = Person::create([
            'name'   => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'person_id'         => $this->santri1->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'is_active'         => true,
        ]);
        SantriProfile::create([
            'person_id' => $this->santri1->id,
            'nis'       => '1001',
        ]);
        RoomAssignment::create([
            'room_id'    => $this->roomA1->id,
            'person_id'  => $this->santri1->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        // Santri 2 (in room A1)
        $this->santri2 = Person::create([
            'name'   => 'Budi Prasetyo',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'person_id'         => $this->santri2->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'is_active'         => true,
        ]);
        SantriProfile::create([
            'person_id' => $this->santri2->id,
            'nis'       => '1002',
        ]);
        RoomAssignment::create([
            'room_id'    => $this->roomA1->id,
            'person_id'  => $this->santri2->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        // Santri Putri (in room P1)
        $this->santriPutri = Person::create([
            'name'   => 'Siti Aisyah',
            'gender' => 'P',
        ]);
        PersonRole::create([
            'person_id'         => $this->santriPutri->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'is_active'         => true,
        ]);
        SantriProfile::create([
            'person_id' => $this->santriPutri->id,
            'nis'       => '2001',
        ]);
        RoomAssignment::create([
            'room_id'    => $this->roomP1->id,
            'person_id'  => $this->santriPutri->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(PapanMutasiSantri::class)
            ->assertForbidden();
    }

    public function test_gender_scope_locked_for_sekretaris_putra_and_putri(): void
    {
        // Sekretaris Putra -> locked to 'L'
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            ->assertSet('genderScope', 'L')
            ->assertSet('isGenderLocked', true)
            ->call('switchGender', 'P')
            ->assertSet('genderScope', 'L'); // Still L because locked

        // Sekretaris Putri -> locked to 'P'
        $this->actingAs($this->sekretarisPutri);

        Livewire::test(PapanMutasiSantri::class)
            ->assertSet('genderScope', 'P')
            ->assertSet('isGenderLocked', true);

        // Super Admin -> unlocked, can switch
        $this->actingAs($this->adminUser);

        Livewire::test(PapanMutasiSantri::class)
            ->assertSet('genderScope', 'L')
            ->assertSet('isGenderLocked', false)
            ->call('switchGender', 'P')
            ->assertSet('genderScope', 'P');
    }

    public function test_livewire_renders_dormitories_and_rooms(): void
    {
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            ->assertSee('Komplek A - Sunan Ampel')
            ->assertSee('Komplek B - Sunan Bonang')
            ->assertSee('Kamar A-01')
            ->assertSee('Kamar B-01')
            ->assertSee('Ahmad Fauzi')
            ->assertSee('Budi Prasetyo')
            ->assertDontSee('Komplek Putri - Fatimah'); // Scoped to Putra
    }

    public function test_stage_cross_komplek_move_and_apply(): void
    {
        $this->actingAs($this->sekretarisPutra);

        // Move Ahmad Fauzi from Komplek A (Kamar A-01) to Komplek B (Kamar B-01)
        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'room', $this->roomB1->id)
            ->assertSet('stagedMoves.' . $this->santri1->id . '.target_room_id', $this->roomB1->id)
            ->assertSet('stagedMoves.' . $this->santri1->id . '.target_type', 'room')
            ->call('applyMutations')
            ->assertSet('stagedMoves', []);

        // Assert database updated
        $newAssignment = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('is_active', true)
            ->first();

        $this->assertNotNull($newAssignment);
        $this->assertEquals($this->roomB1->id, $newAssignment->room_id);

        // Old assignment is inactive
        $oldAssignment = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('room_id', $this->roomA1->id)
            ->first();
        $this->assertFalse((bool) $oldAssignment->is_active);
    }

    public function test_stage_move_to_santri_laju_and_apply(): void
    {
        $this->actingAs($this->sekretarisPutra);

        // Move Ahmad Fauzi to Santri Laju
        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'laju')
            ->assertSet('stagedMoves.' . $this->santri1->id . '.target_type', 'laju')
            ->call('applyMutations');

        // PersonRole presence_status should become laju
        $role = PersonRole::where('person_id', $this->santri1->id)->where('is_active', true)->first();
        $this->assertEquals('laju', $role->presence_status);

        // Active room assignment should be cleared
        $activeAssignment = RoomAssignment::where('person_id', $this->santri1->id)
            ->where('is_active', true)
            ->first();
        $this->assertNull($activeAssignment);

        // When board is re-rendered, santri1 MUST appear in dock_laju and NOT in dock_unassigned
        $test2 = Livewire::test(PapanMutasiSantri::class);
        $board2 = $test2->instance()->getBoardStructureProperty();
        $lajuIds = array_column($board2['dock_laju'], 'person_id');
        $unassignedIds = array_column($board2['dock_unassigned'], 'person_id');

        $this->assertContains($this->santri1->id, $lajuIds, 'Santri Laju harus muncul di Kotak Santri Laju');
        $this->assertNotContains($this->santri1->id, $unassignedIds, 'Santri Laju TIDAK BOLEH muncul di Kotak Tanpa Kamar');

        // Test placing laju santri back into a room
        $test2->call('stageMove', $this->santri1->id, 'room', $this->roomA1->id)
            ->call('applyMutations');

        $role->refresh();
        $this->assertEquals('mukim', $role->presence_status, 'Santri Laju yang diberi kamar otomatis menjadi Mukim');
        $this->assertTrue(RoomAssignment::where('person_id', $this->santri1->id)->where('room_id', $this->roomA1->id)->where('is_active', true)->exists());
    }

    public function test_stage_move_to_boyong_and_apply(): void
    {
        $this->actingAs($this->sekretarisPutra);

        // Move Budi Prasetyo to Boyong
        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri2->id, 'boyong')
            ->assertSet('stagedMoves.' . $this->santri2->id . '.target_type', 'boyong')
            ->call('applyMutations');

        // PersonRole enrollment_status should become keluar_resmi
        $role = PersonRole::where('person_id', $this->santri2->id)->first();
        $this->assertEquals('keluar_resmi', $role->enrollment_status);

        // Active room assignment should be cleared
        $activeAssignment = RoomAssignment::where('person_id', $this->santri2->id)
            ->where('is_active', true)
            ->first();
        $this->assertNull($activeAssignment);
    }

    public function test_unstage_and_reset_moves(): void
    {
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'room', $this->roomB1->id)
            ->call('stageMove', $this->santri2->id, 'laju')
            ->assertCount('stagedMoves', 2)
            ->call('unstageMove', $this->santri1->id)
            ->assertCount('stagedMoves', 1)
            ->call('resetAllMoves')
            ->assertSet('stagedMoves', []);
    }

    public function test_toggle_collapse_dorms(): void
    {
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            ->call('toggleDormCollapse', $this->dormA->id)
            ->assertSet('collapsedDorms', [$this->dormA->id])
            ->call('toggleDormCollapse', $this->dormA->id)
            ->assertSet('collapsedDorms', [])
            ->call('collapseAll')
            ->assertCount('collapsedDorms', 2)
            ->call('expandAll')
            ->assertSet('collapsedDorms', []);
    }

    public function test_review_modal_opens_and_closes(): void
    {
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            // Cannot open when stagedMoves is empty
            ->call('openReviewModal')
            ->assertSet('showReviewModal', false)
            // Stage move then open modal
            ->call('stageMove', $this->santri1->id, 'room', $this->roomB1->id)
            ->call('openReviewModal')
            ->assertSet('showReviewModal', true)
            // Close modal
            ->call('closeReviewModal')
            ->assertSet('showReviewModal', false)
            // Open and unstage until empty should auto-close modal
            ->call('openReviewModal')
            ->assertSet('showReviewModal', true)
            ->call('unstageMove', $this->santri1->id)
            ->assertSet('showReviewModal', false);
    }

    public function test_sort_order_occupants(): void
    {
        $this->actingAs($this->sekretarisPutra);

        $component = Livewire::test(PapanMutasiSantri::class)
            ->set('sortBy', 'name_desc');

        $board = $component->viewData('board');
        $roomA = $board['dormitories'][0]['rooms'][0];
        $names = array_column($roomA['occupants'], 'name');
        
        // Budi Prasetyo comes before Ahmad Fauzi in descending order
        $this->assertEquals(['Budi Prasetyo', 'Ahmad Fauzi'], $names);
    }

    public function test_class_hierarchy_sorting(): void
    {
        $this->actingAs($this->sekretarisPutra);

        $component = Livewire::test(PapanMutasiSantri::class);

        // Reflection to test protected getClassRank method
        $reflection = new \ReflectionClass($component->instance());
        $method = $reflection->getMethod('getClassRank');
        $method->setAccessible(true);

        $rankAwaliyah1 = $method->invoke($component->instance(), 'Awaliyah 1 (Pa)');
        $rankAwaliyah2 = $method->invoke($component->instance(), 'Awaliyah 2 (Pa)');
        $rankWustho1   = $method->invoke($component->instance(), 'Wustho 1 (Pa)');
        $rankUlya1     = $method->invoke($component->instance(), 'Ulya 1 (Pa)');
        $rankUlya2     = $method->invoke($component->instance(), 'Ulya 2 (Pa)');

        // Hierarchy rule: Awaliyah < Wustho < Ulya
        $this->assertLessThan($rankAwaliyah2, $rankAwaliyah1);
        $this->assertLessThan($rankWustho1, $rankAwaliyah2);
        $this->assertLessThan($rankUlya1, $rankWustho1);
        $this->assertLessThan($rankUlya2, $rankUlya1);
    }

    public function test_reactivate_boyong_santri(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Set santri1 to boyong
        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'boyong')
            ->call('applyMutations');

        $role = PersonRole::where('person_id', $this->santri1->id)->where('role_type', 'santri')->first();
        $this->assertEquals('keluar_resmi', $role->enrollment_status);
        $this->assertFalse($role->is_active);

        // 2. Test opening status modal in PetaSantriManager for boyong santri works now!
        Livewire::test(\App\Livewire\Kepengasuhan\PetaSantriManager::class)
            ->call('openStatusModal', $this->santri1->id)
            ->assertSet('showStatusModal', true)
            ->assertSet('statusSantriRoleId', $role->id)
            ->set('targetEnrollmentStatus', 'aktif')
            ->call('executeStatusChange')
            ->assertSet('showStatusModal', false);

        $role->refresh();
        $this->assertEquals('aktif', $role->enrollment_status);
        $this->assertTrue($role->is_active);
        $this->assertNull($role->left_at);
    }

    public function test_tanpa_keterangan_preserves_room_and_class_while_boyong_releases(): void
    {
        $this->actingAs($this->adminUser);

        // Ensure santri1 has active room assignment
        $activeRoom = RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->first();
        $this->assertNotNull($activeRoom);

        // Create and assign class for santri1
        $kelas = \App\Modules\Madrasah\Models\MadrasahKelas::create([
            'name'          => '1 Ula A',
            'jenjang'       => 'ula',
            'academic_year' => '2026/2027',
            'is_active'     => true,
        ]);
        MadrasahEnrollment::create([
            'person_id'     => $this->santri1->id,
            'kelas_id'      => $kelas->id,
            'academic_year' => '2026/2027',
            'is_active'     => true,
        ]);

        $role = PersonRole::where('person_id', $this->santri1->id)->where('role_type', 'santri')->first();

        // 1. Change status to tanpa_keterangan via PetaSantriManager
        Livewire::test(\App\Livewire\Kepengasuhan\PetaSantriManager::class)
            ->call('openStatusModal', $this->santri1->id)
            ->set('targetEnrollmentStatus', 'tanpa_keterangan')
            ->call('requestStatusChangeConfirm')
            ->assertSet('showConfirmModal', true)
            ->assertSet('confirmButtonColor', 'amber')
            ->call('executeStatusChange')
            ->assertSet('showStatusModal', false);

        $role->refresh();
        $this->assertEquals('tanpa_keterangan', $role->enrollment_status);
        $this->assertEquals('alpa', $role->presence_status);
        $this->assertTrue($role->is_active);

        // Room and Class MUST STILL BE ACTIVE!
        $this->assertTrue(RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());
        $this->assertTrue(MadrasahEnrollment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());

        // 2. Now officially change to boyong (keluar_resmi)
        Livewire::test(\App\Livewire\Kepengasuhan\PetaSantriManager::class)
            ->call('openStatusModal', $this->santri1->id)
            ->set('targetEnrollmentStatus', 'keluar_resmi')
            ->call('requestStatusChangeConfirm')
            ->assertSet('showConfirmModal', true)
            ->assertSet('confirmButtonColor', 'rose')
            ->call('executeStatusChange')
            ->assertSet('showStatusModal', false);

        $role->refresh();
        $this->assertEquals('keluar_resmi', $role->enrollment_status);
        $this->assertFalse($role->is_active);

        // Room and Class MUST NOW BE RELEASED (inactive)!
        $this->assertFalse(RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());
        $this->assertFalse(MadrasahEnrollment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());
    }

    public function test_papan_mutasi_dock_tanpa_keterangan_and_execution(): void
    {
        $this->actingAs($this->adminUser);

        // Ensure santri1 has active room
        $this->assertTrue(RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());

        $test = Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'tanpa_keterangan')
            ->assertSet('stagedMoves.' . $this->santri1->id . '.target_type', 'tanpa_keterangan');

        $board = $test->instance()->getBoardStructureProperty();
        $this->assertCount(1, $board['dock_tanpa_keterangan']);
        $this->assertEquals($this->santri1->id, $board['dock_tanpa_keterangan'][0]['person_id']);
        $this->assertEquals(1, $board['staged_breakdown']['tanpa_keterangan']);

        // Apply mutations
        $test->call('applyMutations')
            ->assertSet('stagedMoves', []);

        $role = PersonRole::where('person_id', $this->santri1->id)->where('role_type', 'santri')->first();
        $this->assertEquals('tanpa_keterangan', $role->enrollment_status);
        $this->assertEquals('alpa', $role->presence_status);
        $this->assertTrue($role->is_active);

        // Room assignment MUST STILL BE ACTIVE!
        $this->assertTrue(RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->exists());
    }

    public function test_open_new_santri_modal_and_register_mukim_santri(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanMutasiSantri::class)
            ->call('openNewSantriModal')
            ->assertSet('showNewSantriModal', true)
            ->assertSet('newSantriGender', 'L')
            ->set('newSantriName', 'Santri Baru Budi')
            ->set('newSantriGender', 'L')
            ->set('newSantriPresence', 'mukim')
            ->set('newSantriRoomId', $this->roomB1->id)
            ->call('registerNewSantri')
            ->assertSet('showNewSantriModal', false);

        $person = Person::where('name', 'Santri Baru Budi')->first();
        $this->assertNotNull($person);
        $this->assertEquals('L', $person->gender);

        $role = PersonRole::where('person_id', $person->id)->where('role_type', 'santri')->first();
        $this->assertNotNull($role);
        $this->assertEquals('aktif', $role->enrollment_status);
        $this->assertEquals('mukim', $role->presence_status);

        $roomAssignment = RoomAssignment::where('person_id', $person->id)->where('is_active', true)->first();
        $this->assertNotNull($roomAssignment);
        $this->assertEquals($this->roomB1->id, $roomAssignment->room_id);

        // Check that board reflects student in room B1
        $board = $test->instance()->getBoardStructureProperty();
        $b1Found = false;
        foreach ($board['dormitories'] as $dorm) {
            foreach ($dorm['rooms'] as $room) {
                if ($room['id'] === $this->roomB1->id) {
                    foreach ($room['occupants'] as $occ) {
                        if ($occ['person_id'] === $person->id) {
                            $b1Found = true;
                        }
                    }
                }
            }
        }
        $this->assertTrue($b1Found, 'Santri Baru Budi should be in room B1 occupants list');
    }

    public function test_register_laju_santri_from_papan_mutasi(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanMutasiSantri::class)
            ->call('openNewSantriModal')
            ->assertSet('showNewSantriModal', true)
            ->set('newSantriName', 'Santri Laju Hasan')
            ->set('newSantriGender', 'L')
            ->set('newSantriPresence', 'laju')
            ->call('registerNewSantri')
            ->assertSet('showNewSantriModal', false);

        $person = Person::where('name', 'Santri Laju Hasan')->first();
        $this->assertNotNull($person);

        $role = PersonRole::where('person_id', $person->id)->where('role_type', 'santri')->first();
        $this->assertNotNull($role);
        $this->assertEquals('laju', $role->presence_status);

        // Should appear in dock_laju
        $board = $test->instance()->getBoardStructureProperty();
        $lajuIds = array_column($board['dock_laju'], 'person_id');
        $this->assertContains($person->id, $lajuIds);
    }

    public function test_register_santri_forbidden_without_permission(): void
    {
        // Sekretaris putra does not have create-person permission
        $this->actingAs($this->sekretarisPutra);

        Livewire::test(PapanMutasiSantri::class)
            ->set('newSantriName', 'Santri Ilegal')
            ->set('newSantriGender', 'L')
            ->call('registerNewSantri');

        $this->assertFalse(Person::where('name', 'Santri Ilegal')->exists());
    }

    public function test_mutasi_sync_with_lockers_and_facilities(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Santri 1 starts in Room A1 with locker B-01
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        $this->assertDatabaseHas('lockers', [
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'assigned_person_id' => $this->santri1->id,
        ]);

        // 2. Pindah Kamar (Room A1 -> Room B1)
        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'room', $this->roomB1->id)
            ->call('applyMutations');

        // Santri 1 is now in Room B1
        $assignment = RoomAssignment::where('person_id', $this->santri1->id)->where('is_active', true)->first();
        $this->assertEquals($this->roomB1->id, $assignment->room_id);

        // Locker B-01 in old Room A1 must be released (assigned_person_id = null)
        $this->assertDatabaseHas('lockers', [
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'assigned_person_id' => null,
        ]);

        // 3. Assign a locker in Room B1, then mutate to LAJU
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomB1->id,
            'code'               => 'B-05',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'laju')
            ->call('applyMutations');

        // Santri 1's lockers must all be released
        $this->assertEquals(0, Locker::where('assigned_person_id', $this->santri1->id)->count());

        // 4. Move back to Room A1, assign B-02, then mutate to BOYONG
        app(\App\Modules\Kepengasuhan\Services\DormitoryService::class)->assignRoom(
            $this->roomA1->id,
            $this->santri1->id,
            now()->toDateString()
        );
        $b02 = Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-02',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'boyong')
            ->call('applyMutations');

        // Lockers must be released on boyong
        $this->assertEquals(0, Locker::where('assigned_person_id', $this->santri1->id)->count());

        // 5. Reactivate to Room A1, assign B-03, then mutate to TANPA KETERANGAN
        $role = PersonRole::where('person_id', $this->santri1->id)->where('role_type', 'santri')->first();
        app(\App\Modules\Kepengasuhan\Services\SantriStatusService::class)->changeEnrollmentStatus(
            $role->id,
            'aktif',
            $this->adminUser->id
        );
        app(\App\Modules\Kepengasuhan\Services\DormitoryService::class)->assignRoom(
            $this->roomA1->id,
            $this->santri1->id,
            now()->toDateString()
        );
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-03',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        Livewire::test(PapanMutasiSantri::class)
            ->call('stageMove', $this->santri1->id, 'tanpa_keterangan')
            ->call('applyMutations');

        // On 'tanpa_keterangan', locker MUST BE PRESERVED (tabayyun)!
        $this->assertDatabaseHas('lockers', [
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-03',
            'assigned_person_id' => $this->santri1->id,
        ]);
    }
}
