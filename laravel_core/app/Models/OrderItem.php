<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        // Jaring pengaman: kalau pemanggil lupa isi subtotal, hitung sendiri.
        static::creating(function (OrderItem $item) {
            $item->subtotal ??= $item->price * $item->quantity;
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
