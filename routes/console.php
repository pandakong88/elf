<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-sync transaksi Duitku yang pending setiap 5 menit
Schedule::command('keuangan:sync-gateway-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

