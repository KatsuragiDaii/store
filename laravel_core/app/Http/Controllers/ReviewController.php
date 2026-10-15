<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // 1. Cek apakah user sudah beli produk ini dan statusnya 'Selesai' (Completed)
        $hasPurchased = $user->orders()
            ->where('status', OrderStatus::Completed) // Ganti sesuai nama Enum "Selesai" Anda
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })->exists();

        if (!$hasPurchased) {
            return back()->withErrors(['review' => 'Anda hanya bisa memberikan ulasan setelah pesanan produk ini selesai (diterima).']);
        }

        // 2. Cek apakah sudah pernah mengulas
        if ($product->reviews()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['review' => 'Anda sudah memberikan ulasan untuk produk ini.']);
        }

        // 3. Simpan Ulasan
        $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }
}