<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class LocalSnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds from local snapshot data.
     */
    public function run(): void
    {
        $dataDir = database_path('seeders/data');
        $manifestPath = $dataDir . '/manifest.json';

        if (!File::exists($manifestPath)) {
            if (isset($this->command)) {
                $this->command->error("Manifest file not found at {$manifestPath}");
            }
            return;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (empty($manifest)) {
            if (isset($this->command)) {
                $this->command->error("Manifest file is empty!");
            }
            return;
        }

        Schema::disableForeignKeyConstraints();

        foreach ($manifest as $item) {
            $table = $item['table'];
            $file  = $dataDir . '/' . $item['file'];

            if (!File::exists($file)) {
                if (isset($this->command)) {
                    $this->command->warn("Data file for {$table} not found. Skipping.");
                }
                continue;
            }

            $rows = json_decode(File::get($file), true);
            if (empty($rows)) {
                continue;
            }

            // Truncate table first
            DB::table($table)->truncate();

            // Insert rows in chunks of 300
            $chunks = array_chunk($rows, 300);
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
            }

            if (isset($this->command)) {
                $this->command->info("Seeded {$table} : " . count($rows) . " rows");
            }
        }

        Schema::enableForeignKeyConstraints();

        // Forget cached permissions for Spatie
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        if (isset($this->command)) {
            $this->command->info("✅ LocalSnapshotSeeder completed successfully!");
        }
    }
}
