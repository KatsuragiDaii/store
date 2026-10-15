<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun Admin Toko
    |--------------------------------------------------------------------------
    |
    | Dipakai oleh `php artisan db:seed` (AdminUserSeeder) dan
    | `php artisan admin:create`. Nilainya dibaca lewat config() -- BUKAN env()
    | langsung di seeder -- supaya tetap jalan walaupun kamu pernah menjalankan
    | `php artisan config:cache` di hosting.
    |
    | TIDAK ada nilai bawaan untuk email dan password. Kalau kosong, admin tidak
    | dibuat. Dengan begitu tidak mungkin ada akun admin dengan password default
    | yang bisa ditebak orang.
    |
    */

    'admin' => [
        'name'     => env('ADMIN_NAME', 'Admin FROMHERE'),
        'email'    => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Verifikasi Email
    |--------------------------------------------------------------------------
    |
    | false (bawaan) = perilaku lama: daftar langsung bisa belanja.
    | true           = user baru harus klik link di emailnya dulu sebelum bisa
    |                  membuka akun, checkout, pesanan, wishlist, dan review.
    |
    | Nyalakan HANYA setelah pengiriman email (MAIL_* di .env) terbukti jalan.
    |
    | validate_email_dns: saat verifikasi menyala, form daftar juga menolak
    | email dengan domain yang tidak punya record email (mis. abc@asdfgh.xyz).
    | Set VALIDATE_EMAIL_DNS=false kalau hostingmu gagal melakukan cek DNS.
    |
    */

    'require_email_verification' => (bool) env('REQUIRE_EMAIL_VERIFICATION', false),
    'validate_email_dns'         => (bool) env('VALIDATE_EMAIL_DNS', true),

];
