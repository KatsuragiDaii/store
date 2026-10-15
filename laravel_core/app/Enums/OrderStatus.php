<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';         // Order dibuat, menunggu pembayaran
    case Paid = 'paid';               // Pembayaran sukses (dari webhook Midtrans)
    case Processing = 'processing';   // Sedang dikemas/disiapkan
    case Shipped = 'shipped';         // Sudah diserahkan ke kurir
    case Completed = 'completed';     // Sudah diterima pembeli
    case Cancelled = 'cancelled';     // Dibatalkan oleh pembeli/admin
    case Expired = 'expired';         // Batas waktu bayar habis, tidak dibayar
    case Refunded = 'refunded';       // Dana dikembalikan

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu pembayaran',
            self::Paid => 'Sudah dibayar',
            self::Processing => 'Diproses',
            self::Shipped => 'Dikirim',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::Expired => 'Kedaluwarsa',
            self::Refunded => 'Dana dikembalikan',
        };
    }

    /**
     * Warna semantik buat badge status di halaman riwayat pesanan / panel admin.
     * Pakai nama warna generik (bukan kelas Tailwind) supaya bebas dipetakan
     * ke desain apa pun nantinya.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Paid, self::Processing => 'blue',
            self::Shipped => 'purple',
            self::Completed => 'green',
            self::Cancelled, self::Expired => 'gray',
            self::Refunded => 'red',
        };
    }
}
