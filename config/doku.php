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
    | Kategori Channel Pembayaran DOKU & Skema Biaya Layanan (MDR)
    |--------------------------------------------------------------------------
    */
    'channel_categories' => [
        'va' => [
            'key'           => 'va',
            'name'          => 'Virtual Account (Transfer Bank)',
            'description'   => 'BCA, Mandiri, BRI, BNI',
            'icon'          => 'bank',
            'fee_type'      => 'fixed',
            'fee_amount'    => 3500,
            'fee_label'     => 'Rp 3.500',
            'payment_types' => [
                'VIRTUAL_ACCOUNT_BCA',
                'VIRTUAL_ACCOUNT_MANDIRI',
                'VIRTUAL_ACCOUNT_BRI',
                'VIRTUAL_ACCOUNT_BNI',
            ],
        ],
        'minimarket' => [
            'key'           => 'minimarket',
            'name'          => 'Gerai Retail / Minimarket',
            'description'   => 'Alfamart Group & Indomaret',
            'icon'          => 'store',
            'fee_type'      => 'fixed',
            'fee_amount'    => 4000,
            'fee_label'     => 'Rp 4.000',
            'payment_types' => [
                'ONLINE_TO_OFFLINE_ALFA',
                'ONLINE_TO_OFFLINE_INDOMARET',
            ],
        ],
        'ewallet' => [
            'key'           => 'ewallet',
            'name'          => 'E-Wallet',
            'description'   => 'DANA, ShopeePay, OVO, LinkAja',
            'icon'          => 'wallet',
            'fee_type'      => 'percentage',
            'fee_amount'    => 0.015,
            'min_fee'       => 2000,
            'fee_label'     => '1,5% (min. Rp 2.000)',
            'payment_types' => [
                'EMONEY_DANA',
                'EMONEY_SHOPEEPAY',
                'EMONEY_OVO',
                'EMONEY_LINKAJA',
            ],
        ],
        'qris' => [
            'key'           => 'qris',
            'name'          => 'QRIS (Semua Bank & E-Wallet)',
            'description'   => 'Scan QRIS via BCA Mobile, Livin, BRImo, DANA, GoPay, dll.',
            'icon'          => 'qrcode',
            'fee_type'      => 'percentage',
            'fee_amount'    => 0.007,
            'min_fee'       => 0,
            'fee_label'     => '0,7%',
            'payment_types' => [
                'QRIS',
            ],
        ],
    ],
];
