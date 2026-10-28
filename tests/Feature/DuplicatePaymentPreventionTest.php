<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Livewire\Keuangan\BillingManager;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class DuplicatePaymentPreventionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Person $santri;
    private Bill $bill;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'name'  => 'Admin Keuangan',
        ]);
        $this->admin->assignRole('super-admin');

        $this->santri = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Hasna Althofunnisa',
            'nis'    => '20260167',
            'gender' => 'P',
            'status' => 'aktif',
        ]);

        $this->bill = Bill::create([
            'person_id'    => $this->santri->id,
            'bill_type'    => 'kas_komplek',
            'title'        => 'Kas Komplek Oktober 2026',
            'amount'       => 2000.00,
            'amount_paid'  => 0.00,
            'period_month' => 10,
            'period_year'  => 2026,
            'status'       => 'unpaid',
            'created_by'   => $this->admin->id,
        ]);
    }

    public function test_cannot_approve_transfer_submission_if_bill_already_paid_at_cashier(): void
    {
        // 1. Wali santri mengajukan bukti transfer via portal
        $sub = ManualTransferSubmission::create([
            'submission_code'       => 'TRF-TEST01',
            'person_id'             => $this->santri->id,
            'bill_ids'              => [$this->bill->id],
            'bill_breakdown'        => [
                [
                    'bill_id'      => $this->bill->id,
                    'bill_type'    => 'kas_komplek',
                    'config_label' => 'Kas Komplek Oktober 2026',
                    'period_label' => 'Oktober 2026',
                    'amount'       => 2000.00,
                ],
            ],
            'total_bills_amount'    => 2000.00,
            'pocket_money_amount'   => 0.00,
            'total_transfer_amount' => 2000.00,
            'bank_destination'      => 'BSI',
            'proof_image_path'      => 'proofs/test1.jpg',
            'status'                => 'pending',
        ]);

        // 2. Di kasir, petugas menceklis manual (CASH Rp 2.000)
        BillPayment::create([
            'id'             => (string) Str::uuid(),
            'bill_id'        => $this->bill->id,
            'receipt_no'     => 'KAS-001',
            'amount_paid'    => 2000.00,
            'payment_method' => 'cash',
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
            'notes'          => 'Pembayaran Kasir',
        ]);
        $this->bill->recalculateStatus();
        $this->assertEquals('paid', $this->bill->fresh()->status);
        $this->assertEquals(2000.00, (float)$this->bill->fresh()->amount_paid);

        // 3. Bendahara mencoba menyetujui bukti transfer portal
        $this->actingAs($this->admin);

        Livewire::test(BillingManager::class)
            ->call('approveTransferSubmission', $sub->id)
            ->assertDispatched('toast-show');

        // 4. Verifikasi bahwa transfer TIDAK disetujui dan tidak ada pembayaran kedua
        $this->assertEquals('pending', $sub->fresh()->status);
        $this->assertEquals(1, BillPayment::where('bill_id', $this->bill->id)->count());
        $this->assertEquals(2000.00, (float)$this->bill->fresh()->amount_paid);
    }

    public function test_can_approve_transfer_after_voiding_cashier_payment(): void
    {
        // 1. Pengajuan transfer
        $sub = ManualTransferSubmission::create([
            'submission_code'       => 'TRF-TEST02',
            'person_id'             => $this->santri->id,
            'bill_ids'              => [$this->bill->id],
            'bill_breakdown'        => [
                [
                    'bill_id'      => $this->bill->id,
                    'bill_type'    => 'kas_komplek',
                    'config_label' => 'Kas Komplek Oktober 2026',
                    'period_label' => 'Oktober 2026',
                    'amount'       => 2000.00,
                ],
            ],
            'total_bills_amount'    => 2000.00,
            'pocket_money_amount'   => 0.00,
            'total_transfer_amount' => 2000.00,
            'bank_destination'      => 'BSI',
            'proof_image_path'      => 'proofs/test2.jpg',
            'status'                => 'pending',
        ]);

        // 2. Pembayaran kasir salah input
        $cashierPayment = BillPayment::create([
            'id'             => (string) Str::uuid(),
            'bill_id'        => $this->bill->id,
            'receipt_no'     => 'KAS-002',
            'amount_paid'    => 2000.00,
            'payment_method' => 'cash',
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
            'notes'          => 'Salah input kasir',
        ]);
        $this->bill->recalculateStatus();

        // 3. Batalkan pembayaran kasir
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingManager::class);
        $component->set('paymentToVoidId', $cashierPayment->id);
        $component->call('executeVoidPayment');

        $this->assertEquals(0, BillPayment::where('bill_id', $this->bill->id)->count());
        $this->assertEquals('unpaid', $this->bill->fresh()->status);

        // 4. Sekarang setujui pengajuan transfer portal
        $component->call('approveTransferSubmission', $sub->id);

        $this->assertEquals('approved', $sub->fresh()->status);
        $this->assertEquals(1, BillPayment::where('bill_id', $this->bill->id)->count());
        $this->assertEquals('transfer', BillPayment::where('bill_id', $this->bill->id)->first()->payment_method);
        $this->assertEquals(2000.00, (float)$this->bill->fresh()->amount_paid);
    }

    public function test_rekonsiliasi_detects_existing_duplicate_and_allows_void(): void
    {
        // 1. Simulasikan tagihan yang memiliki data duplikat (CASH + TRANSFER)
        $payCash = BillPayment::create([
            'id'             => (string) Str::uuid(),
            'bill_id'        => $this->bill->id,
            'receipt_no'     => 'KAS-003',
            'amount_paid'    => 2000.00,
            'payment_method' => 'cash',
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
            'notes'          => 'Kasir Tunai',
        ]);

        $payTransfer = BillPayment::create([
            'id'             => (string) Str::uuid(),
            'bill_id'        => $this->bill->id,
            'receipt_no'     => 'TRF-003',
            'amount_paid'    => 2000.00,
            'payment_method' => 'transfer',
            'payment_date'   => now()->toDateString(),
            'logged_by'      => $this->admin->id,
            'notes'          => 'Transfer Portal',
        ]);
        $this->bill->recalculateStatus();

        // 2. Cek apakah settlementDuplicates mendeteksinya
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingManager::class)
            ->set('activeTab', 'settlement')
            ->set('settlementDateFrom', now()->startOfMonth()->toDateString())
            ->set('settlementDateTo', now()->toDateString());

        $duplicates = $component->get('settlementDuplicates');
        $this->assertCount(1, $duplicates);
        $this->assertEquals($this->bill->id, $duplicates[0]['bill_id']);
        $this->assertEquals(4000.00, $duplicates[0]['total_paid']);
        $this->assertEquals(2000.00, $duplicates[0]['overpaid']);

        // 3. Gunakan voidDuplicatePayment untuk membatalkan pembayaran kasir duplikat
        $component->call('voidDuplicatePayment', $payCash->id);

        // Verifikasi bahwa pembayaran kasir telah dihapus dan sisa pembayaran hanya transfer Rp 2.000
        $this->assertDatabaseMissing('bill_payments', ['id' => $payCash->id]);
        $this->assertDatabaseHas('bill_payments', ['id' => $payTransfer->id]);
        $this->assertEquals(2000.00, (float)$this->bill->fresh()->amount_paid);

        // Duplikat hilang dari scanner
        $this->assertCount(0, $component->get('settlementDuplicates'));
    }
}
