<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Modules\Keuangan\Models\PocketMoneyDeposit;
use App\Livewire\WaliPortal\DashboardTagihan;
use App\Livewire\Keuangan\BillingManager;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class PortalWaliV2Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Person $santri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name'  => 'Admin Bendahara',
        ]);

        $this->santri = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Ahmad Santri Teladan',
            'nis'    => '2026001',
            'gender' => 'L',
            'status' => 'aktif',
        ]);
    }

    public function test_wali_portal_renders_all_three_tabs_seamlessly(): void
    {
        $santri = $this->santri;
        $this->actingAs($this->admin);

        Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->assertSet('portalTab', 'tagihan')
            ->assertSee('Rincian Semua Pos Tagihan')
            ->call('setPortalTab', 'bayar')
            ->assertSet('portalTab', 'bayar')
            ->assertSee('Pilih Tagihan yang Ingin Dibayar')
            ->call('setPortalTab', 'riwayat')
            ->assertSet('portalTab', 'riwayat')
            ->assertSee('Riwayat Pembayaran');
    }

    public function test_wali_portal_manual_transfer_submission(): void
    {
        Storage::fake('public');

        $santri = $this->santri;
        $bill = Bill::create([
            'person_id'   => $santri->id,
            'bill_type'   => 'syahriah',
            'title'       => 'Syahriah Uji Coba',
            'amount'      => 100000,
            'amount_paid' => 0,
            'status'      => 'unpaid',
            'due_date'    => now()->addDays(7),
            'created_by'  => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        $fakeImage = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 800);

        Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->call('setPortalTab', 'bayar')
            ->set('selectedBillIds', [$bill->id])
            ->set('includePocketMoney', true)
            ->call('selectPresetPocketMoney', 50000)
            ->set('senderBank', 'BCA')
            ->set('senderAccountName', 'Bpk. Orang Tua')
            ->set('proofImage', $fakeImage)
            ->call('submitManualTransfer')
            ->assertHasNoErrors()
            ->assertSet('portalTab', 'riwayat');

        $this->assertDatabaseHas('manual_transfer_submissions', [
            'person_id'           => $santri->id,
            'sender_account_name' => 'Bpk. Orang Tua',
            'sender_bank'         => 'BCA',
            'pocket_money_amount' => 50000,
            'status'              => 'pending',
        ]);
    }

    public function test_cashier_billing_manager_approves_manual_transfer(): void
    {
        $admin = $this->admin;
        $this->actingAs($admin);

        $santri = $this->santri;
        $bill = Bill::create([
            'person_id'   => $santri->id,
            'bill_type'   => 'syahriah',
            'title'       => 'Syahriah Kasir Test',
            'amount'      => 120000,
            'amount_paid' => 0,
            'status'      => 'unpaid',
            'due_date'    => now()->addDays(7),
            'created_by'  => $this->admin->id,
        ]);

        $sub = ManualTransferSubmission::create([
            'submission_code'       => 'TRF-TEST-' . rand(1000, 9999),
            'person_id'             => $santri->id,
            'bill_ids'              => [$bill->id],
            'bill_breakdown'        => [
                [
                    'bill_id'      => $bill->id,
                    'config_label' => $bill->title ?? 'Tagihan Test',
                    'period_label' => 'Bulan Ini',
                    'amount'       => (float)$bill->amount,
                ]
            ],
            'total_bills_amount'    => (float)$bill->amount,
            'pocket_money_amount'   => 50000,
            'total_transfer_amount' => (float)$bill->amount + 50000,
            'bank_destination'      => 'BSI Pondok Pesantren',
            'sender_bank'           => 'BSI',
            'sender_account_name'   => 'Ibu Fatimah',
            'proof_image_path'      => 'manual_transfers/test_proof.webp',
            'status'                => 'pending',
        ]);

        PocketMoneyDeposit::create([
            'person_id'    => $santri->id,
            'amount'       => 50000,
            'source_type'  => 'manual_transfer',
            'reference_id' => $sub->id,
            'status'       => 'pending',
        ]);

        Livewire::test(BillingManager::class)
            ->set('activeTab', 'transfers')
            ->call('openTransferVerifyModal', $sub->id)
            ->assertSet('showTransferVerifyModal', true)
            ->call('approveTransferSubmission', $sub->id)
            ->assertSet('showTransferVerifyModal', false);

        $sub->refresh();
        $this->assertEquals('approved', $sub->status);
        $this->assertNotEmpty($sub->receipt_no);
        $this->assertEquals($admin->id, $sub->verified_by);

        // Check pocket money deposit status updated to received
        $this->assertDatabaseHas('pocket_money_deposits', [
            'reference_id' => $sub->id,
            'status'       => 'received',
        ]);
    }

    public function test_fifo_logic_and_quick_modes(): void
    {
        $santri = $this->santri;
        $this->actingAs($this->admin);

        // Past bill (tunggakan bulan lalu)
        $pastBill = Bill::create([
            'person_id'    => $santri->id,
            'bill_type'    => 'syahriah',
            'title'        => 'Syahriah Bulan Lalu',
            'amount'       => 150000,
            'amount_paid'  => 0,
            'status'       => 'unpaid',
            'period_month' => now()->subMonth()->month,
            'period_year'  => now()->subMonth()->year,
            'created_by'   => $this->admin->id,
        ]);

        // Current bill (bulan ini)
        $currentBill = Bill::create([
            'person_id'    => $santri->id,
            'bill_type'    => 'syahriah',
            'title'        => 'Syahriah Bulan Ini',
            'amount'       => 150000,
            'amount_paid'  => 0,
            'status'       => 'unpaid',
            'period_month' => now()->month,
            'period_year'  => now()->year,
            'created_by'   => $this->admin->id,
        ]);

        $test = Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->call('setPortalTab', 'bayar');

        // Test past_only quick mode
        $test->call('selectQuickMode', 'past_only')
            ->assertSet('selectedBillIds', [$pastBill->id]);

        // Test unselecting all
        $test->call('selectQuickMode', 'none')
            ->assertSet('selectedBillIds', []);

        // Test FIFO enforcement: selecting current bill when past bill is unselected automatically includes past bill
        $test->call('toggleBillSelection', $currentBill->id)
            ->assertSet('selectedBillIds', [$pastBill->id, $currentBill->id])
            ->assertSet('fifoNotice', 'Tagihan tunggakan bulan sebelumnya otomatis diikutsertakan agar urutan pelunasan tertib.');

        // Test FIFO enforcement: deselecting past bill automatically deselects current bill
        $test->call('toggleBillSelection', $pastBill->id)
            ->assertSet('selectedBillIds', [])
            ->assertSet('fifoNotice', 'Tagihan bulan berjalan/mendatang disesuaikan karena tunggakan lama belum dipilih.');
    }

    public function test_partial_custom_installment_and_submission(): void
    {
        Storage::fake('public');

        $santri = $this->santri;
        $this->actingAs($this->admin);

        $bill = Bill::create([
            'person_id'    => $santri->id,
            'bill_type'    => 'syahriah',
            'title'        => 'Syahriah Cicil Test',
            'amount'       => 200000,
            'amount_paid'  => 0,
            'status'       => 'unpaid',
            'period_month' => now()->month,
            'period_year'  => now()->year,
            'created_by'   => $this->admin->id,
        ]);

        $fakeImage = UploadedFile::fake()->image('bukti_transfer_cicil.jpg', 600, 800);

        $test = Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->call('setPortalTab', 'bayar')
            ->set('selectedBillIds', [$bill->id])
            // Set 50% installment
            ->call('setCustomAmountPercent', $bill->id, 50, 200000)
            ->set('proofImage', $fakeImage)
            ->call('submitManualTransfer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('manual_transfer_submissions', [
            'person_id'          => $santri->id,
            'total_bills_amount' => 100000,
            'status'             => 'pending',
        ]);
    }

    public function test_tab_3_riwayat_filters_manual_submissions_and_payment_breakdown(): void
    {
        $santri = $this->santri;
        $this->actingAs($this->admin);

        // 1. Create a manual transfer submission
        $sub = ManualTransferSubmission::create([
            'submission_code'       => 'TRF-TEST-2026',
            'person_id'             => $santri->id,
            'bill_ids'              => [],
            'bill_breakdown'        => [
                [
                    'config_label' => 'Syahriah Pesantren',
                    'period_label' => 'September 2026',
                    'amount'       => 150000,
                ]
            ],
            'total_bills_amount'    => 150000,
            'pocket_money_amount'   => 25000,
            'total_transfer_amount' => 175000,
            'bank_destination'      => 'BSI',
            'sender_bank'           => 'Mandiri',
            'sender_account_name'   => 'Bpk. Hendra',
            'proof_image_path'      => 'transfer-proofs/test.jpg',
            'status'                => 'pending',
        ]);

        // 2. Create an official cashier payment record
        $billPaid = Bill::create([
            'person_id'    => $santri->id,
            'bill_type'    => 'kitab',
            'title'        => 'Kitab Kuning Lengkap',
            'amount'       => 200000,
            'amount_paid'  => 200000,
            'status'       => 'paid',
            'period_year'  => 2026,
            'created_by'   => $this->admin->id,
        ]);

        BillPayment::create([
            'bill_id'        => $billPaid->id,
            'amount_paid'    => 200000,
            'payment_date'   => now()->toDateString(),
            'payment_method' => 'cash',
            'receipt_no'     => 'KSR-2026-0099',
            'logged_by'      => $this->admin->id,
        ]);

        $test = Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->call('setPortalTab', 'riwayat')
            ->assertSee('TRF-TEST-2026')
            ->assertSee('175.000')
            ->assertSee('Titipan Uang Saku')
            ->assertSee('KSR-2026-0099')
            ->assertSee('200.000');

        // Test filtering by method 'manual' (should show manual submissions, hide cashier)
        $test->set('historyMethod', 'manual')
            ->assertSee('TRF-TEST-2026')
            ->assertDontSee('KSR-2026-0099');

        // Test filtering by method 'kasir' (should hide manual submissions, show cashier)
        $test->set('historyMethod', 'kasir')
            ->assertDontSee('TRF-TEST-2026')
            ->assertSee('KSR-2026-0099');

        // Test filtering by current month
        $currentMonth = (string) now()->month;
        $test->set('historyMethod', '')
            ->set('historyMonth', $currentMonth)
            ->assertSee('TRF-TEST-2026')
            ->assertSee('KSR-2026-0099');

        // Test filtering by different month (e.g. month + 1 modulo 12)
        $diffMonth = (string) (now()->month == 12 ? 1 : now()->month + 1);
        $test->set('historyMonth', $diffMonth)
            ->assertDontSee('TRF-TEST-2026')
            ->assertDontSee('KSR-2026-0099');
    }

    public function test_switch_tab_and_retry_manual_submission(): void
    {
        $santri = $this->santri;
        $bill = Bill::create([
            'person_id'   => $santri->id,
            'bill_type'   => 'syahriah',
            'title'       => 'Syahriah Retried',
            'amount'      => 100000,
            'amount_paid' => 0,
            'status'      => 'unpaid',
            'created_by'  => $this->admin->id,
        ]);

        $sub = ManualTransferSubmission::create([
            'submission_code'       => 'TRF-RETRY-01',
            'person_id'             => $santri->id,
            'bill_ids'              => [$bill->id],
            'bill_breakdown'        => [
                ['bill_id' => $bill->id, 'bill_type' => 'syahriah', 'amount' => 100000, 'title' => 'Syahriah Retried'],
            ],
            'total_bills_amount'    => 100000,
            'pocket_money_amount'   => 25000,
            'total_transfer_amount' => 125000,
            'bank_destination'      => 'BSI',
            'sender_bank'           => 'BCA',
            'sender_account_name'   => 'Ibu Fatimah',
            'proof_image_path'      => 'transfer-proofs/dummy.jpg',
            'status'                => 'rejected',
            'rejection_reason'      => 'Bukti tidak jelas',
        ]);

        Livewire::test(DashboardTagihan::class, ['personId' => $santri->id])
            ->call('switchTab', 'riwayat')
            ->assertSet('portalTab', 'riwayat')
            ->call('retryManualSubmission', $sub->id)
            ->assertSet('portalTab', 'bayar')
            ->assertSet('checkoutMethod', 'manual')
            ->assertSet('selectedBillIds', [(string) $bill->id])
            ->assertSet('pocketMoneyAmount', 25000)
            ->assertSet('senderBank', 'BCA')
            ->assertSet('senderAccountName', 'Ibu Fatimah');
    }
}


