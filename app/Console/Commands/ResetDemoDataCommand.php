<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDemoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:reset-demo {--force : Jalankan tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan seluruh data dummy santri, wali, tagihan, sensus, komplek, kamar, dan kelas untuk setup awal.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('PERINGATAN! Perintah ini akan MENGOSONGKAN seluruh data santri, wali, tagihan, komplek, kamar & kelas madrasah. Lanjutkan?')) {
                $this->info('Operasi dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $this->info('=== RUNNING MIGRATIONS ===');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        $this->info('=== MENGOSONGKAN DATA DUMMY SISTEM ===');

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $tablesToTruncate = [
            'bill_payments',
            'bills',
            'billing_exceptions',
            'billing_configurations',
            'event_bill_items',
            'event_bills',
            'internal_transactions',
            'majek_registrations',
            'majek_periods',
            'santri_siblings',
            'santri_guardians',
            'guardians',
            'santri_profiles',
            'santri_status_logs',
            'room_assignments',
            'madrasah_enrollments',
            'madrasah_promotion_batch_items',
            'madrasah_promotion_batches',
            'perizinan',
            'violations',
            'activity_attendances',
            'activities',
            'census_responses',
            'census_campaign_dormitories',
            'census_campaigns',
            'census_periods',
            'dormitory_censuses',
            'room_census_details',
            'rooms',
            'dormitories',
            'madrasah_kelas',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                if ($driver === 'sqlite') {
                    DB::table($table)->delete();
                } else {
                    DB::table($table)->truncate();
                }
                $this->line("✔ Table [{$table}]: Dikosongkan ({$count} data dihapus)");
            }
        }

        $userPersonIds = User::whereNotNull('person_id')->pluck('person_id')->filter()->toArray();

        $deletedSantriRoles = PersonRole::where('role_type', 'santri')->delete();
        $this->line("✔ Table [person_roles]: {$deletedSantriRoles} role santri dihapus.");

        $deletedPersons = Person::whereNotIn('id', $userPersonIds)->delete();
        $this->line("✔ Table [persons]: {$deletedPersons} data santri/non-user dihapus. (" . count($userPersonIds) . " data pengurus/user tetap dipertahankan).");

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        $this->info("\n=== PEMBERSIHAN DUMMY SELESAI SUKSES ===");
        $this->info("Sistem sekarang 100% bersih dan siap diisi data Excel setup pondok Anda!");

        return Command::SUCCESS;
    }
}
