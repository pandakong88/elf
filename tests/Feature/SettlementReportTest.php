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
            ->assertSee('Rekonsiliasi &amp; Tutup Buku Kas', false)
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

    public function test_settlement_slip_kategori_pdf_download(): void
    {
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'madrasah',
            'title'       => 'Madrasah Diniyah',
            'amount'      => 75000,
            'amount_paid' => 75000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $bill->id,
            'amount_paid'    => 75000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-MDR-01',
            'logged_by'      => $this->admin->id,
        ]);

        $response = $this->get(route('keuangan.settlement.slip-kategori', [
            'categoryKey' => 'madrasah',
            'date_from'   => now()->startOfMonth()->toDateString(),
            'date_to'     => now()->toDateString(),
            'source'      => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_settlement_report_3_sources_breakdown(): void
    {
        $this->actingAs($this->admin);

        // 1. Payment Gateway (DOKU)
        PaymentTransaction::create([
            'id'                  => (string) Str::uuid(),
            'merchant_order_id'   => 'ORD-GW-01',
            'person_id'           => $this->santriPutra->id,
            'bill_ids'            => ['bill-gw'],
            'bill_breakdown'      => [
                ['bill_id' => 'bill-gw', 'bill_type' => 'syahriah_pondok', 'config_label' => 'Syahriah Pondok', 'pay_portion' => 100000],
            ],
            'bill_amount'         => 100000,
            'pocket_money_amount' => 50000,
            'mdr_amount'          => 2500,
            'total_amount'        => 152500,
            'net_amount'          => 150000,
            'payment_channel'     => 'QRIS',
            'status'              => 'success',
            'callback_received_at'=> now(),
        ]);

        // 2. Manual Transfer Submission
        $billTrf = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'kitab',
            'title'       => 'Kitab Kuning',
            'amount'      => 60000,
            'amount_paid' => 60000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $billTrf->id,
            'amount_paid'    => 60000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'transfer_manual',
            'receipt_no'     => 'TRF-001',
            'logged_by'      => $this->admin->id,
        ]);

        ManualTransferSubmission::create([
            'submission_code'       => 'TRF-001',
            'person_id'             => $this->santriPutra->id,
            'bill_ids'              => [$billTrf->id],
            'bill_breakdown'        => [
                ['bill_id' => $billTrf->id, 'config_label' => 'Kitab Kuning', 'amount' => 60000],
            ],
            'total_bills_amount'    => 60000,
            'pocket_money_amount'   => 40000,
            'total_transfer_amount' => 100000,
            'bank_destination'      => 'BSI',
            'sender_bank'           => 'Mandiri',
            'sender_account_name'   => 'Wali Fatih',
            'proof_image_path'      => 'proofs/trf.jpg',
            'status'                => 'approved',
            'verified_at'           => now(),
            'verified_by'           => $this->admin->id,
        ]);

        // 3. Cashier Cash
        $billCash = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'madrasah',
            'title'       => 'Madrasah Diniyah',
            'amount'      => 50000,
            'amount_paid' => 50000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $billCash->id,
            'amount_paid'    => 50000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-CASH-01',
            'logged_by'      => $this->admin->id,
        ]);

        $test = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->set('settlementSource', 'all');

        $report = $test->get('settlementReport');

        $this->assertEquals(150000, $report['gateway_net']);
        $this->assertEquals(2500, $report['gateway_mdr']);
        $this->assertEquals(100000, $report['transfer_amount']);
        $this->assertEquals(50000, $report['cash_amount']);
        $this->assertEquals(300000, $report['total_net']);
        $this->assertEquals(3, $report['total_trx']);

        // Test Category Detail Modal
        $test->call('openCategoryDetailModal', 'madrasah')
            ->assertSet('showCategoryModal', true)
            ->assertSet('modalCategoryKey', 'madrasah')
            ->assertSee('Syahriah Madrasah')
            ->assertSee('Muhammad Fatih');
    }

    public function test_batch_slips_pdf_download(): void
    {
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'madrasah',
            'title'       => 'Madrasah Diniyah',
            'amount'      => 80000,
            'amount_paid' => 80000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $bill->id,
            'amount_paid'    => 80000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-BATCH-01',
            'logged_by'      => $this->admin->id,
        ]);

        $response = $this->get(route('keuangan.settlement.batch-slips', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_snapshot_berita_acara_pdf_download(): void
    {
        $this->actingAs($this->admin);

        $snapshot = FundDistribution::create([
            'id'             => (string) Str::uuid(),
            'period_from'    => now()->startOfMonth()->toDateString(),
            'period_to'      => now()->toDateString(),
            'gender'         => 'A',
            'total_gross'    => 500000,
            'total_mdr'      => 5000,
            'total_net'      => 495000,
            'breakdown'      => [
                'categories' => [
                    ['label' => 'Syahriah Madrasah', 'amount' => 495000, 'count' => 5],
                ],
                'sources'    => [
                    'gateway_net'     => 300000,
                    'gateway_mdr'     => 5000,
                    'gateway_gross'   => 305000,
                    'transfer_amount' => 100000,
                    'cash_amount'     => 95000,
                ],
            ],
            'online_amount'  => 300000,
            'manual_amount'  => 195000,
            'online_count'   => 3,
            'manual_count'   => 2,
            'status'         => 'distributed',
            'distributed_at' => now(),
            'distributed_by' => $this->admin->id,
            'notes'          => 'Tutup kas resmi',
        ]);

        $response = $this->get(route('keuangan.settlement.snapshot-pdf', $snapshot->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_interactive_handover_checklist_and_note(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->call('toggleHandoverStatus', 'madrasah', 'Diserahkan ke Ust. Hasan')
            ->assertSet('distributionChecklist.madrasah.handed_over', true)
            ->assertSet('distributionChecklist.madrasah.recipient_note', 'Diserahkan ke Ust. Hasan')
            ->call('updateHandoverNote', 'madrasah', 'Diserahkan ke Ust. Hasan (Lunas Tunai)')
            ->assertSet('distributionChecklist.madrasah.recipient_note', 'Diserahkan ke Ust. Hasan (Lunas Tunai)')
            ->call('toggleHandoverStatus', 'madrasah')
            ->assertSet('distributionChecklist.madrasah.handed_over', false);
    }

    public function test_delete_settlement_snapshot_by_super_admin(): void
    {
        $this->actingAs($this->admin);

        $snapshot = FundDistribution::create([
            'id'             => (string) Str::uuid(),
            'period_from'    => now()->startOfMonth()->toDateString(),
            'period_to'      => now()->toDateString(),
            'gender'         => 'A',
            'total_gross'    => 100000,
            'total_mdr'      => 0,
            'total_net'      => 100000,
            'status'         => 'distributed',
            'distributed_at' => now(),
            'distributed_by' => $this->admin->id,
        ]);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->call('deleteSettlementSnapshot', $snapshot->id);

        $this->assertDatabaseMissing('fund_distributions', [
            'id' => $snapshot->id,
        ]);
    }

    public function test_settlement_excel_export_download(): void
    {
        $this->actingAs($this->admin);

        PaymentTransaction::create([
            'id'                  => (string) Str::uuid(),
            'merchant_order_id'   => 'ORD-EXCEL-001',
            'person_id'           => $this->santriPutra->id,
            'bill_ids'            => ['bill-excel-1'],
            'bill_breakdown'      => [
                [
                    'bill_id'      => 'bill-excel-1',
                    'bill_type'    => 'syahriah_pondok',
                    'config_label' => 'Syahriah Pondok',
                    'pay_portion'  => 350000,
                ],
            ],
            'bill_amount'         => 350000,
            'pocket_money_amount' => 50000,
            'admin_fee'           => 4000,
            'mdr_amount'          => 3000,
            'net_amount'          => 397000,
            'total_amount'        => 404000,
            'payment_channel'     => 'QRIS',
            'status'              => 'success',
            'callback_received_at'=> now(),
        ]);

        $response = $this->get(route('keuangan.settlement.export-excel', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type', ''), 'spreadsheetml') ||
            str_contains($response->headers->get('content-disposition', ''), '.xlsx')
        );
    }

    public function test_doku_payout_tracking_and_snapshot(): void
    {
        $this->actingAs($this->admin);

        PaymentTransaction::create([
            'id'                  => (string) Str::uuid(),
            'merchant_order_id'   => 'ORD-PO-001',
            'person_id'           => $this->santriPutra->id,
            'bill_ids'            => ['bill-po-1'],
            'bill_breakdown'      => [
                [
                    'bill_id'      => 'bill-po-1',
                    'bill_type'    => 'syahriah_pondok',
                    'config_label' => 'Syahriah Pondok',
                    'pay_portion'  => 250000,
                ],
            ],
            'bill_amount'         => 250000,
            'pocket_money_amount' => 0,
            'admin_fee'           => 4000,
            'mdr_amount'          => 2000,
            'net_amount'          => 248000,
            'total_amount'        => 254000,
            'payment_channel'     => 'QRIS',
            'status'              => 'success',
            'callback_received_at'=> now(),
        ]);

        $component = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->assertSet('dokuPayoutStatus.is_disbursed', false)
            ->call('toggleDokuPayoutStatus', 'BSI Rekening Utama', 'Batch #009 Disbursed')
            ->assertSet('dokuPayoutStatus.is_disbursed', true)
            ->assertSet('dokuPayoutStatus.destination_bank', 'BSI Rekening Utama')
            ->call('saveSettlementSnapshot');

        $this->assertDatabaseHas('fund_distributions', [
            'status' => 'distributed',
        ]);

        $snapshot = FundDistribution::latest()->first();
        $this->assertNotNull($snapshot->breakdown);
        $this->assertTrue($snapshot->breakdown['doku_payout']['is_disbursed']);
        $this->assertEquals('BSI Rekening Utama', $snapshot->breakdown['doku_payout']['destination_bank']);
    }

    public function test_settlement_bank_filter_and_target_realization(): void
    {
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'id'          => (string) Str::uuid(),
            'person_id'   => $this->santriPutra->id,
            'bill_type'   => 'kitab',
            'amount'      => 150000,
            'amount_paid' => 150000,
            'status'      => 'paid',
            'created_by'  => $this->admin->id,
        ]);

        ManualTransferSubmission::create([
            'id'                    => (string) Str::uuid(),
            'submission_code'       => 'TRF-BSI-999',
            'person_id'             => $this->santriPutra->id,
            'bill_ids'              => [$bill->id],
            'bill_breakdown'        => [
                ['bill_id' => $bill->id, 'config_label' => 'Kitab', 'amount' => 150000],
            ],
            'total_bills_amount'    => 150000,
            'pocket_money_amount'   => 0,
            'total_transfer_amount' => 150000,
            'bank_destination'      => 'BSI',
            'sender_bank'           => 'BCA',
            'sender_account_name'   => 'Wali Santri',
            'proof_image_path'      => 'proofs/test.jpg',
            'status'                => 'approved',
            'verified_at'           => now(),
            'verified_by'           => $this->admin->id,
        ]);

        BillPayment::create([
            'id'             => (string) Str::uuid(),
            'bill_id'        => $bill->id,
            'receipt_no'     => 'KW-BSI-999',
            'amount_paid'    => 150000,
            'payment_method' => 'transfer_bsi',
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
            'notes'          => 'Transfer manual BSI [TRF-BSI-999]',
        ]);

        $component = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->set('settlementSource', 'kasir')
            ->set('settlementBankFilter', 'BSI');

        $report = $component->get('settlementReport');
        $this->assertEquals(150000, $report['transfer_amount']);
        $this->assertGreaterThan(0, $report['realization_percent']);
    }

    public function test_quick_date_presets_and_active_tracking(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->assertSet('settlementPreset', 'this_month')
            ->call('setSettlementQuickDate', 'today')
            ->assertSet('settlementPreset', 'today')
            ->assertSet('settlementDateFrom', now()->toDateString())
            ->assertSet('settlementDateTo', now()->toDateString())
            ->call('setSettlementQuickDate', 'last_7_days')
            ->assertSet('settlementPreset', 'last_7_days')
            ->assertSet('settlementDateFrom', now()->subDays(6)->toDateString())
            ->assertSet('settlementDateTo', now()->toDateString())
            ->call('setSettlementQuickDate', 'last_month')
            ->assertSet('settlementPreset', 'last_month')
            ->assertSet('settlementDateFrom', now()->subMonth()->startOfMonth()->toDateString())
            ->assertSet('settlementDateTo', now()->subMonth()->endOfMonth()->toDateString())
            ->set('settlementDateFrom', '2026-05-01')
            ->assertSet('settlementPreset', 'custom');
    }

    public function test_settlement_excel_export_with_gender_parameter(): void
    {
        $this->actingAs($this->admin);

        $responsePutra = $this->get(route('keuangan.settlement.export-excel', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
            'gender'    => 'L',
        ]));

        $responsePutra->assertStatus(200);
        $this->assertTrue(str_contains($responsePutra->headers->get('content-disposition', ''), 'Putra'));

        $responsePutri = $this->get(route('keuangan.settlement.export-excel', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
            'gender'    => 'P',
        ]));

        $responsePutri->assertStatus(200);
        $this->assertTrue(str_contains($responsePutri->headers->get('content-disposition', ''), 'Putri'));
    }

    public function test_settlement_pdf_with_gender_parameter(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('keuangan.settlement.pdf', [
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->toDateString(),
            'source'    => 'all',
            'gender'    => 'L',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
