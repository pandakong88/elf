<?php

namespace App\Modules\Kepengasuhan\Policies;

use App\Models\User;
use App\Modules\Shared\Policies\BaseOrganizationPolicy;

class PerizinanPolicy extends BaseOrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-perizinan');
    }

    public function view(User $user, string $perizinanOrganizationId, string $santriPersonId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        // Wali-santri hanya bisa lihat izin anak sendiri
        if ($user->hasRole('wali-santri')) {
            if ($user->person_id) {
                // Relasi wali ke santri via person_roles
                // Santri memiliki person_roles.wali yang diarahkan ke person wali ini.
                // Untuk sekarang, kita lakukan pengecekan sederhana atau bypass jika valid
                return true; // check riil diimplementasikan saat modul wali santri didesain detail
            }
            return false;
        }

        if ($user->can('view-perizinan')) {
            return $this->userBelongsToOrganization($user, $perizinanOrganizationId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-perizinan');
    }

    public function approve(User $user, string $perizinanOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('approve-perizinan')) {
            return $this->userBelongsToOrganization($user, $perizinanOrganizationId);
        }

        return false;
    }
}
