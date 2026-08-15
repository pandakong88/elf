<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Duitku Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Duitku Payment Gateway.
    | Isi nilai merchant_code dan api_key melalui .env.
    |
    */

    'merchant_code'    => env('DUITKU_MERCHANT_CODE', ''),
    'api_key'          => env('DUITKU_API_KEY', ''),
    'env'              => env('DUITKU_ENV', 'sandbox'), // 'sandbox' atau 'production'
    'expiry_minutes'   => (int) env('DUITKU_EXPIRY_MINUTES', 1440),
    'callback_url'     => env('DUITKU_CALLBACK_URL', ''),
    'return_url'       => env('DUITKU_RETURN_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */
    'base_url' => [
        'sandbox'    => 'https://sandbox.duitku.com/webapi/api',
        'production' => 'https://passport.duitku.com/webapi/api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Channel Pembayaran yang Diaktifkan
    |--------------------------------------------------------------------------
    |
    | Daftar kode channel Duitku yang dibuka untuk wali santri.
    | Kode referensi: https://docs.duitku.com/api/id/#payment-method
    |
    | SP  = QRIS (semua e-wallet, scan satu QR)
    | BR  = VA BRI
    | BT  = VA BSI (Bank Syariah Indonesia)
    | I1  = VA BNI
    | M2  = VA Mandiri (via ATM / Livin)
    | FT  = Minimarket (Alfamart/Indomaret) — opsional
    |
    */
    'enabled_channels' => [
        'SP' => [
            'name'       => 'QRIS',
            'icon'       => 'qris',
            'mdr_rate'   => 0.007,   // 0.7% — rata-rata MDR QRIS Duitku
            'mdr_fixed'  => 0,
            'min_amount' => 1000,
            'max_amount' => 5000000,
        ],
        'BR' => [
            'name'       => 'Virtual Account BRI',
            'icon'       => 'bri',
            'mdr_rate'   => 0,
            'mdr_fixed'  => 4000,    // Rp 4.000 flat per transaksi
            'min_amount' => 10000,
            'max_amount' => 100000000,
        ],
        'BT' => [
            'name'       => 'Virtual Account BSI',
            'icon'       => 'bsi',
            'mdr_rate'   => 0,
            'mdr_fixed'  => 4000,
            'min_amount' => 10000,
            'max_amount' => 100000000,
        ],
        'I1' => [
            'name'       => 'Virtual Account BNI',
            'icon'       => 'bni',
            'mdr_rate'   => 0,
            'mdr_fixed'  => 4000,
            'min_amount' => 10000,
            'max_amount' => 100000000,
        ],
        'M2' => [
            'name'       => 'Virtual Account Mandiri',
            'icon'       => 'mandiri',
            'mdr_rate'   => 0,
            'mdr_fixed'  => 4000,
            'min_amount' => 10000,
            'max_amount' => 100000000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | MDR — Siapa yang menanggung?
    |--------------------------------------------------------------------------
    |
    | Sesuai keputusan: MDR ditanggung wali santri.
    | Artinya: total_yang_dibayar_wali = nominal_tagihan + mdr
    |
    */
    'mdr_bearer' => 'customer', // 'customer' | 'merchant'

];
