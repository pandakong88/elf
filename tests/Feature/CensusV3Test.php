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
use App\Modules\Kepengasuhan\Models\SantriStatusLog;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CensusV3Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Dormitory $dormitory;
    protected Room $room;
    protected Person $santri;
    protected PersonRole $santriRole;
    protected CensusV3Service $censusService;
    protected SantriStatusService $statusService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->censusService = app(CensusV3Service::class);
        $this->statusService = app(SantriStatusService::class);

        // 1. Setup Admin User
        $adminPerson = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '0000000000000000',
            'name'        => 'Test Administrator',
            'gender'      => 'L',
            'birth_place' => 'Surabaya',
            'birth_date'  => '1990-01-01',
            'phone'       => '081234567890',
            'address'     => 'Pesantren Al-Fithroh',
        ]);

        $this->adminUser = User::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $adminPerson->id,
            'name'      => 'Test Administrator',
            'username'  => 'admin',
            'email'     => 'admin@elvith.id',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);

        // 2. Setup Dormitory & Room
        $this->dormitory = Dormitory::create([
            'id'        => Str::uuid()->toString(),
            'name'      => 'Komplek A Putra',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $this->dormitory->id,
            'name'         => 'Kamar A1',
            'capacity'     => 10,
            'is_active'    => true,
        ]);

        // 3. Setup Santri
        $this->santri = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '3333333333333333',
            'name'        => 'Santri Ahmad',
            'gender'      => 'L',
            'birth_place' => 'Sidoarjo',
            'birth_date'  => '2005-05-05',
            'phone'       => '081234567899',
            'address'     => 'Sidoarjo',
        ]);

        $org = Organization::create([
            'id'   => Str::uuid()->toString(),
            'name' => 'Unit Kepengasuhan',
            'slug' => 'unit-kepengasuhan',
            'type' => 'pondok',
        ]);

        $this->santriRole = PersonRole::create([
            'id'                => Str::uuid()->toString(),
            'person_id'         => $this->santri->id,
            'organization_id'   => $org->id,
            'role_type'         => 'santri',
            'valid_from'        => now()->toDateString(),
            'is_active'         => true,
            'enrollment_status' => 'aktif',
            'presence_status'   => 'mukim',
        ]);

        SantriProfile::create([
            'id'         => Str::uuid()->toString(),
            'person_id'  => $this->santri->id,
            'blood_type' => 'O',
        ]);

        // Assign Room
        RoomAssignment::create([
            'id'         => Str::uuid()->toString(),
            'room_id'    => $this->room->id,
            'person_id'  => $this->santri->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);
    }

    /** @test */
    public function test_it_can_create_a_template_and_fields()
    {
        $fields = [
            [
                'group_name'        => 'Hafalan',
                'field_key'         => 'juz_hafalan',
                'field_label'       => 'Jumlah Juz Hafalan',
                'field_type'        => 'number',
                'field_options'     => null,
                'is_required'       => true,
                'is_system_field'   => false,
                'profile_field_key' => null,
            ],
            [
                'group_name'        => 'Kesehatan',
                'field_key'         => 'blood_type',
                'field_label'       => 'Golongan Darah',
                'field_type'        => 'dropdown',
                'field_options'     => ['A', 'B', 'AB', 'O'],
                'is_required'       => false,
                'is_system_field'   => true,
                'profile_field_key' => 'blood_type',
            ]
        ];

        $template = $this->censusService->createTemplate([
            'name'        => 'Template Sensus Bulanan',
            'description' => 'Sensus rutin asrama',
            'is_default'  => true,
        ], $fields, $this->adminUser->id);

        $this->assertDatabaseHas('census_templates', [
            'name'       => 'Template Sensus Bulanan',
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('census_template_fields', [
            'template_id' => $template->id,
            'field_key'   => 'juz_hafalan',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('census_template_fields', [
            'template_id'     => $template->id,
            'field_key'       => 'blood_type',
            'is_system_field' => true,
        ]);
    }

    /** @test */
    public function test_it_can_create_campaign_and_submit_review_cycle()
    {
        // 1. Create Template
        $fields = [
            [
                'group_name'        => 'Hafalan',
                'field_key'         => 'juz_hafalan',
                'field_label'       => 'Jumlah Juz Hafalan',
                'field_type'        => 'number',
                'field_options'     => null,
                'is_required'       => true,
                'is_system_field'   => false,
                'profile_field_key' => null,
            ],
            [
                'group_name'        => 'Kesehatan',
                'field_key'         => 'blood_type',
                'field_label'       => 'Golongan Darah',
                'field_type'        => 'dropdown',
                'field_options'     => ['A', 'B', 'AB', 'O'],
                'is_required'       => false,
                'is_system_field'   => true,
                'profile_field_key' => 'blood_type',
            ]
        ];

        $template = $this->censusService->createTemplate([
            'name'        => 'Template Sensus Bulanan',
            'description' => 'Sensus rutin asrama',
            'is_default'  => true,
        ], $fields, $this->adminUser->id);

        // 2. Create Campaign
        $campaign = $this->censusService->createCampaign([
            'name'               => 'Sensus Juli 2026',
            'description'        => 'Sensus awal semester',
            'template_id'        => $template->id,
            'month'              => 7,
            'year'               => 2026,
            'target_scope'       => 'all',
            'workflow_mode'      => 'distributed',
            'allow_excel'        => false,
            'allow_direct_input' => true,
            'deadline'           => now()->addDays(7)->toDateString(),
        ], $this->adminUser->id);

        $this->assertDatabaseHas('census_campaigns', [
            'name'   => 'Sensus Juli 2026',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('census_campaign_dormitories', [
            'campaign_id'  => $campaign->id,
            'dormitory_id' => $this->dormitory->id,
            'status'       => 'pending',
        ]);

        // 3. Activate Campaign
        $this->censusService->activateCampaign($campaign->id);

        $this->assertDatabaseHas('census_campaigns', [
            'id'     => $campaign->id,
            'status' => 'collecting',
        ]);

        // Fetch target dormitory record
        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaign->id)
            ->where('dormitory_id', $this->dormitory->id)
            ->firstOrFail();

        // 4. Save Response (Changing blood type from O to A)
        $responseData = [
            'juz_hafalan' => 15,
            'blood_type'  => 'A',
        ];

        $response = $this->censusService->saveResponse(
            $cd->id,
            $this->santri->id,
            $responseData,
            $this->adminUser->id
        );

        $this->assertDatabaseHas('census_responses', [
            'campaign_id'         => $campaign->id,
            'person_id'           => $this->santri->id,
            'is_complete'         => true,
            'has_profile_changes' => true,
        ]);

        // 5. Submit Dormitory
        $this->censusService->submitDormitory($cd->id, $this->adminUser->id);

        $this->assertDatabaseHas('census_campaign_dormitories', [
            'id'     => $cd->id,
            'status' => 'submitted',
        ]);

        // 6. Approve Dormitory (Should sync blood type to profile)
        $this->censusService->approveDormitory($cd->id, $this->adminUser->id);

        $this->assertDatabaseHas('census_campaign_dormitories', [
            'id'     => $cd->id,
            'status' => 'approved',
        ]);

        // Verify blood type was synced from O to A
        $profile = SantriProfile::where('person_id', $this->santri->id)->firstOrFail();
        $this->assertEquals('A', $profile->blood_type);
    }

    /** @test */
    public function test_it_can_log_presence_and_enrollment_status_changes()
    {
        // 1. Test presence status change
        $this->statusService->changePresenceStatus(
            $this->santriRole->id,
            'izin',
            $this->adminUser->id,
            now()->addDays(3)->toDateString(),
            'Santri izin pulang karena ada acara keluarga.'
        );

        $this->assertDatabaseHas('person_roles', [
            'id'              => $this->santriRole->id,
            'presence_status' => 'izin',
        ]);

        $this->assertDatabaseHas('santri_status_logs', [
            'person_id'     => $this->santri->id,
            'changed_field' => 'presence_status',
            'old_value'     => 'mukim',
            'new_value'     => 'izin',
            'changed_by'    => $this->adminUser->id,
        ]);

        // 2. Test enrollment status change
        $this->statusService->changeEnrollmentStatus(
            $this->santriRole->id,
            'alumni',
            $this->adminUser->id,
            'Santri lulus formal.'
        );

        $this->assertDatabaseHas('person_roles', [
            'id'                => $this->santriRole->id,
            'enrollment_status' => 'alumni',
            'presence_status'   => null, // should be set to null if not active
        ]);

        $this->assertDatabaseHas('santri_status_logs', [
            'person_id'     => $this->santri->id,
            'changed_field' => 'enrollment_status',
            'old_value'     => 'aktif',
            'new_value'     => 'alumni',
            'changed_by'    => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function test_it_can_export_and_download_template_sample_excel()
    {
        // 1. Create Template
        $fields = [
            [
                'group_name'        => 'Hafalan',
                'field_key'         => 'juz_hafalan',
                'field_label'       => 'Jumlah Juz Hafalan',
                'field_type'        => 'number',
                'field_options'     => null,
                'is_required'       => true,
                'is_system_field'   => false,
                'profile_field_key' => null,
            ]
        ];

        $template = $this->censusService->createTemplate([
            'name'        => 'Template Sensus Bulanan',
            'description' => 'Sensus rutin asrama',
            'is_default'  => true,
        ], $fields, $this->adminUser->id);

        $export = new \App\Modules\Kepengasuhan\Exports\CensusTemplateSampleExport($template);
        $sheets = $export->sheets();

        $this->assertCount(2, $sheets);
        $this->assertInstanceOf(\App\Modules\Kepengasuhan\Exports\SensusTemplateSampleDataSheet::class, $sheets[0]);
        $this->assertInstanceOf(\App\Modules\Kepengasuhan\Exports\SensusTemplateSampleInstructionSheet::class, $sheets[1]);

        $headings = $sheets[0]->headings();
        $this->assertContains('Jumlah Juz Hafalan (juz_hafalan)', $headings);

        $data = $sheets[0]->array();
        $this->assertCount(3, $data); // Should contain 3 dummy rows
        $this->assertEquals('sample-uuid-1', $data[0][0]);
        $this->assertEquals('Muhammad Yusuf', $data[0][1]);
        $this->assertEquals('Umar Bin Khattab - 01', $data[0][2]);
        $this->assertEquals('aktif', $data[0][3]);
        $this->assertEquals('mukim', $data[0][4]);
    }
}
