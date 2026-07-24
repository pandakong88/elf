<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$dataDir = __DIR__ . '/database/seeders/data';
if (!File::exists($dataDir)) {
    File::makeDirectory($dataDir, 0755, true);
}

// Tables to export in exact snapshot (excluding framework transient tables like sessions, cache, migrations, activity_log)
$tablesToExport = [
    'roles',
    'permissions',
    'model_has_roles',
    'role_has_permissions',
    'organizations',
    'positions',
    'persons',
    'person_roles',
    'users',
    'dormitories',
    'rooms',
    'room_assignments',
    'madrasah_kelas',
    'madrasah_enrollments',
    'santri_profiles',
    'santri_status_logs',
    'master_data',
    'census_templates',
    'census_template_fields',
    'census_campaign_dormitories',
    'activities',
    'landing_page_contents',
    'billing_configurations',
    'majek_periods',
    'majek_registrations',
    'bills',
    'bill_payments',
    'workflow_templates',
    'workflow_steps',
];

$manifest = [];

foreach ($tablesToExport as $table) {
    if (!DB::getSchemaBuilder()->hasTable($table)) {
        continue;
    }

    $rows = DB::table($table)->get()->map(function ($row) {
        return (array) $row;
    })->toArray();

    if (empty($rows)) {
        continue;
    }

    $jsonFile = $dataDir . '/' . $table . '.json';
    File::put($jsonFile, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $manifest[] = [
        'table' => $table,
        'count' => count($rows),
        'file'  => $table . '.json',
    ];

    echo "Exported " . str_pad($table, 30) . " : " . count($rows) . " rows" . PHP_EOL;
}

File::put($dataDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo PHP_EOL . "SUCCESS! Exported " . count($manifest) . " tables to database/seeders/data/" . PHP_EOL;
