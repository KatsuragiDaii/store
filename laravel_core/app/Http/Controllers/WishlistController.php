<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Menampilkan halaman daftar wishlist
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('product')->latest()->get();
        return view('wishlist', [
            'title' => 'YOUR WISHLIST',
            'wishlists' => $wishlists
        ]);
    }

    // Menambah atau Menghapus dari wishlist (Toggle)
    public function toggle($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $id)->first();

        if ($wishlist) {
            $wishlist->delete(); // Jika sudah ada, hapus
            return back()->with('success', 'Produk dihapus dari wishlist.');
        } else {
            Wishlist::create([ // Jika belum ada, tambahkan
                'user_id' => Auth::id(),
                'product_id' => $id
            ]);
            return back()->with('success', 'Produk ditambahkan ke wishlist.');
        }
    }
}