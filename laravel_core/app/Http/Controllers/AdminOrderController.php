<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    // 🌟 Daftar semua pesanan (semua user), bisa difilter lewat ?status=paid
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status') && OrderStatus::tryFrom($request->status)) {
            $query->where('status', $request->status);
        }

        return view('admin.orders.index', [
            'title' => 'KELOLA PESANAN',
            'orders' => $query->paginate(15)->withQueryString(),
            'statusFilter' => $request->status,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    // 🌟 Detail satu pesanan + form update status
    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'title' => 'PESANAN '.$order->order_number,
            'order' => $order->load(['items', 'user']),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    // 🌟 Proses update status (+ info kurir kalau diisi)
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'courier' => 'nullable|string|max:255',
            'courier_service' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $newStatus = OrderStatus::from($request->status);

        // "Paid" WAJIB lewat Order::markAsPaid() — itu satu-satunya tempat
        // yang boleh mengurangi stok, supaya tidak ada jalur lain yang bisa
        // bikin stok kurang dua kali atau malah tidak pernah dikurangi sama
        // sekali. Status lain diupdate langsung di sini.
        if ($newStatus === OrderStatus::Paid) {
            $order->markAsPaid();
        } else {
            $order->update(['status' => $newStatus]);
        }

        // Info kurir biasanya diisi bersamaan dengan menandai "shipped", tapi
        // dibiarkan bisa diisi terpisah kapan saja (mis. resi menyusul).
        if ($request->filled('courier') || $request->filled('tracking_number')) {
            // "J&T" / "JNE" yang diketik admin disimpan sebagai kode API ("jnt", "jne")
            // supaya pembeli bisa melacak paketnya. Teks bebas (mis. "Diantar Sendiri")
            // disimpan apa adanya.
            $courierCode = ShippingService::normalizeCourier($request->courier);
            $courier = array_key_exists($courierCode, config('shipping.courier_names'))
                ? $courierCode
                : $request->courier;

            $order->update([
                'courier' => $courier,
                'courier_service' => $request->courier_service,
                'tracking_number' => $request->tracking_number ? trim($request->tracking_number) : null,
            ]);
        }

        // Waktu kirim dicatat setiap kali status "Dikirim", walau kurir/resi
        // belum diisi (sebelumnya hanya tercatat kalau kurir/resi ikut diisi).
        if ($newStatus === OrderStatus::Shipped && $order->shipped_at === null) {
            $order->update(['shipped_at' => now()]);
        }

        if ($newStatus === OrderStatus::Completed) {
            $order->update(['delivered_at' => $order->delivered_at ?? now()]);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
