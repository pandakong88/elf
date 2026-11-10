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
use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusTemplateField;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use DomainException;

class CensusV3SekretarisTest extends TestCase
{
    use RefreshDatabase;

    protected User $sekretarisPutra;
    protected User $sekretarisPutri;
    protected User $musyrifUser;
    protected User $otherMusyrif;

    protected Dormitory $dormPutra;
    protected Dormitory $dormPutri;
    protected Room $roomPutra;
    protected Room $roomPutri;

    protected Person $santriPutra;
    protected Person $santriPutri;

    protected CensusTemplate $template;
    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;
    protected SantriStatusService $statusService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->censusService = app(CensusV3Service::class);
        $this->accessService = app(CensusV3AccessService::class);
        $this->statusService = app(SantriStatusService::class);

        // 1. Seed Roles & Permissions
        $permissions = [
            'manage-sensus-v3',
            'input-census-v3',
            'approve-census-v3',
            'manage-census-template',
            'change-enrollment-status',
        ];

        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }

        $rolePutra = Role::findOrCreate('sekretaris-putra', 'web');
        $rolePutra->syncPermissions($permissions);

        $rolePutri = Role::findOrCreate('sekretaris-putri', 'web');
        $rolePutri->syncPermissions($permissions);

        $roleMusyrif = Role::findOrCreate('musyrif', 'web');
        $roleMusyrif->syncPermissions(['input-census-v3']);

        // 2. Create Users
        $pSekPutra = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '1111111111111111',
            'name' => 'Sekretaris Putra User',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '1995-01-01',
        ]);
        $this->sekretarisPutra = User::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $pSekPutra->id,
            'name' => 'Sekretaris Putra',
            'username' => 'sek_putra',
            'email' => 'sek.putra@elvith.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $this->sekretarisPutra->assignRole('sekretaris-putra');

        $pSekPutri = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '2222222222222222',
            'name' => 'Sekretaris Putri User',
            'gender' => 'P',
            'birth_place' => 'Gresik',
            'birth_date' => '1996-01-01',
        ]);
        $this->sekretarisPutri = User::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $pSekPutri->id,
            'name' => 'Sekretaris Putri',
            'username' => 'sek_putri',
            'email' => 'sek.putri@elvith.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $this->sekretarisPutri->assignRole('sekretaris-putri');

        $pMusyrif = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '3333333333333333',
            'name' => 'Musyrif Asrama A',
            'gender' => 'L',
            'birth_place' => 'Lamongan',
            'birth_date' => '1998-01-01',
        ]);
        $this->musyrifUser = User::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $pMusyrif->id,
            'name' => 'Musyrif User',
            'username' => 'musyrif_a',
            'email' => 'musyrif.a@elvith.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $this->musyrifUser->assignRole('musyrif');

        $pOtherMusyrif = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '4444444444444444',
            'name' => 'Musyrif Asrama B',
            'gender' => 'L',
            'birth_place' => 'Tuban',
            'birth_date' => '1999-01-01',
        ]);
        $this->otherMusyrif = User::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $pOtherMusyrif->id,
            'name' => 'Musyrif B',
            'username' => 'musyrif_b',
            'email' => 'musyrif.b@elvith.id',
            'password' => 'secret123',
            'is_active' => true,
        ]);
        $this->otherMusyrif->assignRole('musyrif');

        // 3. Create Dormitories and Rooms
        $this->dormPutra = Dormitory::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Asrama Putra Al-Ikhlas',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $this->roomPutra = Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $this->dormPutra->id,
            'name' => 'Kamar Putra 01',
            'capacity' => 10,
            'is_active' => true,
        ]);

        $this->dormPutri = Dormitory::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Asrama Putri Khadijah',
            'gender' => 'P',
            'is_active' => true,
        ]);
        $this->roomPutri = Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $this->dormPutri->id,
            'name' => 'Kamar Putri 01',
            'capacity' => 10,
            'is_active' => true,
        ]);

        // 4. Create Santri & Room Assignment
        $org = Organization::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Kepengasuhan',
            'slug' => 'kepengasuhan-unit',
            'type' => 'pondok',
        ]);

        $this->santriPutra = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '5555555555555555',
            'name' => 'Santri Ahmad Putra',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '2008-01-01',
        ]);
        PersonRole::create([
            'id'                => Str::uuid()->toString(),
            'person_id'         => $this->santriPutra->id,
            'role_type'         => 'santri',
            'organization_id'   => $org->id,
            'valid_from'        => now()->toDateString(),
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
            'is_active'         => true,
        ]);
        SantriProfile::create([
            'id'         => Str::uuid()->toString(),
            'person_id'  => $this->santriPutra->id,
            'blood_type' => 'O',
        ]);
        RoomAssignment::create([
            'id'         => Str::uuid()->toString(),
            'person_id'  => $this->santriPutra->id,
            'room_id'    => $this->roomPutra->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        $this->santriPutri = Person::create([
            'id' => Str::uuid()->toString(),
            'nik' => '6666666666666666',
            'name' => 'Santri Fatimah Putri',
            'gender' => 'P',
            'birth_place' => 'Gresik',
            'birth_date' => '2008-05-05',
        ]);
        PersonRole::create([
            'id'                => Str::uuid()->toString(),
            'person_id'         => $this->santriPutri->id,
            'role_type'         => 'santri',
            'organization_id'   => $org->id,
            'valid_from'        => now()->toDateString(),
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
            'is_active'         => true,
        ]);
        SantriProfile::create([
            'id'         => Str::uuid()->toString(),
            'person_id'  => $this->santriPutri->id,
            'blood_type' => 'A',
        ]);
        RoomAssignment::create([
            'id'         => Str::uuid()->toString(),
            'person_id'  => $this->santriPutri->id,
            'room_id'    => $this->roomPutri->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        // 5. Create Template
        $this->template = $this->censusService->createTemplate([
            'name' => 'Template Sensus Standar',
            'description' => 'Template evaluasi',
            'is_default' => true,
        ], [
            [
                'group_name' => 'Hafalan',
                'field_key' => 'juz_hafalan',
                'field_label' => 'Juz Hafalan',
                'field_type' => 'number',
                'is_required' => false,
                'is_system_field' => false,
                'profile_field_key' => null,
            ]
        ], $this->sekretarisPutra->id);
    }

    /** @test */
    public function test_sekretaris_roles_have_proper_permissions()
    {
        $this->assertTrue($this->sekretarisPutra->can('manage-sensus-v3'));
        $this->assertTrue($this->sekretarisPutra->can('input-census-v3'));
        $this->assertTrue($this->sekretarisPutra->can('approve-census-v3'));
        $this->assertTrue($this->sekretarisPutra->can('manage-census-template'));
        $this->assertTrue($this->sekretarisPutra->can('change-enrollment-status'));

        $this->assertTrue($this->sekretarisPutri->can('manage-sensus-v3'));
        $this->assertTrue($this->sekretarisPutri->can('input-census-v3'));
        $this->assertTrue($this->sekretarisPutri->can('approve-census-v3'));
        $this->assertTrue($this->sekretarisPutri->can('manage-census-template'));
        $this->assertTrue($this->sekretarisPutri->can('change-enrollment-status'));
    }

    /** @test */
    public function test_sekretaris_putra_can_create_campaign_for_putra_only()
    {
        // Valid: Target Scope = putra
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Putra Juli 2026',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putra',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutra->id);

        $this->assertDatabaseHas('census_campaigns', [
            'id' => $campaign->id,
            'target_scope' => 'putra',
        ]);

        // Invalid: Target Scope = putri
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Sekretaris putra hanya dapat membuat kampanye untuk komplek putra.');

        $this->censusService->createCampaign([
            'name' => 'Sensus Campuran Ilegal',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putri',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutra->id);
    }

    /** @test */
    public function test_sekretaris_putra_cannot_create_all_scope_campaign()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Sekretaris hanya dapat membuat kampanye sesuai gender binaannya.');

        $this->censusService->createCampaign([
            'name' => 'Sensus Seluruh Pondok',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'all',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutra->id);
    }

    /** @test */
    public function test_sekretaris_putri_can_create_campaign_for_putri_only()
    {
        // Valid: Target Scope = putri
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Putri Juli 2026',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putri',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutri->id);

        $this->assertDatabaseHas('census_campaigns', [
            'id' => $campaign->id,
            'target_scope' => 'putri',
        ]);

        // Invalid: Target Scope = putra
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Sekretaris putri hanya dapat membuat kampanye untuk komplek putri.');

        $this->censusService->createCampaign([
            'name' => 'Sensus Campuran Ilegal Putri',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putra',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutri->id);
    }

    /** @test */
    public function test_multi_assignees_per_dormitory_pivot()
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Multi Assignee',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putra',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
            'assigned_users' => [
                $this->dormPutra->id => [$this->musyrifUser->id, $this->otherMusyrif->id],
            ],
        ], $this->sekretarisPutra->id);

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();

        $this->assertNotNull($cd);
        $this->assertEquals(2, $cd->assignees()->count());
        $this->assertTrue($cd->isAssignedTo($this->musyrifUser->id));
        $this->assertTrue($cd->isAssignedTo($this->otherMusyrif->id));
        $this->assertFalse($cd->isAssignedTo($this->sekretarisPutri->id));
    }

    /** @test */
    public function test_gender_scoping_for_dormitory_input_and_review()
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Access Check',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'custom_dormitories',
            'target_dormitory_ids' => [$this->dormPutra->id, $this->dormPutri->id],
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
            'assigned_users' => [
                $this->dormPutra->id => [$this->musyrifUser->id],
            ],
        ], $this->sekretarisPutra->id);

        $cdPutra = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();

        $cdPutri = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutri->id)
            ->first();

        // 1. Sekretaris Putra:
        $this->assertTrue($this->accessService->canAccessDormitory($this->sekretarisPutra, $cdPutra, 'fill'));
        $this->assertTrue($this->accessService->canAccessDormitory($this->sekretarisPutra, $cdPutra, 'review'));
        $this->assertFalse($this->accessService->canAccessDormitory($this->sekretarisPutra, $cdPutri, 'fill'));
        $this->assertFalse($this->accessService->canAccessDormitory($this->sekretarisPutra, $cdPutri, 'review'));

        // 2. Sekretaris Putri:
        $this->assertFalse($this->accessService->canAccessDormitory($this->sekretarisPutri, $cdPutra, 'fill'));
        $this->assertFalse($this->accessService->canAccessDormitory($this->sekretarisPutri, $cdPutra, 'review'));
        $this->assertTrue($this->accessService->canAccessDormitory($this->sekretarisPutri, $cdPutri, 'fill'));
        $this->assertTrue($this->accessService->canAccessDormitory($this->sekretarisPutri, $cdPutri, 'review'));

        // 3. Assigned Musyrif:
        $this->assertTrue($this->accessService->canAccessDormitory($this->musyrifUser, $cdPutra, 'fill'));
        $this->assertFalse($this->accessService->canAccessDormitory($this->musyrifUser, $cdPutra, 'review'));

        // 4. Unassigned Musyrif:
        $this->assertFalse($this->accessService->canAccessDormitory($this->otherMusyrif, $cdPutra, 'fill'));
        $this->assertFalse($this->accessService->canAccessDormitory($this->otherMusyrif, $cdPutra, 'review'));
    }

    /** @test */
    public function test_save_response_fails_if_dormitory_already_submitted()
    {
        $campaign = $this->censusService->createCampaign([
            'name' => 'Sensus Locking Check',
            'template_id' => $this->template->id,
            'month' => 7,
            'year' => 2026,
            'target_scope' => 'putra',
            'workflow_mode' => 'distributed',
            'allow_excel' => false,
            'allow_direct_input' => true,
        ], $this->sekretarisPutra->id);

        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormPutra->id)
            ->first();

        // 1. Save response in pending state (Allowed)
        $res = $this->censusService->saveResponse(
            $cd->id,
            $this->santriPutra->id,
            ['presence_status' => 'mukim', 'juz_hafalan' => 5],
            $this->sekretarisPutra->id
        );

        $this->assertDatabaseHas('census_responses', [
            'id' => $res->id,
            'campaign_id' => $campaign->id,
            'dormitory_id' => $cd->dormitory_id,
            'person_id' => $this->santriPutra->id,
        ]);

        // 2. Submit dormitory
        $this->censusService->submitDormitory($cd->id, $this->sekretarisPutra->id);
        $cd->refresh();
        $this->assertEquals('submitted', $cd->status);

        // 3. Attempting to save after submit should fail
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Laporan sensus untuk asrama ini sudah dikirim atau disetujui');

        $this->censusService->saveResponse(
            $cd->id,
            $this->santriPutra->id,
            ['presence_status' => 'izin', 'juz_hafalan' => 6],
            $this->sekretarisPutra->id
        );
    }

    /** @test */
    public function test_template_management_access_for_sekretaris()
    {
        // Sekretaris can create template
        $customTemplate = $this->censusService->createTemplate([
            'name' => 'Template Khusus Putra',
            'description' => 'Khusus',
            'is_default' => false,
        ], [
            [
                'group_name' => 'Extra',
                'field_key' => 'extra_info',
                'field_label' => 'Info Tambahan',
                'field_type' => 'text',
                'is_required' => false,
                'is_system_field' => false,
                'profile_field_key' => null,
            ]
        ], $this->sekretarisPutra->id);

        // Owner (sekretarisPutra) can manage it
        $this->assertTrue($this->accessService->canManageTemplate($this->sekretarisPutra, $customTemplate));

        // Other secretary (sekretarisPutri) cannot manage it
        $this->assertFalse($this->accessService->canManageTemplate($this->sekretarisPutri, $customTemplate));
    }
}
