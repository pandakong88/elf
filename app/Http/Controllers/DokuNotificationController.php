<?php

namespace App\Http\Controllers;

use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Models\PocketMoneyDeposit;
use App\Modules\Keuangan\Services\DokuService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuNotificationController extends Controller
{
    protected DokuService $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * Handle Webhook HTTP Notification dari DOKU Payment Gateway.
     */
    public function handle(Request $request): JsonResponse
    {
        $clientId   = $request->header('Client-Id', '');
        $requestId  = $request->header('Request-Id', '');
        $timestamp  = $request->header('Request-Timestamp', '');
        $signature  = $request->header('Signature', '');
        $targetPath = $request->getRequestUri();
        // Bersihkan query string jika ada untuk target path
        if (str_contains($targetPath, '?')) {
            $targetPath = explode('?', $targetPath)[0];
        }

        $rawBody = $request->getContent();
        $payload = $request->json()->all();

        Log::info('[DokuNotification] Received Webhook Payload', [
            'headers' => [
                'Client-Id'         => $clientId,
                'Request-Id'        => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature'         => $signature,
            ],
            'payload' => $payload,
        ]);

        // 1. Verifikasi Signature DOKU
        $isValidSignature = $this->dokuService->verifyNotificationSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $signature
        );

        if (!$isValidSignature) {
            Log::warning('[DokuNotification] Invalid Signature received from DOKU', [
                'client_id' => $clientId,
                'signature' => $signature,
            ]);
            return response()->json([
                'status'  => 'FAILED',
                'message' => 'Invalid signature',
                'error'   => [
                    'code'    => 'INVALID_SIGNATURE',
                    'message' => 'Signature verification failed.',
                ]
            ], 401);
        }

        // 2. Ekstrak Invoice Number & Status Transaksi
        $invoiceNumber = $payload['order']['invoice_number'] 
                      ?? $payload['order']['id'] 
                      ?? $payload['invoice_number'] 
                      ?? null;

        if (!$invoiceNumber) {
            return response()->json([
                'error' => [
                    'code'    => 'INVALID_PAYLOAD',
                    'message' => 'Invoice number not found in payload.',
                ]
            ], 400);
        }

        $trx = PaymentTransaction::where('merchant_order_id', $invoiceNumber)
            ->orWhere('id', $invoiceNumber)
            ->first();

        if (!$trx) {
            Log::warning('[DokuNotification] Transaction not found for invoice: ' . $invoiceNumber);
            return response()->json([
                'error' => [
                    'code'    => 'NOT_FOUND',
                    'message' => 'Transaction not found.',
                ]
            ], 404);
        }

        // 3. Cek Idempotensi (Jika sudah sukses/paid, langsung return 200)
        if (in_array($trx->status, ['paid', 'success'])) {
            Log::info('[DokuNotification] Transaction already marked as paid/success: ' . $invoiceNumber);
            return response()->json([
                'status'  => 'SUCCESS',
                'message' => 'Transaction already processed',
            ]);
        }

        // Status dari DOKU: SUCCESS / FAILED / EXPIRED
        $trxStatus = strtoupper(
            $payload['transaction']['status'] 
            ?? $payload['status'] 
            ?? 'SUCCESS'
        );

        $channelId = $payload['channel']['id'] 
                  ?? $payload['payment']['channel'] 
                  ?? $trx->payment_channel;

        if ($trxStatus === 'SUCCESS') {
            try {
                DB::beginTransaction();

                $now = now();
                $receiptNo = BillPayment::generateReceiptNo();
                $paymentGroupId = (string) Str::uuid();

                // 3a. Update PaymentTransaction
                $trx->update([
                    'status'                => 'success',
                    'payment_channel'       => $channelId,
                    'channel_label'         => str_replace('_', ' ', $channelId),
                    'callback_received_at'  => $now,
                    'raw_callback_payload'  => $payload,
                ]);

                // Tentukan user sistem pencatat (bendahara/super-admin/creator)
                $loggedBy = $trx->user_id;
                if (!$loggedBy || !\App\Models\User::where('id', $loggedBy)->exists()) {
                    $loggedBy = \App\Models\User::whereHas('roles', function($q) {
                        $q->whereIn('name', ['super-admin', 'admin', 'bendahara-putra', 'bendahara-putri']);
                    })->value('id') ?? \App\Models\User::value('id');
                }

                // 3b. Proses Pelunasan Tagihan (Bills)
                $breakdown = $trx->bill_breakdown ?? [];

                if (empty($breakdown) && !empty($trx->bill_ids)) {
                    // Fallback jika breakdown belum terformat
                    foreach ($trx->bill_ids as $bId) {
                        $b = Bill::find($bId);
                        if ($b) {
                            $breakdown[] = [
                                'bill_id'      => $b->id,
                                'config_label' => $b->title ?: 'Tagihan',
                                'amount'       => (float) ($b->remaining_amount ?? $b->amount),
                            ];
                        }
                    }
                }

                foreach ($breakdown as $item) {
                    if (empty($item['bill_id'])) continue;
                    $bill = Bill::find($item['bill_id']);
                    if (!$bill) continue;

                    $amountPaid = (float) ($item['amount'] ?? $bill->remaining_amount ?? 0);
                    if ($amountPaid <= 0) continue;

                    BillPayment::create([
                        'bill_id'          => $bill->id,
                        'receipt_no'       => $receiptNo,
                        'payment_group_id' => $paymentGroupId,
                        'amount_paid'      => $amountPaid,
                        'payment_method'   => 'gateway_duitku', // Diselaraskan dengan enum kasir online
                        'payment_date'     => $now,
                        'logged_by'        => $loggedBy,
                        'notes'            => 'Pembayaran DOKU Gateway [' . $invoiceNumber . ' · ' . $channelId . ']',
                    ]);

                    $bill->recalculateStatus();
                }

                // 3c. Proses Titipan Uang Saku Santri (jika ada)
                if ($trx->pocket_money_amount > 0) {
                    PocketMoneyDeposit::create([
                        'person_id'    => $trx->person_id,
                        'amount'       => $trx->pocket_money_amount,
                        'source'       => 'gateway_duitku',
                        'reference_id' => $trx->id,
                        'status'       => 'received',
                        'notes'        => 'Titipan uang saku via DOKU [' . $invoiceNumber . ']',
                        'received_at'  => $now,
                        'received_by'  => $loggedBy,
                    ]);
                }

                DB::commit();

                Log::info('[DokuNotification] Successfully processed payment for ' . $invoiceNumber . ', Receipt: ' . $receiptNo);

                // 3d. Kirim Notifikasi WhatsApp (jika service aktif)
                try {
                    if (class_exists(WhatsAppService::class) && $trx->person) {
                        $wa = app(WhatsAppService::class);
                        if (method_exists($wa, 'sendPaymentSuccessReceipt')) {
                            $wa->sendPaymentSuccessReceipt($trx, $receiptNo);
                        }
                    }
                } catch (\Throwable $waErr) {
                    Log::warning('[DokuNotification] WA notification failed: ' . $waErr->getMessage());
                }

                return response()->json([
                    'status'     => 'OK',
                    'receipt_no' => $receiptNo,
                    'message'    => 'Payment successfully verified and settled.',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('[DokuNotification] Error processing payment: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json([
                    'error' => [
                        'code'    => 'PROCESSING_ERROR',
                        'message' => 'Failed to process transaction: ' . $e->getMessage(),
                    ]
                ], 500);
            }
        } elseif (in_array($trxStatus, ['FAILED', 'CANCELLED', 'EXPIRED'])) {
            $mappedStatus = $trxStatus === 'EXPIRED' ? 'expired' : 'failed';
            $trx->update([
                'status'               => $mappedStatus,
                'callback_received_at' => now(),
                'raw_callback_payload' => $payload,
                'failure_reason'       => $payload['transaction']['status_message'] ?? 'Pembayaran gagal atau kadaluarsa di DOKU.',
            ]);

            return response()->json([
                'status'  => 'OK',
                'message' => 'Transaction marked as ' . $mappedStatus,
            ]);
        }

        return response()->json(['status' => 'OK']);
    }
}
