<?php

return [
    // Ambil dari Midtrans Dashboard > Settings > Access Keys.
    // Pakai key SANDBOX dulu buat development/testing.
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // false = sandbox (default, aman buat development). Ganti ke true di .env
    // production setelah toko benar-benar siap terima pembayaran asli.
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
];
