<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\FundDistribution;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Livewire\Keuangan\BillingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettlementReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Person $santriPutra;
    private Dormitory $dormPutra;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bendahara-pondok', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'bendahara@example.com',
            'name'  => 'Bendahara Pusat',
        ]);
        $this->admin->assignRole('super-admin');

        $this->santriPutra = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Muhammad Fatih',
            'nis'    => '2026101',
            'gender' => 'L',
            'status' => 'aktif',
        ]);

        $this->dormPutra = Dormitory::create([
            'id'        => (string) Str::uuid(),
            'name'      => 'Komplek Al-Ghazali',
            'code'      => 'AGH',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $room = Room::create([
            'id'           => (string) Str::uuid(),
            'dormitory_id' => $this->dormPutra->id,
            'name'         => 'Kamar 01',
            'code'         => 'K01',
            'capacity'     => 10,
            'is_active'    => true,
        ]);

        RoomAssignment::create([
            'id'         => (string) Str::uuid(),
            'person_id'  => $this->santriPutra->id,
            'room_id'    => $room->id,
            'valid_from' => now()->subMonth(),
            'is_active'  => true,
        ]);
    }

    public function test_settlement_report_gateway_with_pocket_money_and_dormitory_breakdown(): void
    {
        $this->actingAs($this->admin);

        PaymentTransaction::create([
            'id'                  => (string) Str::uuid(),
            'merchant_order_id'   => 'ORD-DOKU-001',
            'person_id'           => $this->santriPutra->id,
            'bill_ids'            => ['bill-1', 'bill-2'],
            'bill_breakdown'      => [
                [
                    'bill_id'      => 'bill-1',
                    'bill_type'    => 'syahriah_pondok',
                    'config_label' => 'Syahriah Pondok',
                    'pay_portion'  => 200000,
                ],
                [
                    'bill_id'      => 'bill-2',
                    'bill_type'    => 'kas_komplek',
                    'config_label' => 'Kas Asrama',
                    'pay_portion'  => 25000,
                ],
            ],
            'bill_amount'         => 225000,
            'pocket_money_amount' => 50000,
            'mdr_amount'          => 4000,
            'total_amount'        => 279000,
            'net_amount'          => 275000,
            'payment_channel'     => 'QRIS',
            'status'              => 'success',
            'callback_received_at'=> now(),
        ]);

        $test = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->assertSee('Settlement Report')
            ->assertSee('279.000')
            ->assertSee('4.000')
            ->assertSee('275.000')
            ->assertSee('Syahriah / SPP Pondok Putra')
            ->assertSee('Kas Komplek / Asrama (Total)')
            ->assertSee('Titipan Uang Saku Santri')
            ->assertSee('Komplek Al-Ghazali');

        $report = $test->get('settlementReport');
        $this->assertEquals(279000, $report['total_gross']);
        $this->assertEquals(4000, $report['total_mdr']);
        $this->assertEquals(275000, $report['total_net']);
        $this->assertEquals(1, $report['total_trx']);
    }

    public function test_settlement_report_cashier_and_manual_transfer_with_pocket_money(): void
    {
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'kitab',
            'title'       => 'Biaya Kitab',
            'amount'      => 100000,
            'amount_paid' => 100000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $bill->id,
            'amount_paid'    => 100000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-001',
            'logged_by'      => $this->admin->id,
        ]);

        ManualTransferSubmission::create([
            'submission_code'       => 'TRF-MANUAL-01',
            'person_id'             => $this->santriPutra->id,
            'bill_ids'              => [$bill->id],
            'bill_breakdown'        => [
                ['bill_id' => $bill->id, 'config_label' => 'Biaya Kitab', 'amount' => 100000],
            ],
            'total_bills_amount'    => 100000,
            'pocket_money_amount'   => 30000,
            'total_transfer_amount' => 130000,
            'bank_destination'      => 'BSI',
            'sender_bank'           => 'BCA',
            'sender_account_name'   => 'Orang Tua Fatih',
            'proof_image_path'      => 'proofs/test.jpg',
            'status'                => 'approved',
            'verified_at'           => now(),
            'verified_by'           => $this->admin->id,
        ]);

        $test = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->set('settlementSource', 'kasir');

        $report = $test->get('settlementReport');
        $this->assertEquals(130000, $report['total_gross']);
        $this->assertEquals(130000, $report['total_net']);
    }

    public function test_settlement_pdf_download(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('keuangan.settlement.pdf', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_slip_kas_komplek_pdf_download(): void
    {
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'kas_komplek',
            'title'       => 'Kas Komplek AGH',
            'amount'      => 25000,
            'amount_paid' => 25000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $bill->id,
            'amount_paid'    => 25000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-KAS-01',
            'logged_by'      => $this->admin->id,
        ]);

        $response = $this->get(route('keuangan.settlement.slip-komplek', [
            'dormitoryId' => $this->dormPutra->id,
            'date_from'   => now()->startOfMonth()->toDateString(),
            'date_to'     => now()->toDateString(),
            'source'      => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_save_settlement_snapshot_to_fund_distribution(): void
    {
        $this->actingAs($this->admin);

        PaymentTransaction::create([
            'id'                  => (string) Str::uuid(),
            'merchant_order_id'   => 'ORD-SNAP-01',
            'person_id'           => $this->santriPutra->id,
            'bill_ids'            => ['bill-snap'],
            'bill_breakdown'      => [
                [
                    'bill_id'      => 'bill-snap',
                    'bill_type'    => 'syahriah_pondok',
                    'config_label' => 'Syahriah Pondok',
                    'pay_portion'  => 150000,
                ],
            ],
            'bill_amount'         => 150000,
            'pocket_money_amount' => 0,
            'mdr_amount'          => 2000,
            'total_amount'        => 152000,
            'net_amount'          => 150000,
            'status'              => 'success',
            'callback_received_at'=> now(),
        ]);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->set('settlementNotes', 'Tutup buku pekan ke-2')
            ->call('saveSettlementSnapshot');

        $this->assertDatabaseHas('fund_distributions', [
            'notes' => 'Tutup buku pekan ke-2',
            'total_net' => 150000,
            'status' => 'distributed',
        ]);
    }
}
