<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // Menggunakan trait SoftDeletes agar fitur "Hapus" tidak merusak database riwayat pesanan
    use HasFactory, SoftDeletes;

    // Mengizinkan semua kolom diisi secara massal (mass assignment)
    protected $guarded = [];

    // Casts: Laravel otomatis mengubah format JSON/String dari database menjadi tipe data asli (Array)
    protected $casts = [
        'images' => 'array',
        'sizes'  => 'array',
        'stock'  => 'array', // Asumsi: stok disimpan dalam JSON, misal {"S": 10, "M": 5, "L": 0}
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke tabel reviews (Satu produk bisa punya banyak ulasan)
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ke tabel order_items (Histori produk ini pernah dibeli di pesanan mana saja)
     */
    public function orderItems()
    {
        // Relasi ini penting agar jika dibutuhkan, kita bisa melacak penjualan per produk
        // (Pastikan Anda sudah memiliki class OrderItem.php)
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS (DIPANGGIL DI VIEW / FRONTEND)
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung rata-rata bintang dari ulasan
     */
    public function averageRating()
    {
        // Akan mereturn rata-rata, jika belum ada ulasan, kembalikan nilai 0
        return $this->reviews()->avg('rating') ?: 0;
    }

    /**
     * Cek apakah produk ini menggunakan sistem pelacakan stok
     */
    public function tracksStock()
    {
        // Mengembalikan nilai true jika properti 'stock' tidak null di database
        return !is_null($this->stock);
    }

    /**
     * Ambil sisa stok untuk ukuran tertentu (Digunakan saat user memilih size di halaman detail)
     */
    public function stockFor($size)
    {
        // Cek apakah data stock berbentuk array dan memiliki size yang dicari
        if (is_array($this->stock) && array_key_exists($size, $this->stock)) {
            return (int) $this->stock[$size];
        }
        
        // Jika tidak ada data spesifik (atau belum diset), asumsikan stok habis (0)
        return 0; 
    }
}