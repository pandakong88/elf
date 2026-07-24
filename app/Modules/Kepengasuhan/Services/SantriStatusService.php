<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\SantriStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DomainException;

class SantriStatusService
{
    /**
     * Ubah presence status santri (status keberadaan harian).
     * Dikelola oleh musyrif / level keatas.
     */
    public function changePresenceStatus(
        string  $roleId,
        string  $newStatus,
        string  $changedBy,
        ?string $until = null,
        ?string $notes = null
    ): PersonRole {
        $role = PersonRole::findOrFail($roleId);

        $validStatuses = array_keys(PersonRole::PRESENCE_STATUSES);
        if (!in_array($newStatus, $validStatuses)) {
            throw new DomainException("Status keberadaan '{$newStatus}' tidak valid.");
        }

        // Hanya santri aktif (aktif atau aktif_laju) yang bisa diubah keberadaannya
        if (!$role->isActiveEnrollment()) {
            throw new DomainException('Santri yang sudah tidak aktif tidak dapat diubah status keberadaannya.');
        }

        $oldStatus = $role->presence_status;

        $role->update([
            'presence_status'       => $newStatus,
            'presence_status_since' => now(),
            'presence_status_until' => $until ? Carbon::parse($until) : null,
            'presence_status_notes' => $notes,
        ]);

        // Jika status keberadaan berubah menjadi laju, otomatis nonaktifkan kamar aktifnya
        if ($newStatus === 'laju') {
            \App\Modules\Kepengasuhan\Models\RoomAssignment::where('person_id', $role->person_id)
                ->where('is_active', true)
                ->update([
                    'is_active'   => false,
                    'valid_until' => now()->toDateString(),
                ]);
        }

        SantriStatusLog::create([
            'id'            => Str::uuid()->toString(),
            'person_id'     => $role->person_id,
            'role_id'       => $role->id,
            'changed_field' => 'presence_status',
            'old_value'     => $oldStatus,
            'new_value'     => $newStatus,
            'changed_by'    => $changedBy,
            'notes'         => $notes,
            'changed_at'    => now(),
        ]);

        return $role->fresh();
    }

    /**
     * Ubah enrollment status santri (status keanggotaan formal).
     * Hanya bisa dilakukan oleh manajemen / super-admin.
     *
     * @param string $newStatus  Salah satu dari: aktif, aktif_laju, alumni, keluar_resmi, pindah, dikeluarkan, tanpa_keterangan
     * @param string|null $leftAt  Tanggal boyong/keluar (khusus untuk status non-aktif)
     */
    public function changeEnrollmentStatus(
        string  $roleId,
        string  $newStatus,
        string  $changedBy,
        ?string $notes = null,
        ?string $leftAt = null
    ): PersonRole {
        $role = PersonRole::findOrFail($roleId);

        $validStatuses = array_keys(PersonRole::ENROLLMENT_STATUSES);
        if (!in_array($newStatus, $validStatuses)) {
            throw new DomainException("Status keanggotaan '{$newStatus}' tidak valid.");
        }

        $oldStatus = $role->enrollment_status;

        $updates = [
            'enrollment_status' => $newStatus,
        ];

        // Jika menjadi tidak aktif, catat tanggal keluar dan hapus presence_status
        $isBecomingInactive = in_array($newStatus, PersonRole::INACTIVE_ENROLLMENT_STATUSES);
        if ($isBecomingInactive) {
            $updates['presence_status']       = null;
            $updates['presence_status_since'] = null;
            $updates['presence_status_until'] = null;
            $updates['presence_status_notes'] = null;
            $updates['left_at']               = $leftAt ? Carbon::parse($leftAt)->toDateString() : now()->toDateString();
            $updates['is_active']             = false;

            // Otomatis non-aktifkan alokasi kamar & kelas aktif
            \App\Modules\Kepengasuhan\Models\RoomAssignment::where('person_id', $role->person_id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'valid_until' => now()->toDateString()]);

            \App\Modules\Madrasah\Models\MadrasahEnrollment::where('person_id', $role->person_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        // Jika kembali aktif (dari tanpa_keterangan misalnya), reset left_at
        if ($newStatus === 'aktif') {
            $updates['left_at']   = null;
            $updates['is_active'] = true;
            // Set default presence: mukim untuk aktif
            $updates['presence_status'] = 'mukim';
        }

        $role->update($updates);

        SantriStatusLog::create([
            'id'            => Str::uuid()->toString(),
            'person_id'     => $role->person_id,
            'role_id'       => $role->id,
            'changed_field' => 'enrollment_status',
            'old_value'     => $oldStatus,
            'new_value'     => $newStatus,
            'changed_by'    => $changedBy,
            'notes'         => $notes,
            'changed_at'    => now(),
        ]);

        return $role->fresh();
    }

    /**
     * Ambil riwayat status santri.
     */
    public function getStatusHistory(string $personId)
    {
        return SantriStatusLog::with('changedBy')
            ->where('person_id', $personId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }
}
