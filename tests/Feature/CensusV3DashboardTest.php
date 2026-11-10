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
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Livewire\Kepengasuhan\CensusV3Dashboard;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CensusV3DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unauthorizedUser;
    protected Dormitory $dormPutra;
    protected Room $roomA1;
    protected Person $santri1;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'manage-sensus-v3',
            'input-census-v3',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->adminUser = User::factory()->create(['name' => 'Admin Eksekutif']);
        $this->adminUser->assignRole($adminRole);

        $this->unauthorizedUser = User::factory()->create(['name' => 'User Biasa']);

        $this->dormPutra = Dormitory::create([
            'name'      => 'Komplek A - Sunan Ampel',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $this->roomA1 = Room::create([
            'dormitory_id' => $this->dormPutra->id,
            'name'         => 'Kamar A-01',
            'capacity'     => 10,
            'is_active'    => true,
        ]);

        $org = Organization::create([
            'name' => 'Pondok Pusat',
            'slug' => 'pondok-pusat',
            'type' => 'pondok',
        ]);

        $this->santri1 = Person::create([
            'name'        => 'Ahmad Fauzi',
            'gender'      => 'L',
            'birth_place' => 'Surabaya',
            'birth_date'  => '2008-01-01',
            'nik'         => '3578010101080001',
            'phone'       => '081234567890',
            'address'     => 'Jl. Pesantren No. 1',
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
            'person_id'    => $this->santri1->id,
            'father_name'  => 'Fauzi Senior',
            'father_phone' => '081299998888',
        ]);

        RoomAssignment::create([
            'room_id'    => $this->roomA1->id,
            'person_id'  => $this->santri1->id,
            'valid_from' => now()->toDateString(),
            'is_active'  => true,
        ]);

        Locker::create([
            'room_id'            => $this->roomA1->id,
            'code'               => 'B-01',
            'type'               => 'baju',
            'condition'          => 'baik',
            'assigned_person_id' => $this->santri1->id,
        ]);
    }

    public function test_can_render_executive_command_center_with_metrics_and_launchpad(): void
    {
        $this->actingAs($this->adminUser);

        $test = Livewire::test(CensusV3Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Pusat Kendali Sensus & Asrama')
            ->assertSee('Executive Command Center')
            ->assertSee('Papan Mutasi Santri Live')
            ->assertSee('Papan Denah Fasilitas Live')
            ->assertSee('+ Sensus Biodata Baru')
            ->assertSee('Okupansi Kasur')
            ->assertSee('Kesiapan Fasilitas')
            ->assertSee('Kelengkapan Profil Santri');

        $execStats = $test->viewData('executiveStats');
        $this->assertNotNull($execStats);
        $this->assertEquals(10, $execStats['total_capacity']);
        $this->assertEquals(1, $execStats['total_assigned_beds']);
        $this->assertEquals(9, $execStats['total_vacant_beds']);
        $this->assertEquals(1, $execStats['total_lockers']);
        $this->assertEquals(1, $execStats['occupied_lockers']);
        $this->assertEquals(0, $execStats['damaged_lockers']);
        $this->assertEquals(1, $execStats['total_active_santri']);
    }

    public function test_unauthorized_user_is_forbidden(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(CensusV3Dashboard::class)
            ->assertStatus(403);
    }

    private function createDummyCampaign(string $status = 'draft'): CensusV3Campaign
    {
        $censusService = app(CensusV3Service::class);
        $template = $censusService->createTemplate([
            'name'        => 'Template Uji ' . uniqid(),
            'description' => 'Untuk test sensus',
            'is_default'  => true,
        ], [
            [
                'group_name'        => 'Umum',
                'field_key'         => 'catatan',
                'field_label'       => 'Catatan',
                'field_type'        => 'text',
                'field_options'     => null,
                'is_required'       => false,
                'is_system_field'   => false,
                'profile_field_key' => null,
            ]
        ], $this->adminUser->id);

        $campaign = $censusService->createCampaign([
            'name'          => 'Sensus Uji ' . uniqid(),
            'description'   => 'Deskripsi uji',
            'template_id'   => $template->id,
            'census_type'   => 'formal',
            'month'         => 11,
            'year'          => 2026,
            'target_scope'  => 'all',
            'workflow_mode' => 'admin_only',
        ], $this->adminUser->id);

        if ($status !== 'draft') {
            $campaign = $censusService->activateCampaign($campaign->id);
            if ($status === 'review') {
                $campaign = $censusService->closeCampaignForReview($campaign->id);
            } elseif ($status === 'cancelled') {
                $campaign = $censusService->cancelCampaign($campaign->id, 'Alasan dibatalkan', $this->adminUser->id);
            }
        }

        return $campaign;
    }

    public function test_admin_can_open_cancel_modal_and_cancel_active_campaign(): void
    {
        $this->actingAs($this->adminUser);
        $campaign = $this->createDummyCampaign('active');

        Livewire::test(CensusV3Dashboard::class)
            ->assertSet('showCancelModal', false)
            ->call('openCancelModal', $campaign->id)
            ->assertSet('showCancelModal', true)
            ->assertSet('campaignIdToCancel', $campaign->id)
            ->assertSet('campaignNameToCancel', $campaign->name)
            ->set('cancelReason', 'Salah memilih target komplek')
            ->call('executeCancelCampaign')
            ->assertSet('showCancelModal', false);

        $campaign->refresh();
        $this->assertEquals('cancelled', $campaign->status);
        $this->assertEquals('Salah memilih target komplek', $campaign->cancellation_reason);
        $this->assertEquals($this->adminUser->id, $campaign->cancelled_by);
        $this->assertNotNull($campaign->cancelled_at);
    }

    public function test_cancelled_campaign_appears_in_archive_tab(): void
    {
        $this->actingAs($this->adminUser);
        $campaign = $this->createDummyCampaign('cancelled');

        Livewire::test(CensusV3Dashboard::class)
            ->set('statusFilter', 'closed')
            ->assertSee($campaign->name)
            ->assertSee('Dibatalkan')
            ->assertSee('Alasan dibatalkan');
    }

    public function test_service_rejects_saving_response_on_cancelled_campaign(): void
    {
        $this->actingAs($this->adminUser);
        $campaign = $this->createDummyCampaign('cancelled');
        $cd = $campaign->dormitories()->first();
        $this->assertNotNull($cd);

        $censusService = app(CensusV3Service::class);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Kampanye sensus ini telah dibatalkan.');

        $censusService->saveResponse(
            $cd->id,
            $this->santri1->id,
            ['catatan' => 'Uji input pada sensus batal'],
            $this->adminUser->id
        );
    }

    public function test_unauthorized_user_cannot_cancel_campaign(): void
    {
        $campaign = $this->createDummyCampaign('active');
        $musyrif = User::factory()->create(['name' => 'Musyrif Biasa']);
        $musyrif->givePermissionTo('input-census-v3');

        $this->actingAs($musyrif);

        Livewire::test(CensusV3Dashboard::class)
            ->call('openCancelModal', $campaign->id)
            ->assertStatus(403);
    }
}
