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
use App\Livewire\Keuangan\BillingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettlementOpnameAndCategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Person $santri;
    private Dormitory $dorm;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name'  => 'Admin Pusat',
        ]);
        $this->admin->assignRole('super-admin');

        $this->santri = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Ahmad Zaki',
            'nis'    => '2026999',
            'gender' => 'L',
            'status' => 'aktif',
        ]);

        $this->dorm = Dormitory::create([
            'id'        => (string) Str::uuid(),
            'name'      => 'Komplek Utama',
            'code'      => 'K-UTM',
            'gender'    => 'L',
            'is_active' => true,
        ]);

        $room = Room::create([
            'id'           => (string) Str::uuid(),
            'dormitory_id' => $this->dorm->id,
            'name'         => 'Kamar 01',
            'gender'       => 'L',
            'capacity'     => 10,
        ]);

        RoomAssignment::create([
            'id'         => (string) Str::uuid(),
            'person_id'  => $this->santri->id,
            'room_id'    => $room->id,
            'valid_from' => now()->subMonths(6),
            'is_active'  => true,
        ]);
    }

    public function test_cash_opname_calculator_flow(): void
    {
        // Setup 1 bill dibayar tunai via kasir sebesar Rp 180.000
        $bill = Bill::create([
            'person_id'   => $this->santri->id,
            'bill_type'   => 'syahriah_pondok',
            'title'       => 'Syahriah Pondok',
            'amount'      => 180000,
            'amount_paid' => 180000,
            'status'      => 'paid',
            'due_date'    => now()->endOfMonth()->toDateString(),
            'created_by'  => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'cash',
            'amount_paid'    => 180000,
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
        ]);

        // Uji Livewire component
        $lw = Livewire::actingAs($this->admin)
            ->test(BillingManager::class)
            ->call('openCashOpnameModal')
            ->assertSet('showCashOpnameModal', true)
            // 1. Test Solusi 3: Tombol 1-Click Auto Match Klop
            ->call('autoMatchCashOpname')
            ->assertSet('cashOpnameMode', 'quick')
            ->assertSet('cashOpnameQuickTotal', 180000)
            ->assertSet('cashOpnameTotal', 180000)
            ->assertSet('cashOpnameDifference', 0)
            // 2. Test Solusi 1: Mode Cepat (Input Nominal Langsung + Shortcut Tambah)
            ->call('resetCashOpname')
            ->assertSet('cashOpnameTotal', 0)
            ->assertSet('cashOpnameDifference', -180000)
            ->call('setCashOpnameMode', 'quick')
            ->set('cashOpnameQuickTotal', 100000)
            ->call('addQuickOpnameAmount', 80000) // 100k + 80k = 180k
            ->assertSet('cashOpnameTotal', 180000)
            ->assertSet('cashOpnameDifference', 0)
            // 3. Test Mode Rinci (Pecahan Lembaran)
            ->call('setCashOpnameMode', 'breakdown')
            ->call('incrementOpnameCount', 100000, 1) // 100k x 1 = 100k
            ->call('incrementOpnameCount', 50000, 1)  // 50k x 1 = 50k
            ->call('incrementOpnameCount', 20000, 1)  // 20k x 1 = 20k
            ->call('incrementOpnameCount', 10000, 1)  // 10k x 1 = 10k -> Total 180k
            ->assertSet('cashOpnameTotal', 180000)
            ->assertSet('cashOpnameDifference', 0)
            // 4. Simpan Kas Opname
            ->set('cashOpnameNotes', 'Fisik kasir klop 100% pas')
            ->call('saveCashOpname')
            ->assertSet('showCashOpnameModal', false)
            ->assertSet('hasSavedOpname', true);
    }

    public function test_settlement_category_filter_isolates_pos(): void
    {
        // Buat 2 tagihan berbeda jenis dan bayar
        $billPondok = Bill::create([
            'person_id'   => $this->santri->id,
            'bill_type'   => 'syahriah_pondok',
            'title'       => 'Syahriah Pondok',
            'amount'      => 100000,
            'amount_paid' => 100000,
            'status'      => 'paid',
            'due_date'    => now()->endOfMonth()->toDateString(),
            'created_by'  => $this->admin->id,
        ]);
        BillPayment::create([
            'bill_id'        => $billPondok->id,
            'payment_method' => 'cash',
            'amount_paid'    => 100000,
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
        ]);

        $billKomplek = Bill::create([
            'person_id'   => $this->santri->id,
            'bill_type'   => 'kas_komplek',
            'title'       => 'Kas Komplek',
            'amount'      => 25000,
            'amount_paid' => 25000,
            'status'      => 'paid',
            'due_date'    => now()->endOfMonth()->toDateString(),
            'created_by'  => $this->admin->id,
        ]);
        BillPayment::create([
            'bill_id'        => $billKomplek->id,
            'payment_method' => 'cash',
            'amount_paid'    => 25000,
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
        ]);

        // Filter: ALL
        $lw = Livewire::actingAs($this->admin)->test(BillingManager::class);
        $reportAll = $lw->get('settlementReport');
        $this->assertEquals(125000, $reportAll['total_net']);

        // Filter: syahriah_pondok
        $lw->set('settlementCategoryFilter', 'syahriah_pondok');
        $reportPondok = $lw->get('settlementReport');
        $this->assertEquals(100000, $reportPondok['total_net']);
        $this->assertEquals(100000, $reportPondok['cash_amount']);
        $this->assertEmpty($reportPondok['dormitory_breakdown']); // Bukan kas komplek, dorm breakdown kosong

        // Filter: kas_komplek
        $lw->set('settlementCategoryFilter', 'kas_komplek');
        $reportKomplek = $lw->get('settlementReport');
        $this->assertEquals(25000, $reportKomplek['total_net']);
        $this->assertEquals(25000, $reportKomplek['cash_amount']);
        $this->assertNotEmpty($reportKomplek['dormitory_breakdown']);

        // Filter: custom pos iuran (misal kebersihan) - memastikan query totalBilledQuery tidak error kolom 'name'
        $lw->set('settlementCategoryFilter', 'kebersihan');
        $reportKebersihan = $lw->get('settlementReport');
        $this->assertEquals(0, $reportKebersihan['total_net']);
    }

    public function test_save_snapshot_includes_cash_opname_and_category_filter(): void
    {
        $bill = Bill::create([
            'person_id'   => $this->santri->id,
            'bill_type'   => 'syahriah_pondok',
            'title'       => 'Syahriah Pondok',
            'amount'      => 100000,
            'amount_paid' => 100000,
            'status'      => 'paid',
            'due_date'    => now()->endOfMonth()->toDateString(),
            'created_by'  => $this->admin->id,
        ]);
        BillPayment::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'cash',
            'amount_paid'    => 100000,
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
        ]);

        $lw = Livewire::actingAs($this->admin)
            ->test(BillingManager::class)
            ->set('settlementCategoryFilter', 'syahriah_pondok')
            ->call('openCashOpnameModal')
            ->call('incrementOpnameCount', 100000, 1)
            ->set('cashOpnameNotes', 'Catatan Klop')
            ->call('saveCashOpname')
            ->call('saveSettlementSnapshot');

        $distribution = FundDistribution::first();
        $this->assertNotNull($distribution);
        $this->assertEquals('syahriah_pondok', $distribution->breakdown['category_filter'] ?? null);
        $this->assertNotNull($distribution->breakdown['cash_opname'] ?? null);
        $this->assertEquals(100000, $distribution->breakdown['cash_opname']['total']);
        $this->assertEquals('Catatan Klop', $distribution->breakdown['cash_opname']['notes']);
    }

    public function test_controller_pdf_and_excel_export_with_category(): void
    {
        $period = now()->format('Y-m');

        // Test export Excel dengan query category
        $responseExcel = $this->actingAs($this->admin)->get(route('keuangan.settlement.export-excel', [
            'period'   => $period,
            'category' => 'kas_komplek',
        ]));
        $responseExcel->assertStatus(200);

        // Test download PDF Rekap dengan query category
        $responsePdf = $this->actingAs($this->admin)->get(route('keuangan.settlement.pdf', [
            'period'   => $period,
            'category' => 'syahriah_pondok',
        ]));
        $responsePdf->assertStatus(200);

        // Test download Batch Slips dengan query category
        $responseSlips = $this->actingAs($this->admin)->get(route('keuangan.settlement.batch-slips', [
            'period'   => $period,
            'category' => 'kas_komplek',
        ]));
        $responseSlips->assertStatus(200);
    }
}
