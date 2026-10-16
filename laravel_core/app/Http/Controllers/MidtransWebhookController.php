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
            // Pastikan nominal yang dibayar sama dengan total pesanan. Signature
            // sudah menjamin payload asli dari Midtrans; pengecekan ini menjaga
            // dari pesanan yang tertandai lunas padahal nominalnya tidak cocok.
            $paidAmount = (int) round((float) ($payload['gross_amount'] ?? 0));

            if ($paidAmount !== (int) $order->total) {
                Log::error('Midtrans webhook: nominal tidak cocok dengan total pesanan', [
                    'order_id' => $order->order_number,
                    'gross_amount' => $payload['gross_amount'] ?? null,
                    'order_total' => $order->total,
                ]);
                // Balas 200 supaya Midtrans tidak retry terus; kasus ini perlu dicek manual.
                return response()->json(['message' => 'Nominal tidak cocok, tidak diproses']);
            }

            $order->markAsPaid($payload['payment_type'] ?? null);
            return response()->json(['message' => 'OK']);
        }

        // 5. Update status lainnya (Cancel, Expire, dll) dengan Database Transaction
        DB::transaction(function () use ($order, $newStatus) {
            // Kunci baris order di dalam transaksi supaya kalau Midtrans kirim
            // notifikasi yang sama dua kali hampir bersamaan (retry), cuma
            // satu yang benar-benar memproses perubahan status.
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            $previous = $locked->status;

            if ($previous === $newStatus) {
                return; // Sudah diproses sebelumnya.
            }

            // Jangan biarkan status mundur ke "pending" kalau order sudah
            // pernah lebih maju dari itu (mis. notifikasi datang tidak urut).
            if ($newStatus === OrderStatus::Pending && $previous !== OrderStatus::Pending) {
                return;
            }

            // Pesanan yang sudah dikirim atau selesai tidak boleh dibatalkan /
            // dikedaluwarsakan oleh notifikasi pembayaran yang telat datang.
            if (in_array($previous, [OrderStatus::Shipped, OrderStatus::Completed])) {
                return;
            }

            // Update status pesanan
            $locked->update(['status' => $newStatus]);

            // Jika pesanan batal atau expired, kembalikan stok produk, TAPI hanya
            // kalau stoknya memang pernah dikurangi. Stok baru dikurangi saat
            // pesanan dibayar (Order::markAsPaid, yang mengisi paid_at), jadi
            // pesanan yang kedaluwarsa sebelum dibayar tidak boleh menambah stok.
            // Status sebelumnya Cancelled/Expired berarti stok sudah dikembalikan.
            if (
                in_array($newStatus, [OrderStatus::Cancelled, OrderStatus::Expired])
                && $locked->paid_at !== null
                && ! in_array($previous, [OrderStatus::Cancelled, OrderStatus::Expired])
            ) {
                $locked->incrementStock();
            }
        });

        return response()->json(['message' => 'OK']);
    }
}