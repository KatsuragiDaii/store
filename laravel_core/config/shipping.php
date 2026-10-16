<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ongkos Kirim & Pelacakan Resi (RajaOngkir / Komerce)
    |--------------------------------------------------------------------------
    |
    | Semua nilai dibaca lewat config() -- BUKAN env() langsung di kode -- supaya
    | tetap jalan walaupun kamu pernah menjalankan `php artisan config:cache`.
    |
    | Fitur ongkir otomatis MENYALA kalau RAJAONGKIR_API_KEY dan
    | SHIPPING_ORIGIN_ID sama-sama terisi. Kalau salah satunya kosong, checkout
    | kembali ke perilaku lama (ongkir gratis / ditangani toko secara manual),
    | jadi aman di-upload sebelum key-nya siap.
    |
    */

    // API key dari dashboard RajaOngkir (rajaongkir.com) -> menu API Keys.
    'api_key' => env('RAJAONGKIR_API_KEY'),

    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    // ID lokasi asal pengiriman (gudang/toko kamu), dalam sistem ID RajaOngkir.
    // Cara cari: login ke website, buka
    //   /shipping/destinations?q=NAMA-KECAMATAN-ATAU-KODE-POS
    // lalu salin angka "id" dari hasil yang cocok.
    'origin_id' => env('SHIPPING_ORIGIN_ID'),

    // Kode kurir yang ditawarkan di checkout, dipisah koma.
    // Contoh kode: jne, sicepat, jnt, pos, tiki, anteraja, ninja, lion, sap, ide
    'couriers' => array_values(array_filter(array_map(
        'trim',
        explode(',', strtolower((string) env('SHIPPING_COURIERS', 'jne,sicepat,jnt')))
    ))),

    // Nama tampilan kurir. Kode yang tidak ada di sini ditampilkan huruf besar.
    'courier_names' => [
        'jne'      => 'JNE',
        'sicepat'  => 'SiCepat',
        'jnt'      => 'J&T Express',
        'pos'      => 'POS Indonesia',
        'tiki'     => 'TIKI',
        'anteraja' => 'AnterAja',
        'ninja'    => 'Ninja Xpress',
        'lion'     => 'Lion Parcel',
        'sap'      => 'SAP Express',
        'ide'      => 'ID Express',
    ],

    // Berat bawaan produk (gram) kalau admin belum mengisinya.
    'default_weight' => (int) env('SHIPPING_DEFAULT_WEIGHT', 300),

    // Batas waktu tunggu tiap panggilan ke API (detik).
    'timeout' => (int) env('SHIPPING_API_TIMEOUT', 10),

    // Lama hasil panggilan API disimpan di cache (detik), supaya kuota API hemat.
    'cache' => [
        'destinations' => 86400, // pencarian lokasi: 24 jam
        'costs'        => 1800,  // ongkir: 30 menit
        'tracking'     => 600,   // lacak resi: 10 menit
    ],

];
