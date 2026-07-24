<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Organization;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\BillingException;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Services\BillingService;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class KeuanganLanjutanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Organization $pondok;

    protected function setUp(): void
    {
        parent::setUp();

        // Create core master organization
        $this->pondok = Organization::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Pondok Pesantren Al-Fithroh',
            'slug' => 'ponpes-al-fithroh',
            'type' => 'pondok',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Administrator',
            'email' => 'admin@alfithroh.com',
            'password' => bcrypt('password'),
        ]);

        // Spatie Roles
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bendahara-putra', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bendahara-putri', 'guard_name' => 'web']);

        $this->admin->assignRole('super-admin');
    }

    public function test_kelas_creation_and_enrollment(): void
    {
        $kelas = MadrasahKelas::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Kelas 1 Ula A',
            'jenjang' => 'ula',
            'academic_year' => '2025/2026',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        $enrollment = MadrasahEnrollment::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'kelas_id' => $kelas->id,
            'academic_year' => '2025/2026',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('madrasah_kelas', ['name' => 'Kelas 1 Ula A']);
        $this->assertDatabaseHas('madrasah_enrollments', [
            'person_id' => $santri->id,
            'kelas_id' => $kelas->id,
        ]);
    }

    public function test_generate_kitab_bills_for_kelas(): void
    {
        $kelas = MadrasahKelas::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Kelas 1 Ula A',
            'jenjang' => 'ula',
            'academic_year' => '2025/2026',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        MadrasahEnrollment::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'kelas_id' => $kelas->id,
            'academic_year' => '2025/2026',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $billingService = new BillingService();
        $result = $billingService->generateKitabBills($kelas->id, 1, 2026, 75000.00, $this->admin->id);

        $this->assertEquals(1, $result['generated']);

        $this->assertDatabaseHas('bills', [
            'person_id' => $santri->id,
            'bill_type' => 'kitab',
            'amount' => 75000.00,
            'period_month' => 1,
            'period_year' => 2026,
        ]);
    }

    public function test_bulk_payment_processing(): void
    {
        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        $bill1 = Bill::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 35000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 6,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        $bill2 = Bill::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 35000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 7,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        $billingService = new BillingService();
        $result = $billingService->processBulkPayment(
            [$bill1->id, $bill2->id],
            'CASH',
            'Setoran Komplek Al-Falah',
            $this->admin->id
        );

        $this->assertEquals(2, $result['processed']);
        $this->assertEquals(70000.00, $result['total_amount']);

        $bill1->refresh();
        $bill2->refresh();

        $this->assertEquals('paid', $bill1->status);
        $this->assertEquals('paid', $bill2->status);
    }

    public function test_gender_scope_filtering(): void
    {
        $santriPutra = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Putra',
            'gender' => 'L',
        ]);
        $santriPutra->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $santriPutri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Aisyah Putri',
            'gender' => 'P',
        ]);
        $santriPutri->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $bendaharaPutra = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Bendahara Putra',
            'email' => 'putra@alfithroh.com',
            'password' => bcrypt('password'),
        ]);
        $bendaharaPutra->assignRole('bendahara-putra');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($bendaharaPutra);

        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'cashier')
            ->set('searchQuery', 'Ahmad')
            ->assertSee('Ahmad Putra')
            ->set('searchQuery', 'Aisyah')
            ->assertDontSee('Aisyah Putri');
    }

    public function test_billing_exceptions_and_discounts(): void
    {
        // Setup billing config
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Syahriah Pondok',
            'amount' => 35000.00,
            'effective_from' => now()->toDateString(),
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Santri Ahmad gets a discount of 10k
        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Santri',
            'gender' => 'L',
        ]);
        $santri->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        BillingException::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $santri->id,
            'exception_type' => 'discount',
            'amount' => 10000.00,
            'created_by' => $this->admin->id,
        ]);

        $service = new BillingService();
        $res = $service->generateMonthlyBills(7, 2026, $this->admin->id);

        $this->assertEquals(1, $res['generated']);

        $bill = Bill::where('person_id', $santri->id)->first();
        $this->assertNotNull($bill);
        $this->assertEquals(25000.00, $bill->amount); // 35000 - 10000 = 25000
    }

    public function test_installment_billing_generation(): void
    {
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'pendaftaran',
            'label' => 'Khataman 2026',
            'amount' => 2000000.00,
            'effective_from' => now()->toDateString(),
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Budi Santri',
            'gender' => 'L',
        ]);

        $service = new BillingService();
        $res = $service->generateInstallmentBills($santri->id, $config->id, 1500000.00, 3, $this->admin->id);

        $this->assertEquals(3, $res['terms']);

        $parent = Bill::find($res['parent_bill_id']);
        $this->assertNotNull($parent);
        $this->assertEquals(1500000.00, $parent->amount);
        $this->assertNull($parent->parent_bill_id);

        $installments = Bill::where('parent_bill_id', $parent->id)->get();
        $this->assertCount(3, $installments);
        foreach ($installments as $inst) {
            $this->assertEquals(500000.00, $inst->amount);
        }

        // Test Livewire BillingManager installment actions
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->call('showInstallmentDetails', $parent->id)
            ->assertSet('showInstallmentDetailsModal', true)
            ->assertSet('selectedParentBillId', $parent->id)
            ->call('closeInstallmentDetailsModal')
            ->assertSet('showInstallmentDetailsModal', false)
            ->call('cancelInstallmentPlan', $parent->id)
            ->assertHasNoErrors();

        // Verify parent and child bills are soft deleted
        $this->assertSoftDeleted('bills', ['id' => $parent->id]);
        foreach ($installments as $inst) {
            $this->assertSoftDeleted('bills', ['id' => $inst->id]);
        }
    }

    public function test_profile_based_gender_scope(): void
    {
        // 1. Create a female admin staff with gender 'P'
        $staffProfile = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Fatimah Staff',
            'gender' => 'P',
        ]);

        $staffUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Fatimah Staff',
            'email' => 'fatimah@alfithroh.com',
            'password' => bcrypt('password'),
            'person_id' => $staffProfile->id,
        ]);
        // Give her a generic finance role
        $staffUser->assignRole('bendahara-putri'); // fallback but profile P is primary

        $this->actingAs($staffUser);

        // Under Fatimah staff (gender P), search result for Ahmad (L) should not see him
        // but Aisyah (P) should see her.
        $santriPutra = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Putra',
            'gender' => 'L',
        ]);
        $santriPutra->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $santriPutri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Aisyah Putri',
            'gender' => 'P',
        ]);
        $santriPutri->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'cashier')
            ->set('searchQuery', 'Ahmad')
            ->assertDontSee('Ahmad Putra')
            ->set('searchQuery', 'Aisyah')
            ->assertSee('Aisyah Putri');
    }

    public function test_dynamic_targeting_bill_generation(): void
    {
        $dorm = Dormitory::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Komplek Ciamik',
            'gender' => 'L',
        ]);

        $santriInDorm = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Dorm',
            'gender' => 'L',
        ]);
        $santriInDorm->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);
        
        $room = \App\Modules\Kepengasuhan\Models\Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $dorm->id,
            'name' => 'Kamar A1',
            'capacity' => 4,
        ]);
        \App\Modules\Kepengasuhan\Models\RoomAssignment::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santriInDorm->id,
            'room_id' => $room->id,
            'is_active' => true,
            'valid_from' => now()->subDays(1),
        ]);

        $santriOutDorm = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Non Dorm',
            'gender' => 'L',
        ]);
        $santriOutDorm->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'kebersihan',
            'label' => 'Kebersihan Komplek Ciamik',
            'amount' => 15000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'monthly',
            'target_type' => 'dormitory',
            'target_filters' => [$dorm->id],
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $service = new BillingService();
        $result = $service->generateBillsFromConfig($config->id, 7, 2026, $this->admin->id);

        $this->assertEquals(1, $result['generated']);

        $this->assertTrue(Bill::where('person_id', $santriInDorm->id)->where('billing_config_id', $config->id)->exists());
        $this->assertFalse(Bill::where('person_id', $santriOutDorm->id)->where('billing_config_id', $config->id)->exists());
    }

    public function test_individual_targeting_bill_generation(): void
    {
        $santri1 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri One',
            'gender' => 'L',
        ]);
        $santri1->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $santri2 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Two',
            'gender' => 'L',
        ]);
        $santri2->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'pendaftaran',
            'label' => 'Ziarah Wali Songo 2026',
            'amount' => 350000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'insidental',
            'target_type' => 'individual',
            'target_filters' => [$santri1->id],
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $service = new BillingService();
        $result = $service->generateBillsFromConfig($config->id, 7, 2026, $this->admin->id);

        $this->assertEquals(1, $result['generated']);

        $this->assertTrue(Bill::where('person_id', $santri1->id)->where('billing_config_id', $config->id)->exists());
        $this->assertFalse(Bill::where('person_id', $santri2->id)->where('billing_config_id', $config->id)->exists());
    }

    public function test_installment_parent_auto_payoff(): void
    {
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'pendaftaran',
            'label' => 'Khataman 2026',
            'amount' => 150000.00,
            'effective_from' => now()->toDateString(),
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Budi Santri',
            'gender' => 'L',
        ]);

        $service = new BillingService();
        $res = $service->generateInstallmentBills($santri->id, $config->id, 150000.00, 3, $this->admin->id);

        $parent = Bill::find($res['parent_bill_id']);
        $installments = Bill::where('parent_bill_id', $parent->id)->get();

        $this->assertEquals('unpaid', $parent->status);

        // Pay off installment 1
        $service->recordPayment($installments[0]->id, 50000.00, 'CASH', 'Term 1', $this->admin->id);
        $parent->refresh();
        $this->assertEquals('partial', $parent->status);
        $this->assertEquals(50000.00, $parent->amount_paid);

        // Pay off installment 2 & 3
        $service->recordPayment($installments[1]->id, 50000.00, 'CASH', 'Term 2', $this->admin->id);
        $service->recordPayment($installments[2]->id, 50000.00, 'CASH', 'Term 3', $this->admin->id);

        $parent->refresh();
        $this->assertEquals('paid', $parent->status);
        $this->assertEquals(150000.00, $parent->amount_paid);
    }

    public function test_co_manager_delegated_access(): void
    {
        $coManagerUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Co Manager User',
            'email' => 'comanager@alfithroh.com',
            'password' => bcrypt('password'),
        ]);

        $unauthorizedUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Unauthorized User',
            'email' => 'unauth@alfithroh.com',
            'password' => bcrypt('password'),
        ]);

        // Create billing config managed by 'bendahara-madrasah' role but individually delegated to $coManagerUser
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'pendaftaran',
            'label' => 'Ziarah Wali Songo 2026 Special',
            'amount' => 350000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'insidental',
            'manager_role' => 'bendahara-madrasah',
            'manager_ids' => [$coManagerUser->id],
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Logged in as co-manager (authorized via JSON whitelist)
        $this->actingAs($coManagerUser);
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'rates')
            ->assertSee('Ziarah Wali Songo 2026 Special');

        // Logged in as unauthorized user (neither has the role nor in the whitelist)
        $this->actingAs($unauthorizedUser);
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'rates')
            ->assertDontSee('Ziarah Wali Songo 2026 Special');
    }

    public function test_multiple_manager_roles_delegated_access(): void
    {
        $pondokUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Bendahara Pondok User',
            'email' => 'pondok@alfithroh.com',
            'password' => bcrypt('password'),
        ]);
        $pondokUser->assignRole('bendahara-putra');

        Role::firstOrCreate(['name' => 'bendahara-madrasah', 'guard_name' => 'web']);
        $madrasahUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Bendahara Madrasah User',
            'email' => 'madrasah@alfithroh.com',
            'password' => bcrypt('password'),
        ]);
        $madrasahUser->assignRole('bendahara-madrasah');

        $unauthorizedUser = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Unauthorized User',
            'email' => 'unauth2@alfithroh.com',
            'password' => bcrypt('password'),
        ]);

        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'pendaftaran',
            'label' => 'Kitab & Ziarah Ganda',
            'amount' => 150000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'insidental',
            'manager_role' => json_encode(['bendahara-putra', 'bendahara-madrasah']),
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Authorized 1
        $this->actingAs($pondokUser);
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'rates')
            ->assertSee('Kitab & Ziarah Ganda');

        // Authorized 2
        $this->actingAs($madrasahUser);
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'rates')
            ->assertSee('Kitab & Ziarah Ganda');

        // Unauthorized
        $this->actingAs($unauthorizedUser);
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('activeTab', 'rates')
            ->assertDontSee('Kitab & Ziarah Ganda');
    }

    public function test_create_billing_configuration_page(): void
    {
        $this->actingAs($this->admin);

        // Access route directly
        $response = $this->get(route('keuangan.billing.create'));
        $response->assertStatus(200);

        // Test Livewire component createConfig
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingConfigurationCreate::class)
            ->set('newConfigName', 'Iuran Ziarah Wali Songo 2027')
            ->set('newConfigType', 'pendaftaran')
            ->set('newConfigAmount', 450000.00)
            ->set('newConfigInterval', 'insidental')
            ->set('newConfigEffectiveFrom', now()->toDateString())
            ->set('newConfigTargetType', 'all')
            ->set('newConfigCanBeInstallment', true)
            ->call('createConfig')
            ->assertRedirect(route('keuangan.billing', ['tab' => 'rates']));

        $this->assertDatabaseHas('billing_configurations', [
            'label' => 'Iuran Ziarah Wali Songo 2027',
            'amount' => 450000.00,
            'can_be_installment' => true,
        ]);
    }

    public function test_edit_billing_configuration_page(): void
    {
        $this->actingAs($this->admin);

        // Create a test santri
        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        // 1. Create config
        $config = \App\Modules\Keuangan\Models\BillingConfiguration::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'kas_komplek',
            'label' => 'Kas Komplek A Awal 2026',
            'amount' => 25000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'monthly',
            'target_type' => 'individual',
            'target_filters' => [],
            'can_be_installment' => false,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // 2. Access edit route
        $response = $this->get(route('keuangan.billing.edit', ['id' => $config->id]));
        $response->assertStatus(200);

        // 3. Test Livewire updateConfig and target validation
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingConfigurationEdit::class, ['id' => $config->id])
            ->assertSet('newConfigName', 'Kas Komplek A Awal 2026')
            ->assertSet('newConfigAmount', 25000.00)
            ->set('newConfigName', 'Kas Komplek A Terbaru 2026')
            ->set('newConfigAmount', 30000.00)
            ->set('newConfigTargetFilters', []) // empty target
            ->call('updateConfig')
            ->assertHasErrors(['newConfigTargetFilters']) // should fail validation because target is empty and type is individual
            ->set('newConfigTargetFilters', [$santri->id]) // set valid target
            ->set('syncNewTargets', true) // enable sync
            ->call('updateConfig')
            ->assertRedirect(route('keuangan.billing', ['tab' => 'rates']));

        // 4. Verify DB updates
        $this->assertDatabaseHas('billing_configurations', [
            'id' => $config->id,
            'label' => 'Kas Komplek A Terbaru 2026',
            'amount' => 30000.00,
        ]);

        // 5. Verify that sync created a bill for the target santri
        $this->assertDatabaseHas('bills', [
            'person_id' => $santri->id,
            'billing_config_id' => $config->id,
            'amount' => 30000.00,
            'status' => 'unpaid',
        ]);
    }

    public function test_print_setup_billing_configuration(): void
    {
        $this->actingAs($this->admin);

        // 1. Create a dormitory, room, and assign a student to it
        $dorm = Dormitory::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Komplek A Baru',
            'gender' => 'L',
            'created_by' => $this->admin->id,
        ]);

        $room = Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $dorm->id,
            'name' => 'Kamar 101',
            'capacity' => 10,
        ]);

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Zaidan Alif',
            'gender' => 'L',
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        RoomAssignment::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'room_id' => $room->id,
            'is_active' => true,
            'valid_from' => now()->subDays(10)->toDateString(),
        ]);

        // 2. Create config
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'kas_komplek',
            'label' => 'Kas Komplek A 2026',
            'amount' => 20000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'monthly',
            'target_type' => 'dormitory',
            'target_filters' => [$dorm->id],
            'can_be_installment' => false,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // 3. Test setup page load
        $response = $this->get(route('keuangan.billing.print-setup', ['id' => $config->id]));
        $response->assertStatus(200);

        // 4. Test Livewire component preview structure
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingConfigurationPrintSetup::class, ['id' => $config->id])
            ->set('selectedDormitoryIds', [$dorm->id])
            ->assertSet('selectedMonth', (int) now()->format('m'))
            ->assertSet('selectedYear', (int) now()->format('Y'))
            ->assertSee('Zaidan Alif');

        // 5. Test printable route
        $printResponse = $this->get(route('print.checklist-config', [
            'id' => $config->id,
            'dormitory_id' => $dorm->id,
            'month' => now()->month,
            'year' => now()->year
        ]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Zaidan Alif');
        $printResponse->assertSee('Kas Komplek A 2026');
    }

    public function test_retroactive_billing_exceptions(): void
    {
        $santri1 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Adik Kandung',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '2012-05-15',
            'phone' => '081234567890',
            'address' => 'Jl. Genteng',
        ]);

        $santri2 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Kakak Kandung',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '2010-05-15',
            'phone' => '081234567891',
            'address' => 'Jl. Genteng',
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri1->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri2->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        // Create confirmed sibling relationship
        \App\Modules\Kepengasuhan\Models\SantriSibling::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri1->id,
            'sibling_person_id' => $santri2->id,
            'relationship' => 'adik',
            'auto_detected' => true,
            'is_confirmed' => true,
            'confirmed_by' => $this->admin->id,
            'confirmed_at' => now(),
            'is_eligible_for_discount' => true,
        ]);

        // Create config for event
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Event Haul 2026',
            'amount' => 200000.00,
            'effective_from' => now()->toDateString(),
            'interval' => 'one_time',
            'target_type' => 'all',
            'target_filters' => [],
            'can_be_installment' => false,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Generate default bills (Rp 200.000)
        $bill1 = Bill::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri1->id,
            'bill_type' => 'syahriah_pondok',
            'billing_config_id' => $config->id,
            'period_month' => 7,
            'period_year' => 2026,
            'amount' => 200000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        $bill2 = Bill::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri2->id,
            'bill_type' => 'syahriah_pondok',
            'billing_config_id' => $config->id,
            'period_month' => 7,
            'period_year' => 2026,
            'amount' => 200000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        // 0.a Verify validation error if notes are empty
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingExceptionCreate::class)
            ->set('excSantriIds', [$santri1->id])
            ->set('excConfigId', $config->id)
            ->set('excType', 'discount')
            ->set('excAmount', 20000.00)
            ->set('excNotes', '') // empty notes
            ->call('saveException')
            ->assertHasErrors(['excNotes']);

        // 0.b Verify validation error if discount exceeds base amount
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingExceptionCreate::class)
            ->set('excSantriIds', [$santri1->id])
            ->set('excConfigId', $config->id)
            ->set('excType', 'discount')
            ->set('excAmount', 250000.00) // exceeds config's 200,000 limit!
            ->set('excNotes', 'Diskon Kakak-Adik')
            ->call('saveException')
            ->assertHasErrors(['excAmount']);

        // 1. Test Livewire auto-select sibling button and retroactive saving
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingExceptionCreate::class)
            ->call('autoSelectSiblingDiscountRecipients')
            ->assertSet('excSantriIds', [$santri1->id, $santri2->id])
            ->set('excConfigId', $config->id)
            ->set('excType', 'discount')
            ->set('excAmount', 20000.00) // Rp 20.000 discount, so rate is Rp 180.000
            ->set('excNotes', 'Diskon Kakak-Adik')
            ->call('saveException')
            ->assertHasNoErrors();

        // Verify database has exceptions
        $this->assertDatabaseHas('billing_exceptions', [
            'billing_config_id' => $config->id,
            'person_id' => $santri1->id,
            'exception_type' => 'discount',
            'amount' => 20000.00,
        ]);

        // Verify retroactively updated bills in database
        $this->assertEquals(180000.00, $bill1->refresh()->amount);
        $this->assertEquals(180000.00, $bill2->refresh()->amount);

        // 1.b Test Livewire BillingExceptionEdit for updating the group exceptions
        \Livewire\Livewire::withQueryParams([
            'config_id' => $config->id,
            'type' => 'discount',
            'amount' => 20000.00,
            'notes' => 'Diskon Kakak-Adik'
        ])
        ->test(\App\Livewire\Keuangan\BillingExceptionEdit::class)
        ->assertSet('excSantriIds', [$santri1->id, $santri2->id])
        ->set('excAmount', 30000.00) // Increase discount to 30.000 (final rate Rp 170.000)
        ->set('excNotes', 'Diskon Saudara Kandung')
        ->set('excSantriIds', [$santri1->id]) // remove santri2 from the group!
        ->call('saveException')
        ->assertHasNoErrors();

        // Verify exceptions in database:
        // Ahmad Santri (santri1) should have new exception
        $this->assertDatabaseHas('billing_exceptions', [
            'billing_config_id' => $config->id,
            'person_id' => $santri1->id,
            'exception_type' => 'discount',
            'amount' => 30000.00,
            'notes' => 'Diskon Saudara Kandung'
        ]);

        // Budi Santri (santri2) exception should have been deleted (since we removed him)
        $this->assertDatabaseMissing('billing_exceptions', [
            'billing_config_id' => $config->id,
            'person_id' => $santri2->id
        ]);

        // Verify retroactively updated/reverted bills:
        // santri1 bill should be 170k (200k - 30k discount)
        $this->assertEquals(170000.00, $bill1->refresh()->amount);
        // santri2 bill should have reverted back to 200k since he has no more discount
        $this->assertEquals(200000.00, $bill2->refresh()->amount);

        // 2. Test showGroupMembers modal opening
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->call('showGroupMembers', $config->id, 'discount', 30000.00, 'Diskon Saudara Kandung')
            ->assertSet('showMembersModal', true)
            ->assertSet('modalGroupName', 'Diskon Saudara Kandung')
            ->assertCount('modalMembers', 1)
            ->call('closeMembersModal')
            ->assertSet('showMembersModal', false);

        // 3. Test deleteGroup and retroactive revert
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->call('deleteGroup', $config->id, 'discount', 30000.00, 'Diskon Saudara Kandung')
            ->assertHasNoErrors();

        // Verify both bills reverted to 200.000 since the whole group is deleted
        $this->assertEquals(200000.00, $bill1->refresh()->amount);
        $this->assertEquals(200000.00, $bill2->refresh()->amount);
    }

    public function test_lembar_setoran_grid_input_and_fifo(): void
    {
        // 1. Setup config and student
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Syahriah Pondok',
            'amount' => 150000.00,
            'effective_from' => '2026-01-01',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $dorm = \App\Modules\Kepengasuhan\Models\Dormitory::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Komplek A',
            'gender' => 'L',
        ]);

        $room = \App\Modules\Kepengasuhan\Models\Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $dorm->id,
            'name' => 'Kamar 1',
            'capacity' => 10,
        ]);

        $student = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Slamet Santri',
            'gender' => 'L',
        ]);

        \App\Modules\Kepengasuhan\Models\RoomAssignment::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $student->id,
            'room_id' => $room->id,
            'is_active' => true,
            'valid_from' => '2026-01-01',
        ]);

        // 2. Create bills (Jan, Feb, July 2026)
        $billJan = Bill::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $student->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 150000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 1,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        $billFeb = Bill::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $student->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 150000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 2,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        $billJuly = Bill::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $student->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 150000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 7,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        // Acting as admin
        $this->actingAs($this->admin);

        // 3. Test Livewire LembarSetoranKomplek grid inputs and FIFO
        \Livewire\Livewire::test(\App\Livewire\Keuangan\LembarSetoranKomplek::class)
            ->set('dormitoryId', $dorm->id)
            ->set('billType', 'syahriah_pondok')
            ->set('month', 7)
            ->set('year', 2026)
            ->set('paymentAmounts', [$billJuly->id => 50000.00])
            ->set('oldArrearsPayments', [$student->id => 200000.00])
            ->call('recalculateTotals')
            ->assertSet('totalChecked', 250000.00)
            ->assertSet('countChecked', 2)
            ->call('prosesSetoran')
            ->assertHasNoErrors();

        // 4. Verify results in DB
        // July bill (Bill 1) -> partial 50k paid
        $this->assertEquals(50000.00, $billJuly->refresh()->amount_paid);
        $this->assertEquals('partial', $billJuly->status);

        // Jan bill (oldest old arrears) -> full 150k paid
        $this->assertEquals(150000.00, $billJan->refresh()->amount_paid);
        $this->assertEquals('paid', $billJan->status);

        // Feb bill (second oldest) -> partial 50k paid (remaining from 200k)
        $this->assertEquals(50000.00, $billFeb->refresh()->amount_paid);
        $this->assertEquals('partial', $billFeb->status);
    }

    public function test_bulk_academic_year_generation(): void
    {
        // Setup config and student
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Syahriah Pondok',
            'amount' => 150000.00,
            'effective_from' => '2026-01-01',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $student = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Zaenal Santri',
            'gender' => 'L',
        ]);

        $student->roles()->create([
            'id' => Str::uuid()->toString(),
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
            'organization_id' => $this->pondok->id,
        ]);

        // Acting as admin
        $this->actingAs($this->admin);

        // Test Livewire BillingManager bulk academic year generation
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->set('genConfigId', $config->id)
            ->set('genYear', 2026)
            ->call('generateFullAcademicYearFromConfig')
            ->assertHasNoErrors();

        // Verify that 12 bills are created for Zaenal (July 2026 to June 2027)
        $bills = Bill::where('person_id', $student->id)
            ->where('bill_type', 'syahriah_pondok')
            ->get();

        $this->assertCount(12, $bills);
        
        $julyBill = $bills->firstWhere('period_month', 7);
        $this->assertNotNull($julyBill);
        $this->assertEquals(2026, $julyBill->period_year);

        $juneBill = $bills->firstWhere('period_month', 6);
        $this->assertNotNull($juneBill);
        $this->assertEquals(2027, $juneBill->period_year);
    }

    public function test_delete_batch_generation(): void
    {
        // Setup config and student
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Syahriah Pondok',
            'amount' => 150000.00,
            'effective_from' => '2026-01-01',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $student1 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Satu',
            'gender' => 'L',
        ]);

        $student2 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Dua',
            'gender' => 'L',
        ]);

        // Unpaid Bill
        $billUnpaid = Bill::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $student1->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 150000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'period_month' => 8,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        // Paid Bill
        $billPaid = Bill::create([
            'id' => Str::uuid()->toString(),
            'billing_config_id' => $config->id,
            'person_id' => $student2->id,
            'bill_type' => 'syahriah_pondok',
            'amount' => 150000.00,
            'amount_paid' => 150000.00,
            'status' => 'paid',
            'period_month' => 8,
            'period_year' => 2026,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        // Call batch delete
        \Livewire\Livewire::test(\App\Livewire\Keuangan\BillingManager::class)
            ->call('deleteBatchGeneration', $config->id, 8, 2026)
            ->assertHasNoErrors();

        // Verify result in DB: Unpaid deleted, Paid remains
        $this->assertNull(Bill::find($billUnpaid->id));
        $this->assertNotNull(Bill::find($billPaid->id));
    }
}
