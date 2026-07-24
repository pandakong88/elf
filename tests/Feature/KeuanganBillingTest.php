<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Organization;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\MajekRegistration;
use App\Modules\Keuangan\Models\EventBill;
use App\Modules\Keuangan\Models\EventBillItem;
use App\Modules\Keuangan\Services\BillingService;
use App\Modules\Keuangan\Services\MajekService;
use App\Modules\Keuangan\Services\EventBillService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class KeuanganBillingTest extends TestCase
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

        // Admin user
        $this->admin = User::factory()->create();
    }

    public function test_monthly_billing_generation_for_mukim_santri(): void
    {
        $billingService = new BillingService();

        // 1. Create a Dormitory with Kas Komplek Rate
        $dormitory = Dormitory::create([
            'id' => Str::uuid()->toString(),
            'organization_id' => $this->pondok->id,
            'name' => 'Komplek A',
            'gender' => 'L',
            'kas_komplek_amount' => 10000.00,
            'is_active' => true,
        ]);

        $room = Room::create([
            'id' => Str::uuid()->toString(),
            'dormitory_id' => $dormitory->id,
            'name' => 'Kamar 1',
            'capacity' => 4,
            'is_active' => true,
        ]);

        // 2. Create Mukim Santri
        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'organization_id' => $this->pondok->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'mukim',
        ]);

        RoomAssignment::create([
            'id' => Str::uuid()->toString(),
            'room_id' => $room->id,
            'person_id' => $santri->id,
            'valid_from' => now()->subDays(5)->toDateString(),
            'is_active' => true,
        ]);

        // 3. Create Laju Santri (should NOT get monthly bills)
        $lajuSantri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Budi Santoso',
            'gender' => 'L',
        ]);

        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $lajuSantri->id,
            'organization_id' => $this->pondok->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
            'presence_status' => 'laju',
        ]);

        // 4. Generate Bills
        $result = $billingService->generateMonthlyBills(7, 2026, $this->admin->id);

        $this->assertEquals(2, $result['generated']); // 1 syahriah + 1 kas komplek

        // Assert bills created for Ahmad Fauzi (mukim)
        $this->assertTrue(Bill::where('person_id', $santri->id)->where('bill_type', 'syahriah_pondok')->exists());
        $this->assertTrue(Bill::where('person_id', $santri->id)->where('bill_type', 'kas_komplek')->exists());

        // Assert NO bills created for Budi Santoso (laju)
        $this->assertFalse(Bill::where('person_id', $lajuSantri->id)->exists());

        // Assert Bill amounts
        $syahriahBill = Bill::where('person_id', $santri->id)->where('bill_type', 'syahriah_pondok')->first();
        $this->assertEquals(35000.00, $syahriahBill->amount);

        $kasBill = Bill::where('person_id', $santri->id)->where('bill_type', 'kas_komplek')->first();
        $this->assertEquals(10000.00, $kasBill->amount);
    }

    public function test_partial_and_full_payment_flow(): void
    {
        $billingService = new BillingService();

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        $bill = Bill::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $santri->id,
            'bill_type' => 'syahriah_pondok',
            'period_month' => 7,
            'period_year' => 2026,
            'amount' => 35000.00,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'created_by' => $this->admin->id,
        ]);

        // Pay 15,000 first (Partial)
        $billingService->recordPayment($bill->id, 15000.00, 'CASH', 'Cicilan 1', $this->admin->id);

        $bill->refresh();
        $this->assertEquals(15000.00, $bill->amount_paid);
        $this->assertEquals('partial', $bill->status);

        // Pay remaining 20,000 (Paid/Lunas)
        $billingService->recordPayment($bill->id, 20000.00, 'CASH', 'Pelunasan', $this->admin->id);

        $bill->refresh();
        $this->assertEquals(35000.00, $bill->amount_paid);
        $this->assertEquals('paid', $bill->status);
    }

    public function test_majek_billing_and_prorata_holiday_adjustments(): void
    {
        $majekService = new MajekService();

        $santri = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Ahmad Fauzi',
            'gender' => 'L',
        ]);

        // Register to Majek Pagi
        $majekService->registerMajek($santri->id, 7, 2026, true, false, $this->admin->id);

        // Generate bills
        $result = $majekService->generateMajekBills(7, 2026, $this->admin->id);
        $this->assertEquals(1, $result['generated']);

        $bill = Bill::where('person_id', $santri->id)->where('bill_type', 'majek_pagi')->first();
        $this->assertNotNull($bill);
        $this->assertEquals(100000.00, $bill->amount);

        // Apply mass prorata discount due to holiday (e.g. 20,000 reduction)
        $majekService->massAdjustMajekBills(7, 2026, 'majek_pagi', 20000.00, 'Libur Akhir Tahun');

        $bill->refresh();
        $this->assertEquals(80000.00, $bill->amount);
        $this->assertStringContainsString('Libur Akhir Tahun', $bill->notes);
    }

    public function test_event_billing_and_sibling_discount(): void
    {
        $eventService = new EventBillService();

        // 1. Create two sibling santri
        $sibling1 = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Farhan Majid',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $sibling1->id,
            'organization_id' => $this->pondok->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
        ]);
        SantriProfile::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $sibling1->id,
            'has_active_sibling' => true,
            'active_sibling_count' => 1,
        ]);

        $nonSibling = Person::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Zainal Abidin',
            'gender' => 'L',
        ]);
        PersonRole::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $nonSibling->id,
            'organization_id' => $this->pondok->id,
            'role_type' => 'santri',
            'is_active' => true,
            'enrollment_status' => 'aktif',
        ]);
        SantriProfile::create([
            'id' => Str::uuid()->toString(),
            'person_id' => $nonSibling->id,
            'has_active_sibling' => false,
            'active_sibling_count' => 0,
        ]);

        // 2. Create Event Bill (default: 50,000)
        $event = $eventService->createEventBill('Ziarah Akbar', '2026-07-20', 50000.00, $this->admin->id);

        // 3. Assign
        $eventService->assignEventToAllActiveSantri($event->id, 10.00); // 10% discount for siblings

        // Assert Sibling item has discount
        $siblingItem = EventBillItem::where('event_bill_id', $event->id)->where('person_id', $sibling1->id)->first();
        $this->assertEquals(50000.00, $siblingItem->original_amount);
        $this->assertEquals(5000.00, $siblingItem->discount_amount);
        $this->assertEquals(45000.00, $siblingItem->final_amount);

        // Assert Non-Sibling item has NO discount
        $nonSiblingItem = EventBillItem::where('event_bill_id', $event->id)->where('person_id', $nonSibling->id)->first();
        $this->assertEquals(50000.00, $nonSiblingItem->original_amount);
        $this->assertEquals(0.00, $nonSiblingItem->discount_amount);
        $this->assertEquals(50000.00, $nonSiblingItem->final_amount);
    }
}
