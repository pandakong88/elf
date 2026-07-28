<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\BillingException;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\Dormitory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Generate monthly bills for all active mukim santri.
     * Includes 'syahriah_pondok' and 'kas_komplek'.
     */
    public function generateMonthlyBills(int $month, int $year, string $createdByUserId): array
    {
        return DB::transaction(function () use ($month, $year, $createdByUserId) {
            $generatedCount = 0;
            $skippedCount = 0;

            // Get active mukim santri
            $santriList = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')
                  ->where('enrollment_status', 'aktif')
                  ->where('presence_status', 'mukim');
            })->get();

            // Load active billing configurations
            $syahriahConfig = BillingConfiguration::where('type', 'syahriah_pondok')
                ->where('is_active', true)
                ->first();
            $defaultSyahriahAmount = $syahriahConfig ? $syahriahConfig->amount : 35000.00;

            foreach ($santriList as $santri) {
                // 1. Generate Syahriah Pondok Bill
                $existingSyahriah = Bill::where('person_id', $santri->id)
                    ->where('bill_type', 'syahriah_pondok')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->exists();

                if (!$existingSyahriah) {
                    $amount = $syahriahConfig ? $this->calculateFinalAmount($syahriahConfig, $santri->id, $defaultSyahriahAmount) : $defaultSyahriahAmount;

                    Bill::create([
                        'id' => Str::uuid()->toString(),
                        'person_id' => $santri->id,
                        'bill_type' => 'syahriah_pondok',
                        'billing_config_id' => $syahriahConfig?->id,
                        'period_month' => $month,
                        'period_year' => $year,
                        'amount' => $amount,
                        'amount_paid' => 0.00,
                        'status' => $amount == 0.00 ? 'paid' : 'unpaid',
                        'due_date' => now()->setDate($year, $month, 10)->toDateString(), // Due tgl 10
                        'created_by' => $createdByUserId,
                    ]);
                    $generatedCount++;
                } else {
                    $skippedCount++;
                }

                // 2. Generate Kas Komplek Bill
                $existingKas = Bill::where('person_id', $santri->id)
                    ->where('bill_type', 'kas_komplek')
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->exists();

                if (!$existingKas) {
                    // Find active room assignment and dormitory
                    $assignment = RoomAssignment::active()->where('person_id', $santri->id)->first();
                    $dormitory = $assignment?->room?->dormitory;

                    if ($dormitory) {
                        // Check if specific config exists for this dormitory
                        $kasConfig = BillingConfiguration::where('type', 'kas_komplek')
                            ->where('dormitory_id', $dormitory->id)
                            ->where('is_active', true)
                            ->first();

                        $defaultKasAmount = $kasConfig ? $kasConfig->amount : $dormitory->kas_komplek_amount;
                        $amount = $kasConfig ? $this->calculateFinalAmount($kasConfig, $santri->id, $defaultKasAmount) : $defaultKasAmount;

                        Bill::create([
                            'id' => Str::uuid()->toString(),
                            'person_id' => $santri->id,
                            'bill_type' => 'kas_komplek',
                            'billing_config_id' => $kasConfig?->id,
                            'period_month' => $month,
                            'period_year' => $year,
                            'amount' => $amount,
                            'amount_paid' => 0.00,
                            'status' => $amount == 0.00 ? 'paid' : 'unpaid',
                            'due_date' => now()->setDate($year, $month, 10)->toDateString(),
                            'created_by' => $createdByUserId,
                        ]);
                        $generatedCount++;
                    }
                } else {
                    $skippedCount++;
                }
            }

            return [
                'generated' => $generatedCount,
                'skipped' => $skippedCount,
            ];
        });
    }

    /**
     * Generate semester bills.
     * Includes 'syahriah_madrasah' (150k for both Mukim & Laju)
     * and 'kebersihan' (20k for Mukim only).
     */
    public function generateSemesterBills(int $periodYear, int $semesterNumber, string $createdByUserId): array
    {
        return DB::transaction(function () use ($periodYear, $semesterNumber, $createdByUserId) {
            $generatedCount = 0;

            // Load active billing configurations
            $madrasahConfig = BillingConfiguration::where('type', 'syahriah_madrasah')
                ->where('is_active', true)
                ->first();
            $defaultMadrasahAmount = $madrasahConfig ? $madrasahConfig->amount : 150000.00;

            $kebersihanConfig = BillingConfiguration::where('type', 'kebersihan')
                ->where('is_active', true)
                ->first();
            $defaultKebersihanAmount = $kebersihanConfig ? $kebersihanConfig->amount : 20000.00;

            // Get all active santri (Mukim & Laju)
            $santriList = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')
                  ->where('enrollment_status', 'aktif');
            })->with('activeRoles')->get();

            foreach ($santriList as $santri) {
                $role = $santri->activeRoles->firstWhere('role_type', 'santri');
                $isMukim = $role && $role->presence_status === 'mukim';

                // 1. Generate Madrasah Bill (for both)
                $existingMadrasah = Bill::where('person_id', $santri->id)
                    ->where('bill_type', 'syahriah_madrasah')
                    ->where('period_month', $semesterNumber) // Store semester number (1 or 2) in period_month
                    ->where('period_year', $periodYear)
                    ->exists();

                if (!$existingMadrasah) {
                    $amount = $madrasahConfig 
                        ? $this->calculateFinalAmount($madrasahConfig, $santri->id, $defaultMadrasahAmount)
                        : $defaultMadrasahAmount;

                    Bill::create([
                        'id' => Str::uuid()->toString(),
                        'person_id' => $santri->id,
                        'bill_type' => 'syahriah_madrasah',
                        'billing_config_id' => $madrasahConfig?->id,
                        'period_month' => $semesterNumber,
                        'period_year' => $periodYear,
                        'amount' => $amount,
                        'amount_paid' => 0.00,
                        'status' => $amount == 0.00 ? 'paid' : 'unpaid',
                        'due_date' => null,
                        'created_by' => $createdByUserId,
                    ]);
                    $generatedCount++;
                }

                // 2. Generate Kebersihan Bill (Mukim only)
                if ($isMukim) {
                    $existingKebersihan = Bill::where('person_id', $santri->id)
                        ->where('bill_type', 'kebersihan')
                        ->where('period_month', $semesterNumber)
                        ->where('period_year', $periodYear)
                        ->exists();

                    if (!$existingKebersihan) {
                        $amount = $kebersihanConfig 
                            ? $this->calculateFinalAmount($kebersihanConfig, $santri->id, $defaultKebersihanAmount)
                            : $defaultKebersihanAmount;

                        Bill::create([
                            'id' => Str::uuid()->toString(),
                            'person_id' => $santri->id,
                            'bill_type' => 'kebersihan',
                            'billing_config_id' => $kebersihanConfig?->id,
                            'period_month' => $semesterNumber,
                            'period_year' => $periodYear,
                            'amount' => $amount,
                            'amount_paid' => 0.00,
                            'status' => $amount == 0.00 ? 'paid' : 'unpaid',
                            'due_date' => null,
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
     * Record a new payment for a bill. Supports negative amount for refund.
     */
    public function recordPayment(string $billId, float $amount, string $method, ?string $notes, string $loggedByUserId): BillPayment
    {
        return DB::transaction(function () use ($billId, $amount, $method, $notes, $loggedByUserId) {
            $bill = Bill::findOrFail($billId);

            $payment = BillPayment::create([
                'id' => Str::uuid()->toString(),
                'bill_id' => $bill->id,
                'amount_paid' => $amount,
                'payment_date' => now()->toDateString(),
                'payment_method' => strtolower($method),
                'logged_by' => $loggedByUserId,
                'notes' => $notes,
            ]);

            // recalculate status
            $bill->recalculateStatus();

            return $payment;
        });
    }

    /**
     * Generate Biaya Kitab bills for all active santri in a specific kelas.
     * Billed per semester alongside Syahriah Madrasah.
     *
     * @param  string $kelasId      UUID of the madrasah_kelas
     * @param  int    $semester     1 or 2 (stored in period_month)
     * @param  int    $year         Academic year (e.g. 2026)
     * @param  float  $amount       Amount per santri (varies per kelas/jenjang)
     * @param  string $createdByUserId
     * @return array{generated: int, skipped: int}
     */
    public function generateKitabBills(string $kelasId, int $semester, int $year, float $amount, string $createdByUserId): array
    {
        return DB::transaction(function () use ($kelasId, $semester, $year, $amount, $createdByUserId) {
            $generated = 0;
            $skipped   = 0;

            $enrollments = \App\Modules\Madrasah\Models\MadrasahEnrollment::where('kelas_id', $kelasId)
                ->where('is_active', true)
                ->get();

            foreach ($enrollments as $enrollment) {
                $exists = Bill::where('person_id', $enrollment->person_id)
                    ->where('bill_type', 'kitab')
                    ->where('reference_id', $kelasId)
                    ->where('period_month', $semester)
                    ->where('period_year', $year)
                    ->exists();

                if (!$exists) {
                    Bill::create([
                        'id'           => Str::uuid()->toString(),
                        'person_id'    => $enrollment->person_id,
                        'bill_type'    => 'kitab',
                        'reference_id' => $kelasId,
                        'period_month' => $semester,
                        'period_year'  => $year,
                        'amount'       => $amount,
                        'amount_paid'  => 0.00,
                        'status'       => 'unpaid',
                        'due_date'     => null,
                        'created_by'   => $createdByUserId,
                    ]);
                    $generated++;
                } else {
                    $skipped++;
                }
            }

            return ['generated' => $generated, 'skipped' => $skipped];
        });
    }

    /**
     * Process bulk payment for multiple bills at once.
     * Used by Lembar Setoran Komplek & Madrasah.
     *
     * @param  string[] $billIds         Array of Bill UUIDs to mark as paid (remaining balance)
     * @param  string   $method          CASH | TRANSFER
     * @param  string   $notes           e.g. "Setoran Komplek Al-Falah Juli 2026"
     * @param  string   $loggedByUserId
     * @return array{processed: int, total_amount: float}
     */
    public function processBulkPayment(array $billIds, string $method, string $notes, string $loggedByUserId): array
    {
        return DB::transaction(function () use ($billIds, $method, $notes, $loggedByUserId) {
            $processed   = 0;
            $totalAmount = 0.0;

            foreach ($billIds as $billId) {
                $bill = Bill::find($billId);
                if (!$bill) continue;

                $remaining = (float) $bill->amount - (float) $bill->amount_paid;
                if ($remaining <= 0) continue;

                BillPayment::create([
                    'id'             => Str::uuid()->toString(),
                    'bill_id'        => $bill->id,
                    'amount_paid'    => $remaining,
                    'payment_date'   => now()->toDateString(),
                    'payment_method' => strtolower($method),
                    'logged_by'      => $loggedByUserId,
                    'notes'          => $notes,
                ]);

                $bill->recalculateStatus();
                $totalAmount += $remaining;
                $processed++;
            }

            return [
                'processed'    => $processed,
                'total_amount' => $totalAmount,
            ];
        });
    }

    /**
     * Helper to calculate final bill amount applying exceptions/discounts.
     */
    public function calculateFinalAmount(BillingConfiguration $config, string $personId, float $defaultAmount): float
    {
        $exception = BillingException::where('billing_config_id', $config->id)
            ->where('person_id', $personId)
            ->first();

        if (!$exception) {
            return $defaultAmount;
        }

        if ($exception->exception_type === 'waived') {
            return 0.00;
        }

        if ($exception->exception_type === 'discount') {
            return max(0.00, $defaultAmount - $exception->amount);
        }

        if ($exception->exception_type === 'custom_rate') {
            return $exception->amount;
        }

        return $defaultAmount;
    }

    /**
     * Generate parent bill and child installment bills for a santri.
     */
    public function generateInstallmentBills(string $personId, string $billingConfigId, float $totalAmount, int $termCount, string $createdByUserId): array
    {
        return DB::transaction(function () use ($personId, $billingConfigId, $totalAmount, $termCount, $createdByUserId) {
            $config = BillingConfiguration::findOrFail($billingConfigId);

            // 1. Create parent bill (as a summary)
            $parentBill = Bill::create([
                'id' => Str::uuid()->toString(),
                'person_id' => $personId,
                'bill_type' => $config->type,
                'billing_config_id' => $config->id,
                'period_month' => (int) now()->format('m'),
                'period_year' => (int) now()->format('Y'),
                'amount' => $totalAmount,
                'amount_paid' => 0.00,
                'status' => 'unpaid',
                'due_date' => null,
                'notes' => 'Tagihan Cicilan (Total)',
                'created_by' => $createdByUserId,
            ]);

            // 2. Create child bills (installments)
            $installmentAmount = round($totalAmount / $termCount, 2);
            $lastInstallmentAmount = $totalAmount - ($installmentAmount * ($termCount - 1));

            for ($i = 1; $i <= $termCount; $i++) {
                $termAmount = ($i === $termCount) ? $lastInstallmentAmount : $installmentAmount;
                $dueDate = now()->addMonths($i - 1)->setDate(now()->year, now()->month, 10)->toDateString();

                Bill::create([
                    'id' => Str::uuid()->toString(),
                    'person_id' => $personId,
                    'bill_type' => $config->type,
                    'billing_config_id' => $config->id,
                    'parent_bill_id' => $parentBill->id,
                    'period_month' => (int) now()->addMonths($i - 1)->format('m'),
                    'period_year' => (int) now()->addMonths($i - 1)->format('Y'),
                    'amount' => $termAmount,
                    'amount_paid' => 0.00,
                    'status' => 'unpaid',
                    'due_date' => $dueDate,
                    'notes' => "Cicilan Termin {$i} dari {$termCount}",
                    'created_by' => $createdByUserId,
                ]);
            }

            return ['parent_bill_id' => $parentBill->id, 'terms' => $termCount];
        });
    }

    /**
     * Generate bills dynamically from a config for a specific period (month/semester/year/sub-period).
     */
    public function generateBillsFromConfig(
        string $configId,
        int $periodMonth,
        int $periodYear,
        string $createdByUserId,
        ?int $periodSub = null,
        ?string $targetPersonId = null
    ): array {
        return DB::transaction(function () use ($configId, $periodMonth, $periodYear, $createdByUserId, $periodSub, $targetPersonId) {
            $config = BillingConfiguration::findOrFail($configId);
            $generatedCount = 0;
            $skippedCount = 0;

            // Build base query for active santri
            $santriQuery = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')
                  ->where('enrollment_status', 'aktif');
            });

            if ($targetPersonId) {
                $santriQuery->where('id', $targetPersonId);
            }

            // Gender scoping by logged-in user role
            $user = auth()->user();
            if ($user && !$user->hasRole('admin') && !$user->hasRole('super-admin') && !$user->hasRole('manajemen') && !$user->hasRole('bendahara-pondok') && !$user->hasRole('bendahara-pusat')) {
                if ($user->hasRole('bendahara-putra')) {
                    $santriQuery->where('gender', 'L');
                } elseif ($user->hasRole('bendahara-putri')) {
                    $santriQuery->where('gender', 'P');
                }
            }

            // Extract target parameters safely
            $targetGenders = [];
            $targetIds = [];
            $residenceTargets = [];

            if (is_array($config->target_filters)) {
                if (isset($config->target_filters['genders'])) {
                    $targetGenders = (array)$config->target_filters['genders'];
                } elseif ($config->target_type === 'all') {
                    $targetGenders = array_values(array_intersect(['L', 'P'], $config->target_filters));
                }

                if (isset($config->target_filters['residence'])) {
                    $residenceTargets = (array)$config->target_filters['residence'];
                }

                if (isset($config->target_filters['ids'])) {
                    $targetIds = (array)$config->target_filters['ids'];
                } elseif ($config->target_type !== 'all') {
                    $targetIds = (array)$config->target_filters;
                }
            }

            // Apply residence filtering (Mukim vs Laju)
            if (!empty($residenceTargets) && count($residenceTargets) < 2) {
                if (in_array('mukim', $residenceTargets)) {
                    $santriQuery->where(function($rq) {
                        $rq->whereHas('activeRoles', fn($q) => $q->where('presence_status', 'mukim'))
                          ->orWhereHas('roomAssignments', fn($q) => $q->where('is_active', true));
                    });
                } elseif (in_array('laju', $residenceTargets)) {
                    $santriQuery->where(function($rq) {
                        $rq->whereHas('activeRoles', fn($q) => $q->where('presence_status', 'laju'))
                          ->whereDoesntHave('roomAssignments', fn($q) => $q->where('is_active', true));
                    });
                }
            }

            // Apply penargetan dinamis (targeting)
            if ($config->target_type === 'all') {
                if (!empty($targetGenders) && count($targetGenders) < 2) {
                    $santriQuery->whereIn('gender', $targetGenders);
                }
            } elseif ($config->target_type === 'dormitory') {
                $dormIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
                if (!empty($dormIds)) {
                    $santriQuery->whereIn('id', function($q) use ($dormIds) {
                        $q->select('person_id')
                          ->from('room_assignments')
                          ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
                          ->whereIn('rooms.dormitory_id', $dormIds)
                          ->where('room_assignments.is_active', true);
                    });
                }
            } elseif ($config->target_type === 'kelas') {
                $kelasIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
                if (!empty($kelasIds)) {
                    $santriQuery->whereIn('id', function($q) use ($kelasIds) {
                        $q->select('person_id')
                          ->from('madrasah_enrollments')
                          ->whereIn('kelas_id', $kelasIds)
                          ->where('is_active', true);
                    });
                }
            } elseif ($config->target_type === 'individual') {
                $santriIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
                if (!empty($santriIds)) {
                    $santriQuery->whereIn('id', $santriIds);
                }
            }

            $santriList = $santriQuery->get();

            // Calculate due date based on config
            $dueDate = null;
            $dueDayType = $config->due_day_type ?? 'fixed_day';
            $dueDayValue = $config->due_day_value ?? 10;

            if ($dueDayType === 'fixed_date' && $config->due_date_specific) {
                $dueDate = $config->due_date_specific->toDateString();
            } elseif ($dueDayType === 'days_after') {
                $dueDate = now()->addDays($dueDayValue)->toDateString();
            } elseif ($dueDayType === 'fixed_day') {
                $day = min(max((int)$dueDayValue, 1), 28);
                $dueDate = now()->setDate($periodYear, min(max($periodMonth, 1), 12), $day)->toDateString();
            }

            foreach ($santriList as $santri) {
                $isEventInterval = in_array($config->interval, ['once', 'insidental', 'event', 'sekali']);

                $existsQuery = Bill::where('person_id', $santri->id)
                    ->where('billing_config_id', $config->id)
                    ->where('period_year', $periodYear);

                if (!$isEventInterval) {
                    $existsQuery->where('period_month', $periodMonth);
                }

                if ($periodSub !== null) {
                    $existsQuery->where('period_sub', $periodSub);
                }

                $exists = $existsQuery->exists();

                if (!$exists) {
                    // Calculate individual exception/discount
                    $amount = $this->calculateFinalAmount($config, $santri->id, $config->amount);

                    Bill::create([
                        'id' => Str::uuid()->toString(),
                        'person_id' => $santri->id,
                        'bill_type' => $config->type,
                        'billing_config_id' => $config->id,
                        'period_month' => $periodMonth,
                        'period_year' => $periodYear,
                        'period_sub' => $periodSub,
                        'amount' => $amount,
                        'amount_paid' => 0.00,
                        'status' => $amount == 0.00 ? 'paid' : 'unpaid',
                        'due_date' => $dueDate,
                        'created_by' => $createdByUserId,
                    ]);
                    $generatedCount++;
                } else {
                    $skippedCount++;
                }
            }

            return [
                'generated' => $generatedCount,
                'skipped' => $skippedCount,
            ];
        });
    }

    public function getTargetPersonsForConfig(BillingConfiguration $config, ?string $targetPersonId = null): \Illuminate\Support\Collection
    {
        $santriQuery = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')
              ->where('enrollment_status', 'aktif');
        });

        if ($targetPersonId) {
            $santriQuery->where('id', $targetPersonId);
        }

        $user = auth()->user();
        if ($user && !$user->hasRole('admin') && !$user->hasRole('super-admin') && !$user->hasRole('manajemen') && !$user->hasRole('bendahara-pondok') && !$user->hasRole('bendahara-pusat')) {
            if ($user->hasRole('bendahara-putra')) {
                $santriQuery->where('gender', 'L');
            } elseif ($user->hasRole('bendahara-putri')) {
                $santriQuery->where('gender', 'P');
            }
        }

        $targetGenders = [];
        $targetIds = [];
        $residenceTargets = [];

        if (is_array($config->target_filters)) {
            if (isset($config->target_filters['genders'])) {
                $targetGenders = (array)$config->target_filters['genders'];
            } elseif ($config->target_type === 'all') {
                $targetGenders = array_values(array_intersect(['L', 'P'], $config->target_filters));
            }

            if (isset($config->target_filters['residence'])) {
                $residenceTargets = (array)$config->target_filters['residence'];
            }

            if (isset($config->target_filters['ids'])) {
                $targetIds = (array)$config->target_filters['ids'];
            } elseif ($config->target_type !== 'all') {
                $targetIds = (array)$config->target_filters;
            }
        }

        if (!empty($residenceTargets) && count($residenceTargets) < 2) {
            if (in_array('mukim', $residenceTargets)) {
                $santriQuery->where(function($rq) {
                    $rq->whereHas('activeRoles', fn($q) => $q->where('presence_status', 'mukim'))
                      ->orWhereHas('roomAssignments', fn($q) => $q->where('is_active', true));
                });
            } elseif (in_array('laju', $residenceTargets)) {
                $santriQuery->where(function($rq) {
                    $rq->whereHas('activeRoles', fn($q) => $q->where('presence_status', 'laju'))
                      ->whereDoesntHave('roomAssignments', fn($q) => $q->where('is_active', true));
                });
            }
        }

        if ($config->target_type === 'all') {
            if (!empty($targetGenders) && count($targetGenders) < 2) {
                $santriQuery->whereIn('gender', $targetGenders);
            }
        } elseif ($config->target_type === 'dormitory') {
            $dormIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($dormIds)) {
                $santriQuery->whereIn('id', function($q) use ($dormIds) {
                    $q->select('person_id')
                      ->from('room_assignments')
                      ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
                      ->whereIn('rooms.dormitory_id', $dormIds)
                      ->where('room_assignments.is_active', true);
                });
            }
        } elseif ($config->target_type === 'kelas') {
            $kelasIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($kelasIds)) {
                $santriQuery->whereIn('id', function($q) use ($kelasIds) {
                    $q->select('person_id')
                      ->from('madrasah_enrollments')
                      ->whereIn('kelas_id', $kelasIds)
                      ->where('is_active', true);
                });
            }
        } elseif ($config->target_type === 'individual') {
            $santriIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($santriIds)) {
                $santriQuery->whereIn('id', $santriIds);
            }
        }

        return $santriQuery->get();
    }

    /**
     * Buat paket tagihan pendaftaran santri baru (Putra / Putri / Laju) beserta rincian itemnya.
     */
    public function createRegistrationPackageBill(
        string $personId,
        string $packageType,
        bool $includeMajek = false,
        bool $includeKitab = false,
        ?string $createdByUserId = null
    ): Bill {
        $person = Person::findOrFail($personId);
        $month = (int) now()->format('n');
        $year = (int) now()->format('Y');

        $breakdownItems = [];
        $totalAmount = 0.0;

        if ($packageType === 'putra_mukim') {
            $breakdownItems = [
                'Pendaftaran Pondok' => 50000,
                'Pendaftaran Madrasah Diniyyah' => 30000,
                'Syahriyah Pondok (1 Bulan)' => 35000,
                'Syahriyah Madrasah (1 Semester)' => 150000,
                'Kitab-kitab & Almari' => 45000,
                'Seragam Pondok' => 125000,
                'Kartu Tanda Santri (KTS)' => 10000,
                'Sumbangan Pembangunan' => 200000,
            ];
            $totalAmount = 640000;

            if ($includeKitab) {
                $breakdownItems['Kitab Madrasah Awaliyah 1 Putra'] = 136000;
                $totalAmount += 136000;
            }
            if ($includeMajek) {
                $breakdownItems['Makan Majek (2x makan)'] = 200000;
                $totalAmount += 200000;
            }
        } elseif ($packageType === 'putri_mukim') {
            $breakdownItems = [
                'Pendaftaran Pondok' => 50000,
                'Pendaftaran Madrasah Diniyyah' => 30000,
                'Syahriyah Pondok (1 Bulan)' => 35000,
                'Syahriyah Madrasah (1 Semester)' => 150000,
                'Kitab-kitab & Almari' => 56000,
                'Kitab Madrasah Diniyyah' => 42000,
                'Seragam Pondok' => 180000,
                'Uang Makan di Ndalem (1 Bulan)' => 180000,
                'Sumbangan Pembangunan' => 200000,
            ];
            $totalAmount = 923000;
        } else {
            // Laju (Non-Mukim)
            $breakdownItems = [
                'Pendaftaran Madrasah Diniyyah' => 30000,
                'Syahriyah Madrasah (1 Semester)' => 150000,
                'Kitab Madrasah Diniyyah' => 42000,
                'Kartu Tanda Santri' => 10000,
            ];
            $totalAmount = 232000;
        }

        return Bill::create([
            'id'             => Str::uuid()->toString(),
            'person_id'      => $person->id,
            'bill_type'      => 'pendaftaran_santri_baru',
            'period_month'   => $month,
            'period_year'    => $year,
            'amount'         => $totalAmount,
            'amount_paid'    => 0.00,
            'status'         => 'unpaid',
            'due_date'       => now()->addMonths(3)->toDateString(), // Dapat diangsur 2x dalam 3 bulan
            'notes'          => json_encode([
                'package_name' => strtoupper(str_replace('_', ' ', $packageType)),
                'items'        => $breakdownItems,
            ]),
            'created_by'     => $createdByUserId ?? auth()->id(),
        ]);
    }

    /**
     * Check whether a specific santri (person) is in the target group of a BillingConfiguration.
     * Returns true if they are, false if they are not (but it's still allowed — just a warning).
     */
    public function isSantriInTargetForConfig(BillingConfiguration $config, string $personId): bool
    {
        $person = Person::find($personId);
        if (!$person) return false;

        // Extract target parameters
        $targetGenders     = [];
        $targetIds         = [];
        $residenceTargets  = [];

        if (is_array($config->target_filters)) {
            $targetGenders    = (array)($config->target_filters['genders'] ?? []);
            $residenceTargets = (array)($config->target_filters['residence'] ?? []);

            if (isset($config->target_filters['ids'])) {
                $targetIds = (array)$config->target_filters['ids'];
            } elseif ($config->target_type !== 'all') {
                $targetIds = (array)$config->target_filters;
            }
        }

        // Gender check
        if (!empty($targetGenders) && count($targetGenders) < 2) {
            if (!in_array($person->gender, $targetGenders)) return false;
        }

        // Residence check (mukim vs laju)
        if (!empty($residenceTargets) && count($residenceTargets) < 2) {
            $isMukim = $person->roomAssignments()->where('is_active', true)->exists();
            if (in_array('mukim', $residenceTargets) && !$isMukim) return false;
            if (in_array('laju', $residenceTargets) && $isMukim) return false;
        }

        // Dormitory check
        if ($config->target_type === 'dormitory') {
            $dormIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($dormIds)) {
                $inDorm = $person->roomAssignments()
                    ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
                    ->whereIn('rooms.dormitory_id', $dormIds)
                    ->where('room_assignments.is_active', true)
                    ->exists();
                if (!$inDorm) return false;
            }
        }

        // Kelas check
        if ($config->target_type === 'kelas') {
            $kelasIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($kelasIds)) {
                $inKelas = $person->madrasahEnrollments()
                    ->whereIn('kelas_id', $kelasIds)
                    ->where('is_active', true)
                    ->exists();
                if (!$inKelas) return false;
            }
        }

        // Individual whitelist check
        if ($config->target_type === 'individual') {
            $santriIds = !empty($targetIds) ? $targetIds : ($config->target_filters ?? []);
            if (!empty($santriIds) && !in_array($personId, $santriIds)) return false;
        }

        return true;
    }

    /**
     * Get available (not-yet-billed) periods for a specific santri and config.
     * Returns an array of periods, each with: label, month, year, sub, exists (bool).
     * Always covers 2 full years (tahun ini + tahun depan) regardless of interval.
     */
    public function getAvailablePeriodsForSantri(BillingConfiguration $config, string $personId, int $lookahead = 0): array
    {
        $nowMonth = (int) now()->format('n');
        $nowYear  = (int) now()->format('Y');

        // Auto-calculate lookahead to always cover 2 full years based on interval
        if ($lookahead <= 0) {
            $lookahead = match(true) {
                in_array($config->interval, ['semester', '2x_yearly'])           => 4,  // 4 semester = 2 tahun
                in_array($config->interval, ['caturwulan', '3x_yearly'])         => 6,  // 6 caturwulan = 2 tahun
                in_array($config->interval, ['triwulan', '4x_yearly'])           => 8,  // 8 triwulan = 2 tahun
                in_array($config->interval, ['bimulanan', '6x_yearly'])         => 12, // 12 dwibulanan = 2 tahun
                in_array($config->interval, ['once', 'insidental', 'event', 'sekali']) => 2, // 2 tahun
                default                                                           => 24, // 24 bulan = 2 tahun
            };
        }

        $rawPeriods = [];

        $isEventInterval = in_array($config->interval, ['once', 'insidental', 'event', 'sekali']);

        if ($isEventInterval) {
            // For one-off tariffs: show current year and next year
            for ($offset = 0; $offset <= 1; $offset++) {
                $y = $nowYear + $offset;
                $rawPeriods[] = ['month' => 1, 'year' => $y, 'sub' => null, 'label' => "Tahun {$y}"];
            }

        } elseif (in_array($config->interval, ['semester', '2x_yearly'])) {
            // Mulai dari Semester 1 tahun ini, tampilkan $lookahead semester
            for ($i = 0; $i < $lookahead; $i++) {
                $s = ($i % 2) + 1;
                $y = $nowYear + (int)floor($i / 2);
                $startM = ($s - 1) * 6 + 1;
                $rawPeriods[] = ['month' => $startM, 'year' => $y, 'sub' => $s, 'label' => "Semester {$s} / {$y}"];
            }

        } elseif (in_array($config->interval, ['caturwulan', '3x_yearly'])) {
            // Mulai dari Caturwulan 1 tahun ini
            for ($i = 0; $i < $lookahead; $i++) {
                $cw = ($i % 3) + 1;
                $y  = $nowYear + (int)floor($i / 3);
                $startM = ($cw - 1) * 4 + 1;
                $rawPeriods[] = ['month' => $startM, 'year' => $y, 'sub' => $cw, 'label' => "Caturwulan {$cw} / {$y}"];
            }

        } elseif (in_array($config->interval, ['triwulan', '4x_yearly'])) {
            // Mulai dari Triwulan 1 tahun ini
            for ($i = 0; $i < $lookahead; $i++) {
                $tw = ($i % 4) + 1;
                $y  = $nowYear + (int)floor($i / 4);
                $startM = ($tw - 1) * 3 + 1;
                $rawPeriods[] = ['month' => $startM, 'year' => $y, 'sub' => $tw, 'label' => "Triwulan {$tw} / {$y}"];
            }

        } elseif (in_array($config->interval, ['bimulanan', '6x_yearly'])) {
            // Mulai dari Dwibulanan 1 (Jan–Feb) tahun ini
            for ($i = 0; $i < $lookahead; $i++) {
                $b = ($i % 6) + 1;
                $y = $nowYear + (int)floor($i / 6);
                $startM = ($b - 1) * 2 + 1;
                $rawPeriods[] = ['month' => $startM, 'year' => $y, 'sub' => $b, 'label' => "Dwibulanan {$b} / {$y}"];
            }

        } else {
            // Monthly: Mulai Januari tahun ini, tampilkan $lookahead bulan ke depan
            for ($i = 0; $i < $lookahead; $i++) {
                $m = ($i % 12) + 1;
                $y = $nowYear + (int)floor($i / 12);
                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                $rawPeriods[] = ['month' => $m, 'year' => $y, 'sub' => null, 'label' => "{$monthName} {$y}"];
            }
        }

        // Check existence for each period
        foreach ($rawPeriods as &$p) {
            $query = Bill::where('person_id', $personId)
                ->where('billing_config_id', $config->id)
                ->where('period_year', $p['year']);

            if (!$isEventInterval) {
                $query->where('period_month', $p['month']);
            }

            $p['exists'] = $query->exists();
        }
        unset($p);

        return $rawPeriods;
    }
}
