<?php

namespace App\Services;

use App\Models\AdminVirtualPet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PetRewardService
{
    /**
     * Berikan reward koin & XP kepada user yang sedang aktif.
     * Menggunakan fail-safe try-catch: tidak akan pernah menggagalkan proses sistem utama.
     */
    public static function rewardCurrentAdmin(string $actionType): void
    {
        try {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            if (!$user instanceof User) {
                return;
            }

            $rewardsMap = [
                'attendance' => [
                    'coins' => 15,
                    'xp' => 10,
                    'reason' => 'Mencatat Presensi Santri',
                ],
                'perizinan' => [
                    'coins' => 20,
                    'xp' => 15,
                    'reason' => 'Menerbitkan Perizinan Santri',
                ],
                'violation' => [
                    'coins' => 20,
                    'xp' => 15,
                    'reason' => 'Mencatat Pelanggaran Disiplin',
                ],
                'payment' => [
                    'coins' => 25,
                    'xp' => 20,
                    'reason' => 'Mencatat Pembayaran Keuangan',
                ],
                'santri_create' => [
                    'coins' => 30,
                    'xp' => 25,
                    'reason' => 'Pendaftaran Santri Baru',
                ],
                'census' => [
                    'coins' => 20,
                    'xp' => 15,
                    'reason' => 'Pengisian Sensus Asrama',
                ],
            ];

            if (!isset($rewardsMap[$actionType])) {
                return;
            }

            $reward = $rewardsMap[$actionType];
            self::rewardUser($user, $reward['coins'], $reward['xp'], $reward['reason']);
        } catch (\Throwable $e) {
            // Fail-safe: Catat ke log tanpa melempar exception agar alur kerja admin tetap 100% lancar
            Log::debug('[PetRewardService] Silently bypassed reward error: ' . $e->getMessage());
        }
    }

    /**
     * Eksekusi pemberian reward ke model AdminVirtualPet.
     */
    public static function rewardUser(User $user, int $coins, int $xp, string $reason): void
    {
        try {
            $pet = AdminVirtualPet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'pet_type' => 'kibo',
                    'name' => 'Kibo',
                    'coins' => 100,
                    'level' => 1,
                    'xp' => 0,
                    'hunger' => 80,
                    'hygiene' => 80,
                    'energy' => 90,
                    'happiness' => 85,
                    'inventory' => [
                        'apple' => 2,
                        'kurma' => 1,
                        'milk' => 1,
                        'kebuli' => 0,
                    ],
                ]
            );

            $pet->addReward($coins, $xp, $reason);
        } catch (\Throwable $e) {
            Log::debug('[PetRewardService] Failed to reward pet: ' . $e->getMessage());
        }
    }
}
