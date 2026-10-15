<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // 🌟 1. Tampilkan Halaman Keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('cart', [
            'title' => 'YOUR CART',
            'cart' => $cart,
            'subtotal' => $subtotal
        ]);
    }

    // 🌟 2. Tambah ke Keranjang
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        // Tangkap ukuran dan jumlah dari form, beri nilai default jika kosong
        $size = $request->size ?? 'S';
        $quantity = (int) ($request->quantity ?? 1);

        // Gunakan harga diskon jika ada, jika tidak gunakan harga normal
        $price = $product->discount_price ? $product->discount_price : $product->price;

        // Buat ID unik untuk item di keranjang (kombinasi ID produk + Ukuran)
        $cartKey = $id . '-' . $size;

        // 🌟 Cek stok sebelum menambahkan. Ini baru penjagaan di level cart —
        // begitu alur checkout & order dibuat, pengurangan stok sesungguhnya
        // harus dikunci pakai lockForUpdate() saat pembayaran berhasil, supaya
        // dua pembeli tidak bisa checkout stok terakhir yang sama secara bersamaan.
        $existingQty = $cart[$cartKey]['quantity'] ?? 0;
        $availableStock = $product->stockFor($size);

        if ($product->tracksStock() && $existingQty + $quantity > $availableStock) {
            $sisa = max(0, $availableStock - $existingQty);
            return back()->withErrors([
                'stock' => $sisa > 0
                    ? "Stok ukuran {$size} tinggal {$sisa}."
                    : "Stok ukuran {$size} sudah habis.",
            ]);
        }

        if (isset($cart[$cartKey])) {
            // Jika produk dengan ukuran yang sama sudah ada, tambahkan jumlahnya
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Jika belum ada, buat item baru di keranjang
            $cart[$cartKey] = [
                'product_id' => $id,
                'name' => $product->name,
                'price' => $price,
                'size' => $size,
                'quantity' => $quantity,
                'image' => (is_array($product->images) && count($product->images) > 0) ? $product->images[0] : ''
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index'); // Langsung bawa ke halaman keranjang
    }

    // 🌟 3. Hapus 1 Item dari Keranjang
    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart', $cart);
        }
        return back();
    }

    // 🌟 4. Kosongkan Semua Keranjang (Hapus Semua)
    public function clear()
    {
        session()->forget('cart');
        return back();
    }

    // 🌟 5. Update Jumlah (Qty) Item di Keranjang
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        $key = $request->cart_key;
        $quantity = $request->quantity;

        if (isset($cart[$key])) {
            // Jika quantity 0 atau kurang, langsung hapus dari keranjang
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                // Update ke jumlah yang baru
                $cart[$key]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }
        
        return back();
    }
}