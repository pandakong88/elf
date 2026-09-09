<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Models\PocketMoneyDeposit;
use App\Modules\Keuangan\Services\DokuService;
use App\Livewire\WaliPortal\DashboardTagihan;
use App\Livewire\System\DeveloperSettings;
use Livewire\Livewire;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DokuIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Person $santri;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'name'  => 'Admin Super',
        ]);
        $this->admin->assignRole('super-admin');

        $this->santri = Person::create([
            'id'     => (string) Str::uuid(),
            'name'   => 'Muhammad Farhan',
            'nis'    => '2026101',
            'gender' => 'L',
            'status' => 'aktif',
        ]);

        // Setup DOKU default config in DB
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_enabled'],
            ['value' => '1', 'type' => 'text', 'section' => 'payment_gateway', 'title' => 'Doku Enabled']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_environment'],
            ['value' => 'sandbox', 'type' => 'text', 'section' => 'payment_gateway', 'title' => 'Doku Environment']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_client_id'],
            ['value' => 'MCH-TEST-12345', 'type' => 'text', 'section' => 'payment_gateway', 'title' => 'Doku Client ID']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_secret_key'],
            ['value' => 'SK-TEST-SECRET-XYZ-987', 'type' => 'text', 'section' => 'payment_gateway', 'title' => 'Doku Secret Key']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_expiry_minutes'],
            ['value' => '1440', 'type' => 'text', 'section' => 'payment_gateway', 'title' => 'Doku Expiry']
        );
    }

    public function test_doku_signature_generation_and_verification(): void
    {
        $dokuService = app(DokuService::class);

        $targetPath = '/checkout/v1/payment';
        $payload = json_encode(['order' => ['invoice_number' => 'INV-TEST-001', 'amount' => 500000]]);
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $headers = $dokuService->generateHeaders($targetPath, $payload, $requestId, $timestamp);

        $this->assertArrayHasKey('Client-Id', $headers);
        $this->assertArrayHasKey('Request-Id', $headers);
        $this->assertArrayHasKey('Request-Timestamp', $headers);
        $this->assertArrayHasKey('Signature', $headers);
        $this->assertEquals('MCH-TEST-12345', $headers['Client-Id']);

        // Verify valid signature
        $isValid = $dokuService->verifyNotificationSignature(
            clientId: 'MCH-TEST-12345',
            requestId: $requestId,
            timestamp: $timestamp,
            targetPath: $targetPath,
            rawBody: $payload,
            receivedSignature: $headers['Signature']
        );
        $this->assertTrue($isValid);

        // Verify tampered payload fails
        $tamperedBody = json_encode(['order' => ['invoice_number' => 'INV-TEST-001', 'amount' => 1000000]]);
        $isTamperedValid = $dokuService->verifyNotificationSignature(
            clientId: 'MCH-TEST-12345',
            requestId: $requestId,
            timestamp: $timestamp,
            targetPath: $targetPath,
            rawBody: $tamperedBody,
            receivedSignature: $headers['Signature']
        );
        $this->assertFalse($isTamperedValid);
    }

    public function test_doku_create_checkout_session(): void
    {
        Http::fake([
            'https://api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'response' => [
                    'payment' => [
                        'url' => 'https://sandbox.doku.com/checkout/pay/TOKEN12345',
                        'token' => 'TOKEN12345',
                    ],
                ],
            ], 200),
        ]);

        $bill = Bill::create([
            'person_id'      => $this->santri->id,
            'bill_type'      => 'syahriah_pondok',
            'title'          => 'SPP September 2026',
            'amount'         => 350000,
            'amount_paid'    => 0,
            'status'         => 'unpaid',
            'period_month'   => 9,
            'period_year'    => 2026,
            'created_by'     => $this->admin->id,
        ]);

        $dokuService = app(DokuService::class);
        $transaction = $dokuService->createCheckoutSession(
            bills: [$bill],
            personId: $this->santri->id,
            pocketMoney: 100000,
            userId: $this->admin->id,
            category: 'va'
        );

        $this->assertInstanceOf(PaymentTransaction::class, $transaction);
        $this->assertEquals('doku', $transaction->gateway_provider);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals(350000, $transaction->bill_amount);
        $this->assertEquals(3500, $transaction->mdr_amount);
        $this->assertEquals(100000, $transaction->pocket_money_amount);
        $this->assertEquals(453500, $transaction->total_amount);
        $this->assertEquals(450000, $transaction->net_amount);
        $this->assertEquals('https://sandbox.doku.com/checkout/pay/TOKEN12345', $transaction->payment_url);

        $this->assertDatabaseHas('payment_transactions', [
            'id'                  => $transaction->id,
            'merchant_order_id'   => $transaction->merchant_order_id,
            'gateway_provider'    => 'doku',
            'total_amount'        => 453500,
            'mdr_amount'          => 3500,
            'pocket_money_amount' => 100000,
            'status'              => 'pending',
        ]);
    }

    public function test_doku_webhook_notification_settles_bills_and_pocket_money(): void
    {
        $bill1 = Bill::create([
            'person_id'      => $this->santri->id,
            'bill_type'      => 'syahriah_pondok',
            'title'          => 'SPP September 2026',
            'amount'         => 350000,
            'amount_paid'    => 0,
            'status'         => 'unpaid',
            'period_month'   => 9,
            'period_year'    => 2026,
            'created_by'     => $this->admin->id,
        ]);

        $bill2 = Bill::create([
            'person_id'      => $this->santri->id,
            'bill_type'      => 'kas_komplek',
            'title'          => 'Kas Komplek September 2026',
            'amount'         => 50000,
            'amount_paid'    => 0,
            'status'         => 'unpaid',
            'period_month'   => 9,
            'period_year'    => 2026,
            'created_by'     => $this->admin->id,
        ]);

        $invoiceNumber = 'DOKU-20260909000001-ABCD';
        $transaction = PaymentTransaction::create([
            'id'                   => (string) Str::uuid(),
            'merchant_order_id'    => $invoiceNumber,
            'person_id'            => $this->santri->id,
            'payment_channel'      => 'DOKU_CHECKOUT',
            'channel_label'        => 'DOKU Checkout (Hosted Page)',
            'bill_amount'          => 400000,
            'mdr_amount'           => 0,
            'total_amount'         => 500000,
            'net_amount'           => 500000,
            'bill_ids'             => [$bill1->id, $bill2->id],
            'bill_breakdown'       => [
                ['bill_id' => $bill1->id, 'config_label' => 'SPP September', 'period_label' => 'Sep 2026', 'amount' => 350000, 'bill_type' => 'syahriah_pondok'],
                ['bill_id' => $bill2->id, 'config_label' => 'Kas Komplek', 'period_label' => 'Sep 2026', 'amount' => 50000, 'bill_type' => 'kas_komplek'],
            ],
            'pocket_money_amount'  => 100000,
            'payment_url'          => 'https://sandbox.doku.com/checkout/pay/DOKU-TEST',
            'gateway_provider'     => 'doku',
            'status'               => 'pending',
            'expires_at'           => now()->addDay(),
        ]);

        $dokuService = app(DokuService::class);
        $targetPath = '/payment/doku/notification';
        $notificationPayload = [
            'order' => [
                'invoice_number' => $invoiceNumber,
                'amount'         => 500000,
            ],
            'transaction' => [
                'status' => 'SUCCESS',
                'date'   => date('Y-m-d H:i:s'),
            ],
            'channel' => [
                'id' => 'VIRTUAL_ACCOUNT_BCA',
            ],
        ];
        $rawJson = json_encode($notificationPayload);
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $headers = $dokuService->generateHeaders($targetPath, $rawJson, $requestId, $timestamp);

        $response = $this->withHeaders([
            'Client-Id'         => $headers['Client-Id'],
            'Request-Id'        => $headers['Request-Id'],
            'Request-Timestamp' => $headers['Request-Timestamp'],
            'Signature'         => $headers['Signature'],
            'Content-Type'      => 'application/json',
        ])->postJson('/payment/doku/notification', $notificationPayload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        // Check transaction updated
        $transaction->refresh();
        $this->assertEquals('success', $transaction->status);
        $this->assertEquals('VIRTUAL_ACCOUNT_BCA', $transaction->payment_channel);
        $this->assertNotNull($transaction->callback_received_at);

        // Check bills settled
        $bill1->refresh();
        $bill2->refresh();
        $this->assertEquals('paid', $bill1->status);
        $this->assertEquals(350000, $bill1->amount_paid);
        $this->assertEquals('paid', $bill2->status);
        $this->assertEquals(50000, $bill2->amount_paid);

        // Check bill payments recorded
        $this->assertDatabaseHas('bill_payments', [
            'bill_id'        => $bill1->id,
            'amount_paid'    => 350000,
            'payment_method' => 'gateway_duitku',
        ]);

        // Check pocket money deposit created
        $this->assertDatabaseHas('pocket_money_deposits', [
            'person_id' => $this->santri->id,
            'amount'    => 100000,
            'status'    => 'received',
        ]);
    }

    public function test_doku_webhook_notification_rejects_invalid_signature(): void
    {
        $response = $this->withHeaders([
            'Client-Id'         => 'MCH-TEST-12345',
            'Request-Id'        => (string) Str::uuid(),
            'Request-Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Signature'         => 'HMACSHA256=INVALID_SIGNATURE_HERE',
            'Content-Type'      => 'application/json',
        ])->postJson('/payment/doku/notification', [
            'order' => [
                'invoice_number' => 'NON-EXISTENT',
                'amount'         => 100000,
            ],
            'transaction' => [
                'status' => 'SUCCESS',
            ],
        ]);

        $response->assertStatus(401);
        $response->assertJson(['status' => 'FAILED', 'message' => 'Invalid signature']);
    }

    public function test_doku_webhook_is_idempotent(): void
    {
        $bill = Bill::create([
            'person_id'      => $this->santri->id,
            'bill_type'      => 'syahriah_pondok',
            'title'          => 'SPP September 2026',
            'amount'         => 300000,
            'amount_paid'    => 0,
            'status'         => 'unpaid',
            'period_month'   => 9,
            'period_year'    => 2026,
            'created_by'     => $this->admin->id,
        ]);

        $invoiceNumber = 'DOKU-IDEMPOTENT-001';
        $transaction = PaymentTransaction::create([
            'id'                   => (string) Str::uuid(),
            'merchant_order_id'    => $invoiceNumber,
            'person_id'            => $this->santri->id,
            'payment_channel'      => 'DOKU_CHECKOUT',
            'bill_amount'          => 300000,
            'total_amount'         => 300000,
            'net_amount'           => 300000,
            'bill_ids'             => [$bill->id],
            'bill_breakdown'       => [
                ['bill_id' => $bill->id, 'config_label' => 'SPP', 'period_label' => 'Sep 2026', 'amount' => 300000, 'bill_type' => 'syahriah_pondok'],
            ],
            'gateway_provider'     => 'doku',
            'status'               => 'pending',
            'expires_at'           => now()->addDay(),
        ]);

        $dokuService = app(DokuService::class);
        $targetPath = '/payment/doku/notification';
        $payload = [
            'order' => ['invoice_number' => $invoiceNumber, 'amount' => 300000],
            'transaction' => ['status' => 'SUCCESS', 'date' => date('Y-m-d H:i:s')],
            'channel' => ['id' => 'QRIS'],
        ];
        $rawJson = json_encode($payload);
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $headers = $dokuService->generateHeaders($targetPath, $rawJson, $requestId, $timestamp);

        // 1st request
        $res1 = $this->withHeaders($headers)->postJson('/payment/doku/notification', $payload);
        $res1->assertStatus(200);

        $this->assertEquals(1, BillPayment::where('bill_id', $bill->id)->count());

        // 2nd request (duplicate webhook delivery)
        $res2 = $this->withHeaders($headers)->postJson('/payment/doku/notification', $payload);
        $res2->assertStatus(200);
        $res2->assertJson(['status' => 'SUCCESS', 'message' => 'Transaction already processed']);

        // Assert payment is not duplicated
        $this->assertEquals(1, BillPayment::where('bill_id', $bill->id)->count());
    }

    public function test_developer_settings_doku_management(): void
    {
        $this->actingAs($this->admin);

        Http::fake([
            'https://api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'response' => [
                    'payment' => [
                        'url' => 'https://sandbox.doku.com/checkout/pay/TEST-TOKEN',
                    ],
                ],
            ], 200),
        ]);

        Livewire::test(DeveloperSettings::class)
            ->set('doku_enabled', true)
            ->set('doku_environment', 'sandbox')
            ->set('doku_client_id', 'MCH-NEW-999')
            ->set('doku_secret_key', 'SK-NEW-SECRET-999')
            ->set('doku_expiry_minutes', 120)
            ->call('saveSettings')
            ->assertSee('Seluruh pengaturan Developer & Payment Gateway DOKU berhasil disimpan')
            ->call('testDokuConnection')
            ->assertSet('dokuTestResult.success', true);

        $this->assertDatabaseHas('landing_page_contents', ['key' => 'doku_client_id', 'value' => 'MCH-NEW-999']);
        $this->assertDatabaseHas('landing_page_contents', ['key' => 'doku_secret_key', 'value' => 'SK-NEW-SECRET-999']);
        $this->assertDatabaseHas('landing_page_contents', ['key' => 'doku_expiry_minutes', 'value' => '120']);
    }

    public function test_wali_portal_pay_via_doku_redirection(): void
    {
        $this->actingAs($this->admin);

        Http::fake([
            'https://api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'response' => [
                    'payment' => [
                        'url' => 'https://sandbox.doku.com/checkout/pay/DOKU-PORTAL-TEST',
                    ],
                ],
            ], 200),
        ]);

        $bill = Bill::create([
            'person_id'      => $this->santri->id,
            'bill_type'      => 'syahriah_pondok',
            'title'          => 'SPP September 2026',
            'amount'         => 350000,
            'amount_paid'    => 0,
            'status'         => 'unpaid',
            'period_month'   => 9,
            'period_year'    => 2026,
            'created_by'     => $this->admin->id,
        ]);

        Livewire::test(DashboardTagihan::class, ['personId' => $this->santri->id])
            ->call('setPortalTab', 'bayar')
            ->assertSet('checkoutMethod', 'doku')
            ->set('selectedBillIds', [$bill->id])
            ->call('payViaDoku')
            ->assertRedirect('https://sandbox.doku.com/checkout/pay/DOKU-PORTAL-TEST');
    }
}