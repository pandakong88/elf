<?php

namespace App\Modules\Kepengasuhan\Policies;

use App\Models\User;
use App\Modules\Shared\Policies\BaseOrganizationPolicy;

class DormitoryPolicy extends BaseOrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-asrama');
    }

    public function view(User $user, string $dormitoryOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('manage-asrama')) {
            return $this->userBelongsToOrganization($user, $dormitoryOrganizationId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-asrama');
    }

    public function update(User $user, string $dormitoryOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('manage-asrama')) {
            return $this->userBelongsToOrganization($user, $dormitoryOrganizationId);
        }

        return false;
    }
}
