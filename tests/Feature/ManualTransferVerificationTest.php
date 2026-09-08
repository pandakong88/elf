<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Modules\Keuangan\Models\PocketMoneyDeposit;
use App\Livewire\Keuangan\BillingManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ManualTransferVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $bendaharaPutra;
    private User $bendaharaPutri;
    private Person $santriPutra;
    private Person $santriPutri;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'bendahara-putra']);
        Role::firstOrCreate(['name' => 'bendahara-putri']);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@example.com',
            'name'  => 'Super Admin Keuangan',
        ]);
        $this->superAdmin->assignRole('super-admin');

        $personPutraUser = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Ust. Bendahara Putra',
            'gender' => 'L',
            'status' => 'aktif',
        ]);
        $this->bendaharaPutra = User::factory()->create([
            'email'     => 'putra@example.com',
            'name'      => 'Bendahara Putra',
            'person_id' => $personPutraUser->id,
        ]);
        $this->bendaharaPutra->assignRole('bendahara-putra');

        $personPutriUser = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Usth. Bendahara Putri',
            'gender' => 'P',
            'status' => 'aktif',
        ]);
        $this->bendaharaPutri = User::factory()->create([
            'email'     => 'putri@example.com',
            'name'      => 'Bendahara Putri',
            'person_id' => $personPutriUser->id,
        ]);
        $this->bendaharaPutri->assignRole('bendahara-putri');

        $this->santriPutra = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Muhammad Fatih',
            'nis'    => 'PA-2026-001',
            'gender' => 'L',
            'status' => 'aktif',
        ]);

        $this->santriPutri = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Aisyah Humaira',
            'nis'    => 'PI-2026-002',
            'gender' => 'P',
            'status' => 'aktif',
        ]);
    }

    private function createSubmission(Person $santri, string $status = 'pending', string $bank = 'BSI', float $billAmount = 100000, float $saku = 50000): ManualTransferSubmission
    {
        $bill = Bill::create([
            'person_id'   => $santri->id,
            'bill_type'   => 'syahriah',
            'title'       => 'Syahriah Santri',
            'amount'      => $billAmount,
            'amount_paid' => 0,
            'status'      => 'unpaid',
            'due_date'    => now()->addDays(7),
            'created_by'  => $this->superAdmin->id,
        ]);

        $sub = ManualTransferSubmission::create([
            'submission_code'      => 'TRX-' . strtoupper(Str::random(8)),
            'person_id'            => $santri->id,
            'bill_ids'             => [$bill->id],
            'bill_breakdown'       => [
                [
                    'bill_id'      => $bill->id,
                    'config_label' => 'Syahriah Santri',
                    'period_label' => 'Oktober 2026',
                    'amount'       => $billAmount,
                ]
            ],
            'total_bills_amount'   => $billAmount,
            'pocket_money_amount'  => $saku,
            'total_transfer_amount'=> $billAmount + $saku,
            'bank_destination'     => $bank,
            'sender_bank'          => 'BCA',
            'sender_account_name'  => 'Wali ' . $santri->name,
            'proof_image_path'     => 'transfer_proofs/sample.jpg',
            'notes'                => 'Bayar SPP & Saku',
            'status'               => $status,
        ]);

        if ($saku > 0) {
            PocketMoneyDeposit::create([
                'person_id'    => $santri->id,
                'amount'       => $saku,
                'source'       => 'manual_transfer',
                'reference_id' => $sub->id,
                'status'       => 'pending',
            ]);
        }

        return $sub;
    }

    public function test_gender_scoping_hides_cross_gender_transfers_from_treasurers(): void
    {
        $subPutra = $this->createSubmission($this->santriPutra);
        $subPutri = $this->createSubmission($this->santriPutri);

        // Putra Treasurer only sees Putra
        $this->actingAs($this->bendaharaPutra);
        Livewire::test(BillingManager::class)
            ->set('activeTab', 'transfers')
            ->assertSee($subPutra->submission_code)
            ->assertSee('Muhammad Fatih')
            ->assertDontSee($subPutri->submission_code)
            ->assertDontSee('Aisyah Humaira');

        // Putri Treasurer only sees Putri
        $this->actingAs($this->bendaharaPutri);
        Livewire::test(BillingManager::class)
            ->set('activeTab', 'transfers')
            ->assertSee($subPutri->submission_code)
            ->assertSee('Aisyah Humaira')
            ->assertDontSee($subPutra->submission_code)
            ->assertDontSee('Muhammad Fatih');
    }

    public function test_super_admin_can_filter_gender_freely(): void
    {
        $subPutra = $this->createSubmission($this->santriPutra);
        $subPutri = $this->createSubmission($this->santriPutri);

        $this->actingAs($this->superAdmin);

        // Default: see all
        Livewire::test(BillingManager::class)
            ->set('activeTab', 'transfers')
            ->assertSee($subPutra->submission_code)
            ->assertSee($subPutri->submission_code)
            // Filter Putra only
            ->set('transferGenderFilter', 'L')
            ->assertSee($subPutra->submission_code)
            ->assertDontSee($subPutri->submission_code)
            // Filter Putri only
            ->set('transferGenderFilter', 'P')
            ->assertDontSee($subPutra->submission_code)
            ->assertSee($subPutri->submission_code);
    }

    public function test_treasurer_cannot_open_or_verify_cross_gender_submission(): void
    {
        $subPutri = $this->createSubmission($this->santriPutri);

        // Putra Treasurer attempts to verify Putri submission
        $this->actingAs($this->bendaharaPutra);
        Livewire::test(BillingManager::class)
            ->call('openTransferVerifyModal', $subPutri->id)
            ->assertSet('showTransferVerifyModal', false)
            ->assertDispatched('toast-show', type: 'error')
            ->call('approveTransferSubmission', $subPutri->id)
            ->assertDispatched('toast-show', type: 'error');

        $this->assertEquals('pending', $subPutri->fresh()->status);
    }

    public function test_transfer_filters_by_search_status_and_bank(): void
    {
        $sub1 = $this->createSubmission($this->santriPutra, 'pending', 'BSI');
        $sub2 = $this->createSubmission($this->santriPutra, 'approved', 'BRI');

        $this->actingAs($this->superAdmin);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'transfers')
            ->set('transferFilterStatus', 'pending')
            ->assertSee($sub1->submission_code)
            ->assertDontSee($sub2->submission_code)
            ->set('transferFilterStatus', 'approved')
            ->assertDontSee($sub1->submission_code)
            ->assertSee($sub2->submission_code)
            ->set('transferFilterStatus', 'all')
            ->set('transferBankDestination', 'BSI')
            ->assertSee($sub1->submission_code)
            ->assertDontSee($sub2->submission_code);
    }

    public function test_successful_approval_workflow_with_pocket_money(): void
    {
        $sub = $this->createSubmission($this->santriPutra, 'pending', 'BSI', 150000, 50000);
        $bill = Bill::find($sub->bill_ids[0]);

        $this->actingAs($this->bendaharaPutra);

        Livewire::test(BillingManager::class)
            ->call('openTransferVerifyModal', $sub->id)
            ->assertSet('showTransferVerifyModal', true)
            ->call('approveTransferSubmission', $sub->id)
            ->assertSet('showTransferVerifyModal', false)
            ->assertDispatched('toast-show', type: 'success');

        $sub = $sub->fresh();
        $this->assertEquals('approved', $sub->status);
        $this->assertNotNull($sub->receipt_no);
        $this->assertNotNull($sub->verified_at);
        $this->assertEquals($this->bendaharaPutra->id, $sub->verified_by);

        // Bill marked paid
        $bill = $bill->fresh();
        $this->assertEquals('paid', $bill->status);
        $this->assertEquals(150000, $bill->amount_paid);

        // Payment record exists
        $this->assertDatabaseHas('bill_payments', [
            'bill_id' => $bill->id,
            'amount_paid' => 150000,
            'payment_method' => 'transfer',
            'receipt_no' => $sub->receipt_no,
        ]);

        // Pocket money deposit received
        $this->assertDatabaseHas('pocket_money_deposits', [
            'person_id' => $this->santriPutra->id,
            'amount' => 50000,
            'status' => 'received',
        ]);
    }

    public function test_rejection_workflow_requires_reason(): void
    {
        $sub = $this->createSubmission($this->santriPutra);

        $this->actingAs($this->bendaharaPutra);

        Livewire::test(BillingManager::class)
            ->call('openTransferVerifyModal', $sub->id)
            ->set('transferRejectionReason', '')
            ->call('rejectTransferSubmission', $sub->id)
            ->assertDispatched('toast-show', type: 'error');

        $this->assertEquals('pending', $sub->fresh()->status);

        // Reject with preset
        Livewire::test(BillingManager::class)
            ->call('openTransferVerifyModal', $sub->id)
            ->call('setQuickRejectionReason', 'Bukti transfer buram atau tidak terbaca')
            ->assertSet('transferRejectionReason', 'Bukti transfer buram atau tidak terbaca')
            ->call('rejectTransferSubmission', $sub->id)
            ->assertDispatched('toast-show', type: 'warning');

        $sub = $sub->fresh();
        $this->assertEquals('rejected', $sub->status);
        $this->assertEquals('Bukti transfer buram atau tidak terbaca', $sub->rejection_reason);
    }
}
