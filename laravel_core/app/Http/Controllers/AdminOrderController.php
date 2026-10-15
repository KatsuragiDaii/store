<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
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
            $order->update([
                'courier' => $request->courier,
                'courier_service' => $request->courier_service,
                'tracking_number' => $request->tracking_number,
                'shipped_at' => $newStatus === OrderStatus::Shipped ? ($order->shipped_at ?? now()) : $order->shipped_at,
            ]);
        }

        if ($newStatus === OrderStatus::Completed) {
            $order->update(['delivered_at' => $order->delivered_at ?? now()]);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
