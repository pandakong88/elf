<?php

namespace App\Traits;

trait HasGenderScope
{
    /**
     * Returns the gender scope based on logged-in user's profile person gender.
     */
    protected function genderScope(): ?string
    {
        $user = auth()->user();
        if (!$user) return null;

        // Super Admin & Manajemen bypass gender scoping
        if ($user->hasRole('super-admin') || $user->hasRole('manajemen')) {
            return null;
        }

        // 1. Check associated person profile
        if ($user->person?->gender) {
            return $user->person->gender;
        }

        // 2. Fallback to gendered roles for backward compatibility
        if ($user->hasRole('bendahara-putra')) {
            return 'L';
        }
        if ($user->hasRole('bendahara-putri')) {
            return 'P';
        }

        return null;
    }
}
