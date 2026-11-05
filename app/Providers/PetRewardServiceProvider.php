<?php

namespace App\Providers;

use App\Services\PetRewardService;
use Illuminate\Support\ServiceProvider;

class PetRewardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Mendaftarkan listener pasif ke model-model aksi utama tanpa mengubah code controller/livewire.
     */
    public function boot(): void
    {
        $this->registerOperationalModelHooks();
    }

    /**
     * Daftarkan hooks ke model-model kerja santri & keuangan.
     */
    protected function registerOperationalModelHooks(): void
    {
        // 1. Presensi Santri
        if (class_exists(\App\Modules\Kepengasuhan\Models\ActivityAttendance::class)) {
            \App\Modules\Kepengasuhan\Models\ActivityAttendance::created(function () {
                PetRewardService::rewardCurrentAdmin('attendance');
            });
        }

        // 2. Perizinan Santri
        if (class_exists(\App\Modules\Kepengasuhan\Models\Perizinan::class)) {
            \App\Modules\Kepengasuhan\Models\Perizinan::created(function () {
                PetRewardService::rewardCurrentAdmin('perizinan');
            });
        }

        // 3. Catatan Pelanggaran / Disiplin
        if (class_exists(\App\Modules\Kepengasuhan\Models\Violation::class)) {
            \App\Modules\Kepengasuhan\Models\Violation::created(function () {
                PetRewardService::rewardCurrentAdmin('violation');
            });
        }

        // 4. Pembayaran Tagihan Keuangan
        if (class_exists(\App\Modules\Keuangan\Models\BillPayment::class)) {
            \App\Modules\Keuangan\Models\BillPayment::created(function () {
                PetRewardService::rewardCurrentAdmin('payment');
            });
        }

        // 5. Pendaftaran Profil Santri Baru
        if (class_exists(\App\Modules\Kepengasuhan\Models\SantriProfile::class)) {
            \App\Modules\Kepengasuhan\Models\SantriProfile::created(function () {
                PetRewardService::rewardCurrentAdmin('santri_create');
            });
        }

        // 6. Respon Sensus Asrama
        if (class_exists(\App\Modules\Kepengasuhan\Models\CensusV3Response::class)) {
            \App\Modules\Kepengasuhan\Models\CensusV3Response::created(function () {
                PetRewardService::rewardCurrentAdmin('census');
            });
        }
    }
}
