<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Endpoint ini dipanggil server Midtrans, BUKAN oleh browser pembeli 
     * jadi tidak ada sesi/login/CSRF token. Satu-satunya yang membuktikan
     * notifikasi ini benar dari Midtrans adalah signature_key (lihat
     * MidtransService::isValidSignature). Jangan pernah percaya isi payload
     * sebelum signature-nya tervalidasi.
     */
    public function handle(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        // 1. Validasi Signature Key dari Midtrans
        if (! $midtrans->isValidSignature($payload)) {
            Log::warning('Midtrans webhook: signature tidak valid', [
                'order_id' => $payload['order_id'] ?? null,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari Order berdasarkan order_number
        $order = Order::where('order_number', $payload['order_id'] ?? null)->first();

        if (! $order) {
            Log::warning('Midtrans webhook: order tidak ditemukan', [
                'order_id' => $payload['order_id'] ?? null,
            ]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Petakan status Midtrans ke Enum OrderStatus kita
        $newStatus = $midtrans->mapStatus(
            $payload['transaction_status'] ?? '',
            $payload['fraud_status'] ?? null
        );

        if ($newStatus === null) {
            // Status yang belum perlu kita tangani (mis. capture yang masih
            // challenge fraud). Tetap balas 200 supaya Midtrans tidak retry.
            return response()->json(['message' => 'Status diabaikan, tidak ada perubahan']);
        }

        // 4. "Paid" lewat jalur khusus Order::markAsPaid()
        // Supaya pengurangan stok konsisten dengan jalur manual admin,
        // satu method, satu logika, tidak ada risiko dobel kurangi stok.
        if ($newStatus === OrderStatus::Paid) {
            $order->markAsPaid($payload['payment_type'] ?? null);
            return response()->json(['message' => 'OK']);
        }

        // 5. Update status lainnya (Cancel, Expire, dll) dengan Database Transaction
        DB::transaction(function () use ($order, $newStatus) {
            // Kunci baris order di dalam transaksi supaya kalau Midtrans kirim
            // notifikasi yang sama dua kali hampir bersamaan (retry), cuma
            // satu yang benar-benar memproses perubahan status.
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($locked->status === $newStatus) {
                return; // Sudah diproses sebelumnya.
            }

            // Jangan biarkan status mundur ke "pending" kalau order sudah
            // pernah lebih maju dari itu (mis. notifikasi datang tidak urut).
            if ($newStatus === OrderStatus::Pending && $locked->status !== OrderStatus::Pending) {
                return;
            }

            // Update status pesanan
            $locked->update(['status' => $newStatus]);

            // TAMBAHAN: Jika pesanan batal atau expired, kembalikan stok produk
            if (in_array($newStatus, [OrderStatus::Cancelled, OrderStatus::Expired])) {
                $locked->incrementStock();
            }
        });

        return response()->json(['message' => 'OK']);
    }
}