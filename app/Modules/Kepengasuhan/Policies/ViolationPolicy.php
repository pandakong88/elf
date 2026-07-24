<?php

namespace App\Modules\Kepengasuhan\Policies;

use App\Models\User;
use App\Modules\Shared\Policies\BaseOrganizationPolicy;

class ViolationPolicy extends BaseOrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-pelanggaran');
    }

    public function view(User $user, string $violationOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('view-pelanggaran')) {
            return $this->userBelongsToOrganization($user, $violationOrganizationId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-pelanggaran');
    }

    public function resolve(User $user, string $violationOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        // Musyrif yang memegang wewenang unit asrama tersebut bisa resolve
        if ($user->can('create-pelanggaran')) {
            return $this->userBelongsToOrganization($user, $violationOrganizationId);
        }

        return false;
    }
}
