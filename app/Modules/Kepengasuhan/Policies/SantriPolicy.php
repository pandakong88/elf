<?php

namespace App\Modules\Kepengasuhan\Policies;

use App\Models\User;
use App\Modules\Shared\Policies\BaseOrganizationPolicy;

/**
 * SantriPolicy — contoh implementasi konkret policy.
 *
 * Aturan:
 * - super-admin & pengasuh: bypass semua via Gate::before
 * - musyrif: bisa view-any (semua santri di unitnya), view/update santri di unitnya
 * - admin-data: bisa view-any, create, update (semua unit)
 * - wali-santri: hanya bisa lihat anaknya sendiri (cek via person_roles.wali)
 *
 * Santri di sini direpresentasikan oleh person_id + organization_id dari PersonRole.
 * Bukan model Santri terpisah — sesuai prinsip "satu persons table".
 */
class SantriPolicy extends BaseOrganizationPolicy
{
    /**
     * Apakah user bisa melihat daftar santri?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-santri');
    }

    /**
     * Apakah user bisa melihat satu santri tertentu?
     *
     * @param  string  $santriOrganizationId  organization_id dari santri tersebut
     */
    public function viewInOrganization(User $user, string $santriOrganizationId): bool
    {
        // Musyrif hanya bisa lihat santri di unitnya
        if ($user->hasRole('musyrif')) {
            return $this->userBelongsToOrganization($user, $santriOrganizationId);
        }

        // admin-data bisa lihat semua
        if ($user->hasRole('admin-data')) {
            return $user->can('view-any-santri');
        }

        return false;
    }

    /**
     * Apakah user bisa membuat data santri baru?
     */
    public function create(User $user): bool
    {
        return $user->can('create-person');
    }

    /**
     * Apakah user bisa update data santri?
     *
     * @param  string  $santriOrganizationId  organization_id dari santri tersebut
     */
    public function updateInOrganization(User $user, string $santriOrganizationId): bool
    {
        if (! $user->can('update-person')) {
            return false;
        }

        // Musyrif hanya bisa update santri di unitnya
        if ($user->hasRole('musyrif')) {
            return $this->userBelongsToOrganization($user, $santriOrganizationId);
        }

        // admin-data bisa update semua
        return $user->hasRole('admin-data');
    }

    /**
     * Apakah user bisa menghapus (soft-delete) data santri?
     * Hanya admin-data dan di atas.
     */
    public function delete(User $user): bool
    {
        return $user->can('delete-person');
    }
}
