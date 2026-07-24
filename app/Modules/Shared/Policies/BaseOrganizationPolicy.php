<?php

namespace App\Modules\Shared\Policies;

use App\Models\User;

/**
 * Abstract base untuk semua policy yang butuh scope per unit organisasi.
 *
 * Prinsip:
 * - super-admin dan pengasuh selalu lolos (bypass via Gate::before di AppServiceProvider)
 * - Policy ini HANYA menangani unit-scope check untuk role lainnya
 */
abstract class BaseOrganizationPolicy
{
    /**
     * Apakah user bertugas di organisasi ini?
     * Dipakai untuk membatasi akses musyrif, guru, bendahara-unit, dll.
     */
    protected function userBelongsToOrganization(User $user, string $organizationId): bool
    {
        return $user->isInOrganization($organizationId);
    }

    /**
     * Apakah user adalah super-admin atau pengasuh?
     * Biasanya tidak perlu dipanggil karena Gate::before sudah handle,
     * tapi tersedia sebagai safety net.
     */
    protected function isSuperOrPengasuh(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'pengasuh']);
    }

    /**
     * Apakah user memiliki salah satu dari role yang diberikan?
     */
    protected function hasAnyRole(User $user, array $roles): bool
    {
        return $user->hasAnyRole($roles);
    }
}
