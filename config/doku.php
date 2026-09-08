<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DOKU Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi integrasi DOKU Checkout V2 (Jokul).
    | Nilai dapat dikonfigurasi via .env atau ditimpa secara dinamis melalui
    | Pengaturan Developer & Testing System di database (LandingPageContent).
    |
    */

    'is_enabled' => env('DOKU_ENABLED', true),

    'environment' => env('DOKU_ENVIRONMENT', 'sandbox'), // 'sandbox' atau 'production'

    'client_id' => env('DOKU_CLIENT_ID', 'MCH-DOKU-TEST-001'),

    'secret_key' => env('DOKU_SECRET_KEY', 'SK-DOKU-SECRET-KEY-001'),

    'expiry_minutes' => (int) env('DOKU_EXPIRY_MINUTES', 1440), // 24 jam

    'base_url' => [
        'sandbox'    => 'https://api-sandbox.doku.com',
        'production' => 'https://api.doku.com',
    ],

    'notification_url' => env('DOKU_NOTIFICATION_URL', null), // null = auto-route

    'return_url' => env('DOKU_RETURN_URL', null),
];
