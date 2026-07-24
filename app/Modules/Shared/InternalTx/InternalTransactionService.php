<?php

namespace App\Modules\Shared\InternalTx;

use App\Modules\Core\Models\InternalTransaction;
use App\Modules\Core\Models\Organization;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InternalTransactionService
{
    /**
     * Buat transaksi internal baru antara dua unit.
     *
     * @throws DomainException
     */
    public function create(
        string $requesterUnitId,
        string $providerUnitId,
        string $referenceType,
        string $referenceId,
        float $amount,
        ?string $description = null
    ): InternalTransaction {
        if ($requesterUnitId === $providerUnitId) {
            throw new DomainException('Requester dan provider tidak boleh unit yang sama.');
        }

        if ($amount <= 0) {
            throw new DomainException('Nominal transaksi harus lebih dari 0.');
        }

        // Pastikan kedua unit valid
        Organization::findOrFail($requesterUnitId);
        Organization::findOrFail($providerUnitId);

        return InternalTransaction::create([
            'requester_unit_id' => $requesterUnitId,
            'provider_unit_id'  => $providerUnitId,
            'reference_type'    => $referenceType,
            'reference_id'      => $referenceId,
            'description'       => $description,
            'amount'            => $amount,
            'status'            => 'pending',
        ]);
    }

    /**
     * Tandai transaksi sebagai sudah dipenuhi (barang/jasa dikirim).
     *
     * @throws DomainException
     */
    public function fulfill(InternalTransaction $transaction): InternalTransaction
    {
        if ($transaction->status !== 'pending') {
            throw new DomainException(
                "Transaksi hanya bisa di-fulfill dari status 'pending'. Status saat ini: '{$transaction->status}'."
            );
        }

        $transaction->update(['status' => 'fulfilled']);

        return $transaction->fresh();
    }

    /**
     * Tandai transaksi sebagai sudah ditagih (invoice diterbitkan).
     *
     * @throws DomainException
     */
    public function invoice(InternalTransaction $transaction): InternalTransaction
    {
        if ($transaction->status !== 'fulfilled') {
            throw new DomainException(
                "Transaksi hanya bisa di-invoice dari status 'fulfilled'. Status saat ini: '{$transaction->status}'."
            );
        }

        $transaction->update(['status' => 'invoiced']);

        return $transaction->fresh();
    }

    /**
     * Selesaikan transaksi (payment settled).
     *
     * @throws DomainException
     */
    public function settle(InternalTransaction $transaction): InternalTransaction
    {
        if ($transaction->status !== 'invoiced') {
            throw new DomainException(
                "Transaksi hanya bisa di-settle dari status 'invoiced'. Status saat ini: '{$transaction->status}'."
            );
        }

        $transaction->update([
            'status'      => 'settled',
            'settled_at'  => now(),
        ]);

        return $transaction->fresh();
    }

    /**
     * Ambil semua transaksi yang belum selesai untuk sebuah unit (sebagai requester atau provider).
     */
    public function getPendingByUnit(string $organizationId): Collection
    {
        return InternalTransaction::byUnit($organizationId)
                                  ->unsettled()
                                  ->with(['requesterUnit', 'providerUnit'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();
    }
}
