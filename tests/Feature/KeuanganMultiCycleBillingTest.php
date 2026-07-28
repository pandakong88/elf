<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Organization;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Services\BillingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class KeuanganMultiCycleBillingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Organization $pondok;
    private Person $santri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pondok = Organization::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Pondok Pesantren Al-Fithroh',
            'slug' => 'ponpes-al-fithroh',
            'type' => 'pondok',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Bendahara Utama',
            'email' => 'bendahara@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Santri Test Multi Cycle',
            'gender' => 'L',
            'is_active' => true,
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $this->santri->id,
            'organization_id' => $this->pondok->id,
            'role_type' => 'santri',
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
        ]);
    }

    public function test_multi_cycle_billing_generation_and_due_date(): void
    {
        // 1. Create a 2x monthly billing configuration with fixed due day (15th of the month)
        $config = BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => 'syahriah_pondok',
            'label' => 'Iuran Makan 2x Sebulan',
            'amount' => 150000,
            'interval' => 'biweekly',
            'due_day_type' => 'fixed_day',
            'due_day_value' => 15,
            'effective_from' => now()->toDateString(),
            'target_type' => 'all',
            'target_filters' => ['genders' => ['L', 'P'], 'residence' => ['mukim', 'laju']],
            'can_be_installment' => false,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $service = app(BillingService::class);

        // 2. Generate Gelombang 1 for July 2026
        $res1 = $service->generateBillsFromConfig($config->id, 7, 2026, $this->admin->id, 1);
        $this->assertEquals(1, $res1['generated']);
        $this->assertEquals(0, $res1['skipped']);

        // 3. Generate Gelombang 2 for July 2026
        $res2 = $service->generateBillsFromConfig($config->id, 7, 2026, $this->admin->id, 2);
        $this->assertEquals(1, $res2['generated']);
        $this->assertEquals(0, $res2['skipped']);

        // 4. Try generating Gelombang 1 again (should skip as duplicate)
        $res1Dup = $service->generateBillsFromConfig($config->id, 7, 2026, $this->admin->id, 1);
        $this->assertEquals(0, $res1Dup['generated']);
        $this->assertEquals(1, $res1Dup['skipped']);

        // 5. Assert database records
        $bills = Bill::where('person_id', $this->santri->id)
            ->where('billing_config_id', $config->id)
            ->get();

        $this->assertCount(2, $bills);
        $this->assertEquals(1, $bills[0]->period_sub);
        $this->assertEquals(2, $bills[1]->period_sub);
        $this->assertEquals('2026-07-15', $bills[0]->due_date->toDateString());
    }
}
