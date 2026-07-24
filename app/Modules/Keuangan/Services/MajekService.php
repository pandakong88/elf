<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Keuangan\Models\MajekRegistration;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MajekService
{
    /**
     * Register or update majek status for a santri.
     */
    public function registerMajek(string $personId, int $month, int $year, bool $pagi, bool $sore, string $registeredByUserId): MajekRegistration
    {
        return MajekRegistration::updateOrCreate(
            [
                'person_id' => $personId,
                'month' => $month,
                'year' => $year,
            ],
            [
                'session_pagi' => $pagi,
                'session_sore' => $sore,
                'registered_by' => $registeredByUserId,
                'amount_pagi' => 100000.00,
                'amount_sore' => 100000.00,
            ]
        );
    }

    /**
     * Generate majek bills for all registered santri for the month.
     */
    public function generateMajekBills(int $month, int $year, string $createdByUserId): array
    {
        return DB::transaction(function () use ($month, $year, $createdByUserId) {
            $generatedCount = 0;

            // Load active billing configurations
            $pagiConfig = BillingConfiguration::where('type', 'majek_pagi')
                ->where('is_active', true)
                ->first();
            $defaultPagiAmount = $pagiConfig ? $pagiConfig->amount : 100000.00;

            $soreConfig = BillingConfiguration::where('type', 'majek_sore')
                ->where('is_active', true)
                ->first();
            $defaultSoreAmount = $soreConfig ? $soreConfig->amount : 100000.00;

            // Get registrations
            $registrations = MajekRegistration::where('month', $month)
                ->where('year', $year)
                ->get();

            foreach ($registrations as $reg) {
                // 1. Majek Pagi
                if ($reg->session_pagi) {
                    $existingPagi = Bill::where('person_id', $reg->person_id)
                        ->where('bill_type', 'majek_pagi')
                        ->where('period_month', $month)
                        ->where('period_year', $year)
                        ->exists();

                    if (!$existingPagi) {
                        Bill::create([
                            'id' => Str::uuid()->toString(),
                            'person_id' => $reg->person_id,
                            'bill_type' => 'majek_pagi',
                            'billing_config_id' => $pagiConfig?->id,
                            'period_month' => $month,
                            'period_year' => $year,
                            'amount' => $defaultPagiAmount,
                            'amount_paid' => 0.00,
                            'status' => 'unpaid',
                            'due_date' => now()->setDate($year, $month, 10)->toDateString(),
                            'created_by' => $createdByUserId,
                        ]);
                        $generatedCount++;
                    }
                }

                // 2. Majek Sore
                if ($reg->session_sore) {
                    $existingSore = Bill::where('person_id', $reg->person_id)
                        ->where('bill_type', 'majek_sore')
                        ->where('period_month', $month)
                        ->where('period_year', $year)
                        ->exists();

                    if (!$existingSore) {
                        Bill::create([
                            'id' => Str::uuid()->toString(),
                            'person_id' => $reg->person_id,
                            'bill_type' => 'majek_sore',
                            'billing_config_id' => $soreConfig?->id,
                            'period_month' => $month,
                            'period_year' => $year,
                            'amount' => $defaultSoreAmount,
                            'amount_paid' => 0.00,
                            'status' => 'unpaid',
                            'due_date' => now()->setDate($year, $month, 10)->toDateString(),
                            'created_by' => $createdByUserId,
                        ]);
                        $generatedCount++;
                    }
                }
            }

            return ['generated' => $generatedCount];
        });
    }

    /**
     * Adjust/reduce a single majek bill's amount.
     */
    public function adjustBillAmount(string $billId, float $reductionAmount, string $reason): Bill
    {
        return DB::transaction(function () use ($billId, $reductionAmount, $reason) {
            $bill = Bill::findOrFail($billId);

            // Deduct from bill amount
            $bill->amount = max(0.00, $bill->amount - $reductionAmount);
            $bill->notes = trim(($bill->notes ?? '') . " [Penyesuaian Libur: Potongan Rp " . number_format($reductionAmount, 0, ',', '.') . " - Alasan: {$reason}]");
            $bill->save();

            $bill->recalculateStatus();

            return $bill;
        });
    }

    /**
     * Mass adjust majek bills for the month.
     */
    public function massAdjustMajekBills(int $month, int $year, string $type, float $reductionAmount, string $reason): int
    {
        return DB::transaction(function () use ($month, $year, $type, $reductionAmount, $reason) {
            $bills = Bill::where('bill_type', $type)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->get();

            $count = 0;
            foreach ($bills as $bill) {
                $this->adjustBillAmount($bill->id, $reductionAmount, $reason);
                $count++;
            }

            return $count;
        });
    }
}
