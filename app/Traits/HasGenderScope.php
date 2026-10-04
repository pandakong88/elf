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

        // Central roles bypass gender scoping (all access: Putra & Putri)
        if ($user->hasRole([
            'super-admin',
            'admin',
            'manajemen',
            'pengasuh',
            'bendahara',
            'bendahara-pondok',
            'bendahara-pusat',
            'bendahara-unit',
            'admin-data',
        ])) {
            return null;
        }

        // Gender-specific roles
        if ($user->hasRole(['bendahara-putra', 'lurah-putra'])) {
            return 'L';
        }
        if ($user->hasRole(['bendahara-putri', 'lurah-putri'])) {
            return 'P';
        }

        // Check associated person profile
        if ($user->person?->gender) {
            return $user->person->gender;
        }

        return null;
    }
}
