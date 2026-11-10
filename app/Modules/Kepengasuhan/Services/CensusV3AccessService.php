<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Models\User;
use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;

class CensusV3AccessService
{
    /**
     * Apakah user adalah admin pusat (super-admin atau manajemen)?
     */
    public function isCentral(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole([
            'super-admin',
            'admin',
            'manajemen',
            'pengasuh',
            'bendahara-pondok',
            'bendahara-pusat',
            'admin-data',
        ]);
    }

    /**
     * Scope gender untuk user (null = tanpa batasan / semua komplek, 'L' = Putra, 'P' = Putri).
     */
    public function genderScope(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        if ($this->isCentral($user)) {
            return null;
        }

        if ($user->hasRole(['sekretaris-putra', 'bendahara-putra', 'lurah-putra'])) {
            return 'L';
        }

        if ($user->hasRole(['sekretaris-putri', 'bendahara-putri', 'lurah-putri'])) {
            return 'P';
        }

        return null;
    }

    /**
     * Apakah user boleh mengelola kampanye pada level global (terbitkan, review, finalisasi, hapus)?
     */
    public function canManageCampaign(?User $user, CensusV3Campaign $campaign): bool
    {
        if (!$user || !$user->can('manage-sensus-v3')) {
            return false;
        }

        if ($this->isCentral($user)) {
            return true;
        }

        $userScope = $this->genderScope($user);
        if (!$userScope) {
            return false;
        }

        // Jika kampanye secara eksplisit putra / putri
        if ($campaign->target_scope === 'putra' && $userScope === 'L') {
            return true;
        }
        if ($campaign->target_scope === 'putri' && $userScope === 'P') {
            return true;
        }

        // Jika custom dormitories, periksa apakah seluruh asrama di kampanye ini segender
        if ($campaign->target_scope === 'custom_dormitories') {
            $dormitories = $campaign->dormitories()->with('dormitory')->get();
            if ($dormitories->isEmpty()) {
                return true;
            }

            foreach ($dormitories as $cd) {
                if ($cd->dormitory?->gender !== $userScope) {
                    return false;
                }
            }

            return true;
        }

        // Jika kampanye 'all' (campuran), sekretaris hanya mengelola asrama masing-masing,
        // aksi level kampanye (publish/finalize) ditangani admin pusat.
        return false;
    }

    /**
     * Apakah user boleh mengakses laporan asrama tertentu (untuk input atau review)?
     *
     * @param  string  $action  'fill' | 'review'
     */
    public function canAccessDormitory(?User $user, CensusV3CampaignDormitory $cd, string $action = 'fill'): bool
    {
        if (!$user) {
            return false;
        }

        $cd->loadMissing('dormitory');
        $dormGender = $cd->dormitory?->gender;

        if ($this->isCentral($user)) {
            if ($action === 'review') {
                return $user->can('approve-census-v3');
            }
            return true;
        }

        $userScope = $this->genderScope($user);

        if ($action === 'review') {
            // Harus punya izin approve dan manage kampanye sensus
            if (!$user->can('approve-census-v3') || !$user->can('manage-sensus-v3')) {
                return false;
            }

            // Harus segender dengan asrama
            return $userScope !== null && $dormGender === $userScope;
        }

        if ($action === 'fill') {
            if (!$user->can('input-census-v3')) {
                return false;
            }

            // Jika sekretaris yang memegang izin manage-sensus-v3, boleh isi seluruh asrama segender
            if ($user->can('manage-sensus-v3') && $userScope !== null && $dormGender === $userScope) {
                return true;
            }

            // Jika petugas yang ditugaskan (musyrif dll)
            return $cd->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Apakah user boleh membuat / mengedit / mengarsipkan template?
     */
    public function canManageTemplate(?User $user, ?CensusTemplate $template = null): bool
    {
        if (!$user || !$user->can('manage-census-template')) {
            return false;
        }

        if ($this->isCentral($user)) {
            return true;
        }

        // Jika template baru
        if (!$template) {
            return true;
        }

        // Sekretaris hanya boleh mengedit / mengarsipkan template yang ia buat sendiri
        return $template->created_by === $user->id;
    }
}
