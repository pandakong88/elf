<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\SantriLoan;
use App\Modules\Keuangan\Models\SantriLoanPayment;
use Illuminate\Support\Facades\DB;

class SantriLoanService
{
    /**
     * Create a new Santri Loan.
     */
    public function createLoan(array $data, ?string $userId = null): SantriLoan
    {
        return DB::transaction(function () use ($data, $userId) {
            $person = Person::findOrFail($data['person_id']);

            $loan = SantriLoan::create([
                'loan_code'              => SantriLoan::generateLoanCode(),
                'person_id'              => $person->id,
                'gender'                 => $person->gender ?: 'L',
                'amount'                 => (float) $data['amount'],
                'amount_paid'            => 0.00,
                'status'                 => 'unpaid',
                'borrowed_at'            => $data['borrowed_at'] ?? now()->toDateString(),
                'due_date'               => !empty($data['due_date']) ? $data['due_date'] : null,
                'reason'                 => $data['reason'] ?? null,
                'is_visible_to_guardian' => (bool) ($data['is_visible_to_guardian'] ?? false),
                'created_by'             => $userId,
                'notes'                  => $data['notes'] ?? null,
            ]);

            if ($loan->is_visible_to_guardian) {
                $this->syncBillForLoan($loan);
            }

            return $loan;
        });
    }

    /**
     * Toggle visibility to guardian and sync the bill.
     */
    public function toggleGuardianVisibility(SantriLoan $loan): bool
    {
        return DB::transaction(function () use ($loan) {
            $loan->is_visible_to_guardian = !$loan->is_visible_to_guardian;
            $loan->save();

            $this->syncBillForLoan($loan);

            return $loan->is_visible_to_guardian;
        });
    }

    /**
     * Sync Bill record according to loan's visibility and status.
     */
    public function syncBillForLoan(SantriLoan $loan): ?Bill
    {
        if ($loan->is_visible_to_guardian) {
            $bill = $loan->bill_id ? Bill::find($loan->bill_id) : null;
            $month = $loan->borrowed_at ? (int) \Carbon\Carbon::parse($loan->borrowed_at)->format('m') : (int) now()->format('m');
            $year  = $loan->borrowed_at ? (int) \Carbon\Carbon::parse($loan->borrowed_at)->format('Y') : (int) now()->format('Y');

            $reasonText = !empty($loan->reason) ? $loan->reason : 'Kebutuhan Mendesak Santri';
            $billNote   = 'Keperluan: ' . $reasonText . ' (Ref: ' . $loan->loan_code . ')';

            if (!$bill) {
                $bill = Bill::create([
                    'person_id'    => $loan->person_id,
                    'bill_type'    => 'pinjaman',
                    'reference_id' => $loan->id,
                    'period_month' => $month,
                    'period_year'  => $year,
                    'amount'       => $loan->amount,
                    'amount_paid'  => $loan->amount_paid,
                    'status'       => $loan->status,
                    'due_date'     => $loan->due_date ?: null,
                    'notes'        => $billNote,
                    'created_by'   => $loan->created_by ?: auth()->id() ?: \App\Models\User::first()?->id,
                ]);

                $loan->bill_id = $bill->id;
                $loan->saveQuietly();
            } else {
                $bill->update([
                    'amount'      => $loan->amount,
                    'amount_paid' => $loan->amount_paid,
                    'status'      => $loan->status,
                    'due_date'    => $loan->due_date ?: null,
                    'notes'       => $billNote,
                ]);
            }

            return $bill;
        } else {
            // If hidden from guardian, soft delete or remove the bill if unpaid
            if ($loan->bill_id) {
                $bill = Bill::find($loan->bill_id);
                if ($bill && $bill->status === 'unpaid' && $bill->payments()->count() === 0) {
                    $bill->delete();
                    $loan->bill_id = null;
                    $loan->saveQuietly();
                }
            }
            return null;
        }
    }

    /**
     * Record payment (Cash / Offline or Manual)
     */
    public function recordPayment(
        SantriLoan $loan,
        float $amount,
        string $method = 'cash',
        ?string $userId = null,
        ?string $receiptNo = null,
        ?string $notes = null
    ): SantriLoanPayment {
        return DB::transaction(function () use ($loan, $amount, $method, $userId, $receiptNo, $notes) {
            $paymentCode = SantriLoanPayment::generatePaymentCode();
            $receipt = $receiptNo ?: ('KSR-LN-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4)));
            $actorId = $userId ?: auth()->id() ?: \App\Models\User::first()?->id;

            $payment = SantriLoanPayment::create([
                'loan_id'        => $loan->id,
                'payment_code'   => $paymentCode,
                'amount'         => $amount,
                'payment_method' => $method,
                'payment_date'   => now()->toDateString(),
                'received_by'    => $actorId,
                'receipt_no'     => $receipt,
                'notes'          => $notes,
            ]);

            $loan->recalculateStatus();

            // If a bill is linked to this loan, sync BillPayment as well
            if ($loan->bill_id && $loan->bill) {
                $bill = $loan->bill;
                BillPayment::create([
                    'bill_id'          => $bill->id,
                    'receipt_no'       => $receipt,
                    'amount_paid'      => $amount,
                    'payment_date'     => now()->toDateString(),
                    'payment_method'   => $method,
                    'logged_by'        => $actorId,
                    'notes'            => 'Pelunasan Pinjaman Santri [' . $loan->loan_code . ']',
                ]);
            }

            return $payment;
        });
    }

    /**
     * Synchronize payment made through Wali Portal (Manual transfer approval or Gateway).
     */
    public function syncFromPortalPayment(Bill $bill, float $amountPaid, string $method, ?string $receiptNo, ?string $userId = null): void
    {
        if ($bill->bill_type !== 'pinjaman' || empty($bill->reference_id)) {
            return;
        }

        $loan = SantriLoan::find($bill->reference_id);
        if (!$loan) return;

        // Check if already recorded with this receipt_no
        if ($receiptNo && SantriLoanPayment::where('loan_id', $loan->id)->where('receipt_no', $receiptNo)->exists()) {
            return;
        }

        SantriLoanPayment::create([
            'loan_id'        => $loan->id,
            'payment_code'   => SantriLoanPayment::generatePaymentCode(),
            'amount'         => $amountPaid,
            'payment_method' => 'portal_wali',
            'payment_date'   => now()->toDateString(),
            'received_by'    => $userId,
            'receipt_no'     => $receiptNo,
            'notes'          => 'Pembayaran via Portal Wali (' . ucfirst($method) . ')',
        ]);

        $loan->recalculateStatus();
    }
}
