<?php

namespace App\Http\Controllers;

use App\Modules\Keuangan\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DuitkuCallbackController extends Controller
{
    public function __construct(
        private readonly DuitkuService $duitkuService
    ) {}

    /**
     * Menerima callback/webhook dari Duitku setelah pembayaran diproses.
     *
     * Endpoint ini:
     * - Bersifat publik (tidak perlu login)
     * - Dikecualikan dari CSRF verification
     * - Selalu return HTTP 200 (standar Duitku)
     * - Idempotent: aman dipanggil berkali-kali
     *
     * Duitku akan mengirim POST request dengan payload:
     *   merchantCode, amount, merchantOrderId, productDetail,
     *   additionalParam, paymentMethod, resultCode,
     *   merchantUserId, reference, signature, publisherOrderId
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        Log::info('[Duitku] Callback received', [
            'ip'      => $request->ip(),
            'payload' => $payload,
        ]);

        try {
            // 1. Verifikasi signature — tolak jika tidak valid
            if (!$this->duitkuService->verifyCallback($payload)) {
                Log::warning('[Duitku] Callback rejected: invalid signature', [
                    'ip'       => $request->ip(),
                    'order_id' => $payload['merchantOrderId'] ?? 'unknown',
                ]);

                // Tetap return 200 agar Duitku tidak retry terus-menerus
                // (retry hanya berguna jika server error, bukan jika kita yang reject)
                return response('INVALID_SIGNATURE', 200);
            }

            // 2. Proses callback (update transaksi + buat BillPayment)
            $this->duitkuService->processCallback($payload);

            return response('SUCCESS', 200);

        } catch (\Exception $e) {
            Log::error('[Duitku] Callback processing error', [
                'order_id' => $payload['merchantOrderId'] ?? 'unknown',
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            // Return 200 tetap agar Duitku tidak spam retry
            // Error sudah di-log untuk investigasi manual
            return response('ERROR_LOGGED', 200);
        }
    }
}
