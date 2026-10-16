<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatus::class,
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Isi otomatis kalau belum di-set manual, supaya tidak ada order yang
        // lolos tanpa order_number (dipakai juga sebagai order_id Midtrans).
        static::creating(function (Order $order) {
            $order->order_number ??= static::generateOrderNumber();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Contoh hasil: FH-20260814-9K3PQZ
     * Dicek dulu ke database supaya (walau sangat kecil kemungkinannya) tidak
     * pernah tabrakan dengan order_number yang sudah ada.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'FH-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Satu-satunya jalan resmi untuk menandai order "paid" — dipakai baik
     * oleh MidtransWebhookController (otomatis) maupun AdminOrderController
     * (manual, misal pembeli transfer langsung di luar Midtrans). Disatukan
     * di sini supaya stok TIDAK PERNAH bisa dikurangi dua kali atau malah
     * lupa dikurangi, apa pun jalur yang memicu status "paid".
     */
    public function markAsPaid(?string $paymentMethod = null): void
    {
        DB::transaction(function () use ($paymentMethod) {
            $locked = static::whereKey($this->id)->lockForUpdate()->first();

            // Sudah pernah diproses: statusnya Paid, atau sudah lanjut ke tahap
            // berikutnya (Diproses/Dikirim/Selesai) dan paid_at sudah terisi.
            // Jangan kurangi stok dua kali, dan jangan memundurkan status
            // pesanan yang sudah dikirim kalau notifikasi "settlement" Midtrans
            // datang lagi (retry / tidak berurutan).
            if ($locked->status === OrderStatus::Paid || $locked->paid_at !== null) {
                return;
            }

            $locked->update([
                'status' => OrderStatus::Paid,
                'payment_method' => $paymentMethod ?? $locked->payment_method,
                'paid_at' => $locked->paid_at ?? now(),
            ]);

            $locked->decrementStock();
        });

        $this->refresh();
    }

    public function incrementStock(): void
{
    foreach ($this->items as $item) {
        if (! $item->product_id) {
            continue;
        }
        $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
        if (! $product || ! $product->tracksStock()) {
            continue;
        }
        $stock = $product->stock;
        // Tambahkan kembali quantity ke stok yang ada
        $stock[$item->size] = ($stock[$item->size] ?? 0) + $item->quantity;
        $product->update(['stock' => $stock]);
    }
}

    /**
     * Kurangi stok tiap item di order ini, dengan lockForUpdate per produk
     * supaya aman kalau ada order lain untuk produk yang sama diproses
     * bersamaan. Produk lama yang belum tracksStock() sengaja dilewati.
     */
    public function decrementStock(): void
    {
        foreach ($this->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if (! $product || ! $product->tracksStock()) {
                continue;
            }

            $stock = $product->stock;
            $stock[$item->size] = max(0, ($stock[$item->size] ?? 0) - $item->quantity);
            $product->update(['stock' => $stock]);
        }
    }
}
