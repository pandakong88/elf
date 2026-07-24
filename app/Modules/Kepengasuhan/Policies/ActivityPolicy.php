<?php

namespace App\Modules\Kepengasuhan\Policies;

use App\Models\User;
use App\Modules\Shared\Policies\BaseOrganizationPolicy;

class ActivityPolicy extends BaseOrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-kegiatan');
    }

    public function view(User $user, string $activityOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('manage-kegiatan')) {
            return $this->userBelongsToOrganization($user, $activityOrganizationId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-kegiatan');
    }

    public function update(User $user, string $activityOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('manage-kegiatan')) {
            return $this->userBelongsToOrganization($user, $activityOrganizationId);
        }

        return false;
    }

    public function recordAttendance(User $user, string $activityOrganizationId): bool
    {
        if ($this->isSuperOrPengasuh($user)) {
            return true;
        }

        if ($user->can('manage-kegiatan')) {
            return $this->userBelongsToOrganization($user, $activityOrganizationId);
        }

        return false;
    }
}
