<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Services\DuitkuService;
use Illuminate\Support\Facades\Log;

class SyncPendingGatewayTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keuangan:sync-gateway-pending {--limit=50 : Batas jumlah transaksi yang diperiksa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan status transaksi Duitku yang masih pending ke server Duitku secara otomatis';

    /**
     * Execute the console command.
     */
    public function handle(DuitkuService $duitkuService): int
    {
        $limit = (int) $this->option('limit');

        $this->info("Memulai pengecekan transaksi gateway pending (maks {$limit} transaksi)...");

        // Ambil transaksi pending yang belum kadaluarsa lebih dari 3 hari
        $pendingTransactions = PaymentTransaction::where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(3))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('Tidak ada transaksi pending untuk disinkronkan.');
            return Command::SUCCESS;
        }

        $successCount = 0;
        $failedCount  = 0;
        $stillPending = 0;

        foreach ($pendingTransactions as $trx) {
            try {
                $res = $duitkuService->checkTransactionStatus($trx->merchant_order_id);
                $statusCode    = (string) ($res['statusCode'] ?? ($res['status_code'] ?? ''));
                $statusMessage = (string) ($res['statusMessage'] ?? ($res['status_message'] ?? ''));

                if ($statusCode === '00') {
                    // ✅ LUNAS (Sukses)
                    $trx->update([
                        'status'               => 'success',
                        'callback_received_at' => $trx->callback_received_at ?: now(),
                        'duitku_reference'     => $res['reference'] ?? $trx->duitku_reference,
                    ]);
                    $duitkuService->handleSuccessfulPayment($trx, $res);
                    $successCount++;
                    $this->line("<info>[SUCCESS]</info> {$trx->merchant_order_id} ({$trx->person?->name}) telah LUNAS.");
                } elseif ($statusCode === '02' || ($trx->expires_at && $trx->expires_at->isPast())) {
                    // ❌ GAGAL / EXPIRED
                    $trx->update([
                        'status'         => 'failed',
                        'failure_reason' => $statusMessage ?: 'Expired / Canceled',
                    ]);
                    $failedCount++;
                    $this->line("<comment>[EXPIRED/FAILED]</comment> {$trx->merchant_order_id} kadaluarsa.");
                } else {
                    $stillPending++;
                    $this->line("<fg=gray>[PENDING]</> {$trx->merchant_order_id} masih menunggu pembayaran.");
                }
            } catch (\Throwable $e) {
                Log::error("[DuitkuSyncCommand] Error syncing {$trx->merchant_order_id}: " . $e->getMessage());
                $this->error("Error pada {$trx->merchant_order_id}: " . $e->getMessage());
            }
        }

        $this->info("Sinkronisasi selesai! Sukses: {$successCount}, Gagal/Expired: {$failedCount}, Tetap Pending: {$stillPending}");

        return Command::SUCCESS;
    }
}
