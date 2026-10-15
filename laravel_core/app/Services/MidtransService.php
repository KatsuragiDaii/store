<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true; // Bersihkan input kotor
        Config::$is3ds = true;       // Wajib untuk transaksi Kartu Kredit
    }

    public function createSnapToken(Order $order)
    {
        // 1. Siapkan detail transaksi
        $transaction_details = [
            'order_id' => $order->order_number, // Harus unik (misal: ORD-12345)
            'gross_amount' => (int) $order->total, // Total harga
        ];

        // 2. Siapkan detail barang (Opsional tapi direkomendasikan)
        $item_details = [];
        foreach ($order->items as $item) {
            $item_details[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => $item->product_name . ' (' . $item->size . ')',
            ];
        }

        // Tambahkan ongkos kirim sebagai item tambahan jika ada
        if ($order->shipping_cost > 0) {
            $item_details[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        // 3. Siapkan detail pelanggan
        $customer_details = [
            'first_name' => $order->recipient_name,
            'phone' => $order->recipient_phone,
            'email' => $order->user->email, // Email user yang sedang login
            'shipping_address' => [
                'first_name' => $order->recipient_name,
                'phone' => $order->recipient_phone,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'postal_code' => $order->shipping_postal_code,
            ],
        ];

        // 4. Bungkus semua parameter
        $params = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
        ];

        // 5. Minta Token ke Midtrans
        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            // Log error jika gagal
            report($e);
            return null;
        }
    }
}