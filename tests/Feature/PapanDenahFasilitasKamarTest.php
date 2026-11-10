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
use App\Livewire\Kepengasuhan\PapanDenahFasilitasKamar;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PapanDenahFasilitasKamarTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unauthorizedUser;
    protected Dormitory $dormPutra;
    protected Dormitory $dormPutri;
    protected Room $roomA1;
    protected Room $roomA2;
    protected Room $roomP1;
    protected Person $santri1;
    protected Person $santri2;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'manage-sensus-v3',
            'manage-asrama',
            'manage-kamar',
            'input-census-v3',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->adminUser = User::factory()->create(['name' => 'Admin Pusat']);
        $this->adminUser->assignRole($adminRole);

        $this->unauthorizedUser = User::factory()->create(['name' => 'Unauthorized User']);

        $this->dormPutra = Dormitory::create([
            'name'      => 'Komplek A - Sunan Ampel',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $this->dormPutri = Dormitory::create([
            'name'      => 'Komplek Putri - Fatimah',
            'gender'    => 'P',
            'is_active' => true,
        ]);

        $this->roomA1 = Room::create([
            'dormitory_id' => $this->dormPutra->id,
            'name'         => 'Kamar A-01',
            'capacity'     => 8,
            'is_active'    => true,
        ]);

        $this->roomA2 = Room::create([
            'dormitory_id' => $this->dormPutra->id,
            'name'         => 'Kamar A-02',
            'capacity'     => 8,
            'is_active'    => true,
        ]);

        $this->roomP1 = Room::create([
            'dormitory_id' => $this->dormPutri->id,
            'name'         => 'Kamar P-01',
            'capacity'     => 10,
            'is_active'    => true,
        ]);

        $org = \App\Modules\Core\Models\Organization::create([
            'name' => 'Pondok Utama',
            'slug' => 'pondok-utama',
            'type' => 'pondok',
        ]);

        $this->santri1 = Person::create([
            'name'   => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'person_id'         => $this->santri1->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
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

        $this->santri2 = Person::create([
            'name'   => 'Muhammad Zaidan',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'person_id'         => $this->santri2->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
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
    }

    public function test_can_render_papan_denah_fasilitas_kamar(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->assertStatus(200)
            ->assertSet('selectedRoomId', $this->roomA1->id)
            ->assertSee('Kamar: Kamar A-01')
            ->assertSee('Ahmad Fauzi')
            ->assertSee('Muhammad Zaidan');

        $this->assertNotEmpty($test->get('furnitureUnits'));
    }

    public function test_admin_can_build_custom_locker_layout_with_rows(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openBuilderModal')
            ->assertSet('showBuilderModal', true)
            ->call('applyBuilderPreset', '2_atas_4_bawah')
            ->set('builderName', 'Lemari Pakaian Khusus')
            ->call('saveBuilderModal')
            ->assertSet('showBuilderModal', false);

        $this->roomA1->refresh();
        $layout = $this->roomA1->furniture_layout;
        $this->assertNotEmpty($layout);

        $wardrobe = collect($layout)->firstWhere('name', 'Lemari Pakaian Khusus');
        $this->assertNotNull($wardrobe);
        $this->assertCount(2, $wardrobe['rows']);
        $this->assertCount(2, $wardrobe['rows'][0]['slots']); // 2 atas
        $this->assertCount(4, $wardrobe['rows'][1]['slots']); // 4 bawah

        // Check lockers created in DB
        $this->assertTrue(Locker::where('room_id', $this->roomA1->id)->where('code', 'B-01')->exists());
        $this->assertTrue(Locker::where('room_id', $this->roomA1->id)->where('code', 'B-02')->exists());
        $this->assertTrue(Locker::where('room_id', $this->roomA1->id)->where('code', 'B-06')->exists());
    }

    public function test_admin_can_build_pigeon_hole_kitab_rack(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openBuilderModal')
            ->call('applyBuilderPreset', '5x5_kitab')
            ->call('saveBuilderModal');

        $this->roomA1->refresh();
        $kitabRack = collect($this->roomA1->furniture_layout)->firstWhere('type', 'kitab');
        $this->assertNotNull($kitabRack);
        $this->assertCount(5, $kitabRack['rows']);
        $this->assertCount(5, $kitabRack['rows'][0]['slots']);

        // Check lockers created in DB
        $this->assertTrue(Locker::where('room_id', $this->roomA1->id)->where('code', 'K-01')->exists());
        $this->assertTrue(Locker::where('room_id', $this->roomA1->id)->where('code', 'K-25')->exists());
    }

    public function test_can_assign_and_unassign_santri_to_locker_slot(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openAssignModal', 'B-01', 'baju', null)
            ->assertSet('showAssignModal', true)
            ->set('assignSelectedPersonId', $this->santri1->id)
            ->call('saveAssignModal')
            ->assertSet('showAssignModal', false);

        $locker = Locker::where('room_id', $this->roomA1->id)->where('code', 'B-01')->first();
        $this->assertNotNull($locker);
        $this->assertEquals($this->santri1->id, $locker->assigned_person_id);

        // Test unassign
        $test->call('unassignSlot', 'B-01');
        $locker->refresh();
        $this->assertNull($locker->assigned_person_id);
    }

    public function test_quick_assign_from_drag_drop(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('quickAssignFromDrag', 'B-02', $this->santri2->id);

        $locker = Locker::where('room_id', $this->roomA1->id)->where('code', 'B-02')->first();
        $this->assertNotNull($locker);
        $this->assertEquals($this->santri2->id, $locker->assigned_person_id);
    }

    public function test_can_update_locker_condition_and_record_notes(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openConditionModal', 'slot', null, 'B-01', 'baik', '')
            ->assertSet('showConditionModal', true)
            ->call('setQuickCondition', 'rusak_ringan')
            ->set('conditionNotes', 'Engsel pintu bawah copot')
            ->call('saveConditionModal')
            ->assertSet('showConditionModal', false);

        $locker = Locker::where('room_id', $this->roomA1->id)->where('code', 'B-01')->first();
        $this->assertNotNull($locker);
        $this->assertEquals('rusak_ringan', $locker->condition);
        $this->assertEquals('Engsel pintu bawah copot', $locker->notes);
    }

    public function test_can_clone_layout_to_another_room(): void
    {
        $this->actingAs($this->adminUser);

        // Setup custom layout on roomA1
        Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openBuilderModal')
            ->call('applyBuilderPreset', '2_atas_4_bawah')
            ->call('saveBuilderModal');

        // Clone to roomA2
        Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openCloneModal')
            ->assertSet('showCloneModal', true)
            ->call('toggleCloneTarget', $this->roomA2->id)
            ->call('executeCloneLayout')
            ->assertSet('showCloneModal', false);

        $this->roomA2->refresh();
        $this->assertNotEmpty($this->roomA2->furniture_layout);

        // Check lockers created in room A2
        $this->assertTrue(Locker::where('room_id', $this->roomA2->id)->where('code', 'B-01')->exists());
        $this->assertTrue(Locker::where('room_id', $this->roomA2->id)->where('code', 'B-06')->exists());
    }

    public function test_can_manage_room_general_facilities(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openBuilderModal')
            ->call('applyBuilderPreset', 'fasilitas_umum')
            ->set('builderName', 'Kipas Angin Dinding')
            ->set('builderSingleNotes', 'Merk Cosmos')
            ->call('saveBuilderModal');

        $this->roomA1->refresh();
        $facility = collect($this->roomA1->furniture_layout)->firstWhere('name', 'Kipas Angin Dinding');
        $this->assertNotNull($facility);
        $this->assertEquals('single', $facility['category']);

        // Update its condition
        $test->call('openConditionModal', 'single', $facility['id'], null, 'baik', '')
            ->call('setQuickCondition', 'rusak_berat')
            ->set('conditionNotes', 'Baling-baling patah')
            ->call('saveConditionModal');

        $this->roomA1->refresh();
        $updated = collect($this->roomA1->furniture_layout)->firstWhere('id', $facility['id']);
        $this->assertEquals('rusak_berat', $updated['condition']);
        $this->assertEquals('Baling-baling patah', $updated['notes']);
    }

    public function test_mixed_lockers_automatically_sanitized_and_split_into_separate_units(): void
    {
        $this->actingAs($this->adminUser);

        // Simulate a room layout that has mixed 'B-' and 'K-' locker codes in a single unit
        $mixedUnit = [
            'id'       => 'unit_mixed_1',
            'type'     => 'baju',
            'category' => 'grid',
            'name'     => 'Lemari Pakaian Santri',
            'rows'     => [
                [
                    'row_num' => 1,
                    'slots'   => [
                        ['code' => 'B-01', 'is_active' => true],
                        ['code' => 'B-02', 'is_active' => true],
                        ['code' => 'K-01', 'is_active' => true],
                        ['code' => 'K-02', 'is_active' => true],
                    ],
                ],
            ],
        ];

        $this->roomA1->update(['furniture_layout' => [$mixedUnit]]);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        $this->roomA1->refresh();
        $layout = $this->roomA1->furniture_layout;

        // Must be split into 2 separate units: Lemari Pakaian & Rak Kitab
        $this->assertCount(2, $layout);
        $wardrobeUnit = collect($layout)->firstWhere('type', 'baju');
        $kitabUnit = collect($layout)->firstWhere('type', 'kitab');

        $this->assertNotNull($wardrobeUnit);
        $this->assertNotNull($kitabUnit);

        $wardrobeCodes = collect($wardrobeUnit['rows'])->flatMap(fn($r) => collect($r['slots'])->pluck('code'))->all();
        $kitabCodes = collect($kitabUnit['rows'])->flatMap(fn($r) => collect($r['slots'])->pluck('code'))->all();

        $this->assertEquals(['B-01', 'B-02'], $wardrobeCodes);
        $this->assertEquals(['K-01', 'K-02'], $kitabCodes);
    }

    public function test_selective_clone_layout_with_append_and_overwrite(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Create a wardrobe unit and a book rack unit in roomA1
        $unitWardrobe = [
            'id'       => 'unit_wardrobe_source',
            'type'     => 'baju',
            'category' => 'grid',
            'name'     => 'Lemari Pakaian A1',
            'rows'     => [
                ['row_num' => 1, 'slots' => [['code' => 'B-01', 'is_active' => true]]],
            ],
        ];
        $unitKitab = [
            'id'       => 'unit_kitab_source',
            'type'     => 'kitab',
            'category' => 'grid',
            'name'     => 'Rak Kitab A1',
            'rows'     => [
                ['row_num' => 1, 'slots' => [['code' => 'K-01', 'is_active' => true]]],
            ],
        ];
        $this->roomA1->update(['furniture_layout' => [$unitWardrobe, $unitKitab]]);

        // Give roomA2 its own existing wardrobe
        $targetExistingWardrobe = [
            'id'       => 'unit_target_existing',
            'type'     => 'baju',
            'category' => 'grid',
            'name'     => 'Lemari Asli A2',
            'rows'     => [
                ['row_num' => 1, 'slots' => [['code' => 'B-99', 'is_active' => true]]],
            ],
        ];
        $this->roomA2->update(['furniture_layout' => [$targetExistingWardrobe]]);

        // 2. Clone ONLY the book rack ('unit_kitab_source') to roomA2 with mode 'append'
        Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openCloneModal')
            ->set('cloneMode', 'append')
            ->set('cloneSelectedUnitIds', ['unit_kitab_source'])
            ->call('toggleCloneTarget', $this->roomA2->id)
            ->call('executeCloneLayout');

        $this->roomA2->refresh();
        $targetLayout = $this->roomA2->furniture_layout;

        // In append mode, roomA2 should keep its original wardrobe and have the new cloned kitab unit appended
        $this->assertCount(2, $targetLayout);
        $this->assertTrue(collect($targetLayout)->contains('name', 'Lemari Asli A2'));
        $this->assertTrue(collect($targetLayout)->contains('name', 'Rak Kitab A1'));

        // 3. Clone ONLY the wardrobe with mode 'overwrite'
        Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openCloneModal')
            ->set('cloneMode', 'overwrite')
            ->set('cloneSelectedUnitIds', ['unit_wardrobe_source'])
            ->call('toggleCloneTarget', $this->roomA2->id)
            ->call('executeCloneLayout');

        $this->roomA2->refresh();
        $overwrittenLayout = $this->roomA2->furniture_layout;

        // In overwrite mode, roomA2 should now ONLY have the cloned wardrobe unit
        $this->assertCount(1, $overwrittenLayout);
        $this->assertEquals('Lemari Pakaian A1', $overwrittenLayout[0]['name']);
    }

    public function test_occupant_filters_and_status_badges_for_wardrobe_and_bookshelf(): void
    {
        $this->actingAs($this->adminUser);

        // santri1 gets both wardrobe and book rack
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'K-01',
            'type'               => 'kitab',
            'assigned_person_id' => $this->santri1->id,
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        // santri2 has no wardrobe and no book rack

        // 1. Check default 'all' filter
        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->assertSee('Lemari: B-01')
            ->assertSee('Rak: K-01')
            ->assertSee('⚠️ Belum Lemari')
            ->assertSee('⚠️ Belum Rak')
            ->assertSee('1/2 Lengkap');

        $stats = $test->viewData('occupantStats');
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['no_baju']);
        $this->assertEquals(1, $stats['no_kitab']);
        $this->assertEquals(1, $stats['complete']);

        $occupantsAll = $test->viewData('occupantsList');
        $this->assertCount(2, $occupantsAll);

        // 2. Filter 'no_baju' (only santri2 should appear)
        $test->call('setOccupantFilter', 'no_baju');
        $occupantsNoBaju = $test->viewData('occupantsList');
        $this->assertCount(1, $occupantsNoBaju);
        $this->assertEquals($this->santri2->id, $occupantsNoBaju->first()['id']);

        // 3. Filter 'no_kitab' (only santri2 should appear)
        $test->call('setOccupantFilter', 'no_kitab');
        $occupantsNoKitab = $test->viewData('occupantsList');
        $this->assertCount(1, $occupantsNoKitab);
        $this->assertEquals($this->santri2->id, $occupantsNoKitab->first()['id']);

        // 4. Filter 'complete' (only santri1 should appear)
        $test->call('setOccupantFilter', 'complete');
        $occupantsComplete = $test->viewData('occupantsList');
        $this->assertCount(1, $occupantsComplete);
        $this->assertEquals($this->santri1->id, $occupantsComplete->first()['id']);

        // 5. Search query filter
        $test->call('setOccupantFilter', 'all')
            ->set('occupantSearch', 'Zaidan');
        $occupantsSearch = $test->viewData('occupantsList');
        $this->assertCount(1, $occupantsSearch);
        $this->assertEquals($this->santri2->id, $occupantsSearch->first()['id']);

        // 6. Quick assign santri2 to a wardrobe slot via drag & drop
        $test->call('quickAssignFromDrag', 'B-02', $this->santri2->id);

        $lockerB2 = Locker::where('room_id', $this->roomA1->id)->where('code', 'B-02')->first();
        $this->assertNotNull($lockerB2);
        $this->assertEquals($this->santri2->id, $lockerB2->assigned_person_id);

        // Under 'no_baju' filter, santri2 should now no longer appear, showing celebratory empty state
        $test->set('occupantSearch', '')
            ->call('setOccupantFilter', 'no_baju')
            ->assertSee('Semua Sudah Dapat Lemari');

        $this->assertCount(0, $test->viewData('occupantsList'));
    }

    public function test_room_vacancy_filter_and_active_unit_tab_switching(): void
    {
        $this->actingAs($this->adminUser);

        // Setup custom layout with wardrobe and kitab rack
        $this->roomA1->update([
            'furniture_layout' => [
                ['id' => 'u1', 'type' => 'baju', 'category' => 'compartment', 'name' => 'Lemari A1', 'rows' => []],
                ['id' => 'u2', 'type' => 'kitab', 'category' => 'compartment', 'name' => 'Rak A1', 'rows' => []],
            ],
        ]);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        // Test default activeUnitTab 'all'
        $this->assertCount(2, $test->viewData('visibleUnits'));

        // Test activeUnitTab 'baju'
        $test->call('setActiveUnitTab', 'baju');
        $visibleBaju = $test->viewData('visibleUnits');
        $this->assertCount(1, $visibleBaju);
        $this->assertEquals('u1', $visibleBaju[0]['id']);

        // Test activeUnitTab 'kitab'
        $test->call('setActiveUnitTab', 'kitab');
        $visibleKitab = $test->viewData('visibleUnits');
        $this->assertCount(1, $visibleKitab);
        $this->assertEquals('u2', $visibleKitab[0]['id']);

        // Test roomFilter: roomA1 has vacancy (2 occupants / 8 capacity)
        $test->call('setRoomFilter', 'has_vacancy');
        $dorms = $test->viewData('dormitories');
        $dormA = $dorms->firstWhere('id', $this->dormPutra->id);
        $this->assertTrue($dormA->rooms->contains('id', $this->roomA1->id));

        // Test roomFilter: 'full' should exclude roomA1
        $test->call('setRoomFilter', 'full');
        $dormsFull = $test->viewData('dormitories');
        $dormAFull = $dormsFull->firstWhere('id', $this->dormPutra->id);
        $this->assertFalse($dormAFull->rooms->contains('id', $this->roomA1->id));
    }

    public function test_builder_multi_room_distribution_on_new_facility_creation(): void
    {
        $this->actingAs($this->adminUser);

        // Room A1 & Room A2 initially have empty layouts
        $this->roomA1->update(['furniture_layout' => []]);
        $this->roomA2->update(['furniture_layout' => []]);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id)
            ->call('openBuilderModal')
            ->assertSet('builderTargetRoomIds', [$this->roomA1->id]);

        // Toggle Room A2 into target list
        $test->call('toggleBuilderTargetRoom', $this->roomA2->id)
            ->assertSet('builderTargetRoomIds', [$this->roomA1->id, $this->roomA2->id]);

        // Configure a new wardrobe 2 atas + 4 bawah
        $test->set('builderName', 'Lemari Seragam Asrama')
            ->set('builderType', 'baju')
            ->call('applyBuilderPreset', '2_atas_4_bawah')
            ->call('saveBuilderModal');

        // Verify Room A1 has the new unit appended
        $this->roomA1->refresh();
        $this->assertCount(1, $this->roomA1->furniture_layout);
        $newUnitA1 = collect($this->roomA1->furniture_layout)->firstWhere('name', 'Lemari Seragam Asrama');
        $this->assertNotNull($newUnitA1);
        $unitA1Id = $newUnitA1['id'];

        // Verify Room A2 ALSO received the new unit
        $this->roomA2->refresh();
        $this->assertCount(1, $this->roomA2->furniture_layout);
        $this->assertEquals('Lemari Seragam Asrama', $this->roomA2->furniture_layout[0]['name']);
        $unitA2Id = $this->roomA2->furniture_layout[0]['id'];

        // Crucial: Unit IDs must be independent UUIDs per room
        $this->assertNotEquals($unitA1Id, $unitA2Id);

        // Test Skenario A: When editing an existing unit, target rooms is disabled
        $test->call('openBuilderModal', $unitA1Id)
            ->assertSet('builderTargetRoomIds', []);
    }

    public function test_special_allocation_for_cash_treasurer_and_non_system_supervisor(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        // 1. Alokasikan B-01 untuk Kas / Uang Bendahara (Inventaris)
        $test->call('openAssignModal', 'B-01', 'baju', null, 'inventaris', 'Kas / Uang Bendahara')
            ->assertSet('assignAllocationType', 'inventaris')
            ->assertSet('assignCustomHolderName', 'Kas / Uang Bendahara')
            ->call('saveAssignModal');

        // Verifikasi di DB
        $lockerB1 = \App\Modules\Kepengasuhan\Models\Locker::where('room_id', $this->roomA1->id)
            ->where('code', 'B-01')
            ->first();

        $this->assertNotNull($lockerB1);
        $this->assertEquals('inventaris', $lockerB1->allocation_type);
        $this->assertEquals('Kas / Uang Bendahara', $lockerB1->custom_holder_name);
        $this->assertNull($lockerB1->assigned_person_id);

        // 2. Alokasikan B-02 untuk Pengurus / Non-Sistem (Ust. Rahmat)
        $test->call('openAssignModal', 'B-02', 'baju', null, 'external', 'Ust. Rahmat (Pengurus Komplek)')
            ->assertSet('assignAllocationType', 'external')
            ->set('assignCustomHolderName', 'Ust. Rahmat (Pengurus Komplek)')
            ->call('saveAssignModal');

        $lockerB2 = \App\Modules\Kepengasuhan\Models\Locker::where('room_id', $this->roomA1->id)
            ->where('code', 'B-02')
            ->first();

        $this->assertNotNull($lockerB2);
        $this->assertEquals('external', $lockerB2->allocation_type);
        $this->assertEquals('Ust. Rahmat (Pengurus Komplek)', $lockerB2->custom_holder_name);
        $this->assertNull($lockerB2->assigned_person_id);

        // 3. Verifikasi keduanya muncul di viewData (furnitureUnits)
        $units = $test->viewData('furnitureUnits');
        $bajuUnit = collect($units)->firstWhere('type', 'baju');
        $slots = collect($bajuUnit['rows'])->flatMap(fn($r) => $r['slots'])->keyBy('code');

        $this->assertEquals('inventaris', $slots['B-01']['allocation_type']);
        $this->assertEquals('Kas / Uang Bendahara', $slots['B-01']['custom_holder_name']);

        $this->assertEquals('external', $slots['B-02']['allocation_type']);
        $this->assertEquals('Ust. Rahmat (Pengurus Komplek)', $slots['B-02']['custom_holder_name']);

        // 4. Kosongkan kembali B-01
        $test->call('unassignSlot', 'B-01');
        $lockerB1->refresh();
        $this->assertEquals('santri', $lockerB1->allocation_type);
        $this->assertNull($lockerB1->custom_holder_name);
        $this->assertNull($lockerB1->assigned_person_id);
    }

    public function test_toggle_highlight_vacant_slots_and_glowing_state(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        $units = $test->viewData('furnitureUnits');
        $this->assertNotEmpty($units);
        $firstUnit = $units[0];
        $unitId = $firstUnit['id'];

        // Initially no units highlighted
        $test->assertSet('highlightVacantUnitIds', [])
            ->assertSet('highlightAllVacant', false)
            ->assertSee('Cek Kosong')
            ->assertDontSee('SIAP PAKAI');

        // 1. Toggle highlight for specific unit
        $test->call('toggleHighlightVacant', $unitId)
            ->assertSet('highlightVacantUnitIds', [$unitId])
            ->assertSee('Menyala')
            ->assertSee('SIAP PAKAI')
            ->assertSee('+ Pasang Santri');

        // Toggle again to turn off
        $test->call('toggleHighlightVacant', $unitId)
            ->assertSet('highlightVacantUnitIds', [])
            ->assertDontSee('SIAP PAKAI');

        // 2. Toggle global highlight
        $test->call('toggleHighlightAllVacant')
            ->assertSet('highlightAllVacant', true)
            ->assertSee('Semua Kosong Menyala')
            ->assertSee('SIAP PAKAI');

        // Switching room resets highlight state
        $test->call('selectRoom', $this->roomA2->id)
            ->assertSet('highlightVacantUnitIds', [])
            ->assertSet('highlightAllVacant', false);
    }

    public function test_delete_furniture_unit_releases_occupants_and_shows_impact_modal(): void
    {
        $this->actingAs($this->adminUser);

        // Assign santri1 to B-01 in Room A1
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'allocation_type'    => 'santri',
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        $units = $test->viewData('furnitureUnits');
        $wardrobeUnit = collect($units)->firstWhere('type', 'baju');
        $this->assertNotNull($wardrobeUnit);
        $unitId = $wardrobeUnit['id'];

        // Initially santri1 has baju locker
        $occupantsBefore = $test->viewData('occupantsList');
        $santri1Before = collect($occupantsBefore)->firstWhere('id', $this->santri1->id);
        $this->assertTrue($santri1Before['has_baju']);

        // 1. Open confirmation modal for deleting the wardrobe unit
        $test->call('confirmDeleteFurnitureUnit', $unitId)
            ->assertSet('showDeleteUnitModal', true)
            ->assertSet('deleteUnitId', $unitId)
            ->assertSee('Hapus Unit Fasilitas?')
            ->assertSee('Ada 1 Penghuni di Lemari Ini')
            ->assertSee($this->santri1->name)
            ->assertSee('B-01');

        // Cancel deletion
        $test->call('cancelDeleteFurnitureUnit')
            ->assertSet('showDeleteUnitModal', false)
            ->assertSet('deleteUnitId', null);

        // 2. Open again and execute deletion
        $test->call('confirmDeleteFurnitureUnit', $unitId)
            ->call('executeDeleteFurnitureUnit')
            ->assertSet('showDeleteUnitModal', false);

        // Verify unit is removed from room layout
        $this->roomA1->refresh();
        $newLayout = $this->roomA1->furniture_layout;
        $this->assertNull(collect($newLayout)->firstWhere('id', $unitId));

        // Verify locker records for B-01 were deleted from DB
        $this->assertDatabaseMissing('lockers', [
            'room_id' => $this->roomA1->id,
            'code'    => 'B-01',
        ]);

        // Verify santri1 is still resident of the room, but now has_baju = false (Belum Lemari)
        $occupantsAfter = $test->viewData('occupantsList');
        $santri1After = collect($occupantsAfter)->firstWhere('id', $this->santri1->id);
        $this->assertNotNull($santri1After);
        $this->assertFalse($santri1After['has_baju']);
    }

    public function test_unassign_slot_confirmation_modal_and_execution(): void
    {
        $this->actingAs($this->adminUser);

        // Assign santri1 to B-01
        Locker::create([
            'id'                 => (string) \Illuminate\Support\Str::uuid(),
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'type'               => 'baju',
            'assigned_person_id' => $this->santri1->id,
            'allocation_type'    => 'santri',
            'condition'          => 'baik',
            'is_active'          => true,
        ]);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->call('selectRoom', $this->roomA1->id);

        // 1. Open confirmation modal for unassigning slot B-01
        $test->call('confirmUnassignSlot', 'B-01')
            ->assertSet('showUnassignModal', true)
            ->assertSet('unassignSlotCode', 'B-01')
            ->assertSee('Lepas Penghuni Loker?')
            ->assertSee($this->santri1->name)
            ->assertSee('B-01');

        // Cancel unassign
        $test->call('cancelUnassignSlot')
            ->assertSet('showUnassignModal', false)
            ->assertSet('unassignSlotCode', null);

        // 2. Open again and execute unassign
        $test->call('confirmUnassignSlot', 'B-01')
            ->call('executeUnassignSlot')
            ->assertSet('showUnassignModal', false);

        // Verify DB record has assigned_person_id = null
        $locker = Locker::where('room_id', $this->roomA1->id)->where('code', 'B-01')->first();
        $this->assertNotNull($locker);
        $this->assertNull($locker->assigned_person_id);

        // Verify santri1 now has_baju = false
        $occupants = $test->viewData('occupantsList');
        $santri1 = collect($occupants)->firstWhere('id', $this->santri1->id);
        $this->assertFalse($santri1['has_baju']);
    }

    public function test_quick_room_stepper_and_picker_navigation(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(PapanDenahFasilitasKamar::class)
            ->assertStatus(200)
            ->assertSet('selectedRoomId', $this->roomA1->id)
            ->assertSee('Sebelumnya')
            ->assertSee('Berikutnya')
            ->assertSee('Ganti Kamar')
            ->assertSee('1/2');

        // Step next: roomA1 -> roomA2
        $test->call('goToNextRoom')
            ->assertSet('selectedRoomId', $this->roomA2->id)
            ->assertSee('Kamar: Kamar A-02')
            ->assertSee('2/2');

        // Step next again: loops back to roomA1
        $test->call('goToNextRoom')
            ->assertSet('selectedRoomId', $this->roomA1->id)
            ->assertSee('Kamar: Kamar A-01');

        // Step previous: loops to roomA2
        $test->call('goToPreviousRoom')
            ->assertSet('selectedRoomId', $this->roomA2->id)
            ->assertSee('Kamar: Kamar A-02');

        // Direct select room
        $test->call('selectRoom', $this->roomA1->id)
            ->assertSet('selectedRoomId', $this->roomA1->id);

        $viewData = $test->viewData('availableRoomsList');
        $this->assertCount(2, $viewData);
        $this->assertEquals($this->roomA1->id, $viewData[0]['id']);
        $this->assertEquals($this->roomA2->id, $viewData[1]['id']);
    }

    public function test_unauthorized_access_denied(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(PapanDenahFasilitasKamar::class)
            ->assertStatus(403);
    }
}
