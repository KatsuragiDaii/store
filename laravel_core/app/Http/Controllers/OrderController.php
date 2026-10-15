<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 1. Tampilkan halaman checkout: form alamat + ringkasan cart
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        
        // Ambil alamat utama user untuk auto-fill di form checkout
        $primaryAddress = auth()->user()->addresses()->where('is_primary', true)->first();

        return view('checkout', [
            'title' => 'CHECKOUT',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shippingCost' => $this->estimateShippingCost(),
            'primaryAddress' => $primaryAddress,
        ]);
    }

    // 2. Proses checkout: validasi, buat Order + OrderItem, minta Snap Token
    public function store(Request $request, MidtransService $midtrans)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_province' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        $validatedCartItems = [];

        // Cek ulang stok dan hitung ulang subtotal langsung dari database untuk mencegah manipulasi
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);

            if (! $product) {
                return back()->withErrors([
                    'stock' => "Produk \"{$item['name']}\" sudah tidak tersedia."
                ])->withInput();
            }

            if ($product->tracksStock() && $product->stockFor($item['size']) < $item['quantity']) {
                return back()->withErrors([
                    'stock' => "Stok \"{$product->name}\" ukuran {$item['size']} tidak mencukupi. Cek kembali keranjangmu.",
                ])->withInput();
            }

            // Tentukan harga yang berlaku saat ini (diskon atau normal) dari database
            $currentPrice = $product->discount_price ?: $product->price;
            $subtotal += $currentPrice * $item['quantity'];

            $validatedCartItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => (is_array($product->images) && count($product->images) > 0) ? $product->images[0] : '',
                'size' => $item['size'],
                'price' => $currentPrice,
                'quantity' => $item['quantity'],
            ];
        }

        $shippingCost = $this->estimateShippingCost();

        $order = DB::transaction(function () use ($request, $validatedCartItems, $subtotal, $shippingCost) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => \App\Enums\OrderStatus::Pending,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_province' => $request->shipping_province,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_postal_code' => $request->shipping_postal_code,
                'notes' => $request->notes,
            ]);

            foreach ($validatedCartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order;
        });

        // Minta Snap Token ke Midtrans
        try {
            $snapToken = $midtrans->createSnapToken($order->load('items', 'user'));
            
            // Simpan token ke database
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // Kosongkan keranjang
        session()->forget('cart');

        // Arahkan ke halaman detail pesanan untuk memunculkan pop-up
        return redirect()->route('orders.show', $order);
    }

    // 3. Riwayat pesanan milik user yang sedang login
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('orders.index', [
            'title' => 'PESANAN SAYA',
            'orders' => $orders,
        ]);
    }

    // 4. Detail + status satu pesanan (menampilkan tombol bayar kalau masih pending)
    public function show(Order $order)
    {
        // Cek kepemilikan
        abort_unless($order->user_id === auth()->id(), 403);

        return view('orders.show', [
            'title' => 'PESANAN '.$order->order_number,
            'order' => $order->load('items'),
            'midtransClientKey' => config('midtrans.client_key'),
            'midtransIsProduction' => config('midtrans.is_production'),
        ]);
    }

    /**
     * Ongkir gratis untuk sekarang (pengiriman ditangani manual/offline oleh toko).
     */
    protected function estimateShippingCost(): int
    {
        return 0;
    }
}