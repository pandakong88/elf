<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Notification via Fonnte
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pengiriman notifikasi WhatsApp ke grup admin/bendahara
    | menggunakan Fonnte API (fonnte.com).
    |
    | Syarat:
    | - Daftar akun di fonnte.com
    | - Hubungkan HP via QR di dashboard Fonnte
    | - Salin token API ke FONNTE_TOKEN di .env
    | - Salin nomor target grup ke FONNTE_GROUP_TARGET di .env
    |   Format nomor grup: "628xxxx@g.us" (dapat dicek di dashboard Fonnte)
    |
    */

    'enabled'         => env('FONNTE_ENABLED', false),
    'token'           => env('FONNTE_TOKEN', ''),
    'target'          => env('FONNTE_GROUP_TARGET', ''),
    'api_url'         => 'https://api.fonnte.com/send',

    /*
    |--------------------------------------------------------------------------
    | Notifikasi Aktif
    |--------------------------------------------------------------------------
    | Kontrol notifikasi mana saja yang dikirim.
    */
    'notify_gateway'  => env('FONNTE_NOTIFY_GATEWAY', true),
    'notify_kasir'    => env('FONNTE_NOTIFY_KASIR', true),

];
