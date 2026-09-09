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

    /*
    |--------------------------------------------------------------------------
    | Biaya Layanan (Admin Gateway) & Saluran yang Diaktifkan
    |--------------------------------------------------------------------------
    */
    'admin_fee' => (float) env('DOKU_ADMIN_FEE', 3500),

    'allowed_payment_types' => [
        'VIRTUAL_ACCOUNT_BCA',
        'VIRTUAL_ACCOUNT_MANDIRI',
        'VIRTUAL_ACCOUNT_BRI',
        'VIRTUAL_ACCOUNT_BNI',
        'ONLINE_TO_OFFLINE_ALFA',
        'ONLINE_TO_OFFLINE_INDOMARET',
        'EMONEY_DANA',
        'EMONEY_SHOPEEPAY',
        'EMONEY_OVO',
        'EMONEY_LINKAJA',
        'QRIS',
    ],
];
