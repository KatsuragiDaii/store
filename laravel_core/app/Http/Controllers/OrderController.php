<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use App\Services\ShippingException;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 1. Tampilkan halaman checkout: form alamat + ringkasan cart
    public function checkout(Request $request, ShippingService $shipping)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        // Ambil alamat utama user untuk auto-fill di form checkout
        $primaryAddress = auth()->user()->addresses()->where('is_primary', true)->first();

        // Ongkir otomatis aktif kalau RAJAONGKIR_API_KEY & SHIPPING_ORIGIN_ID terisi.
        // Kalau tidak, checkout berjalan seperti sebelumnya (ongkir gratis/manual).
        $shippingEnabled = $shipping->isConfigured();

        return view('checkout', [
            'title' => 'CHECKOUT',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shippingCost' => $this->estimateShippingCost(),
            'primaryAddress' => $primaryAddress,
            'shippingEnabled' => $shippingEnabled,
            'cartWeight' => $shippingEnabled ? $shipping->cartWeight($cart) : null,
        ]);
    }

    // 2. Proses checkout: validasi, buat Order + OrderItem, minta Snap Token
    public function store(Request $request, MidtransService $midtrans, ShippingService $shipping)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $shippingEnabled = $shipping->isConfigured();

        $rules = [
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_province' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_district' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ];

        if ($shippingEnabled) {
            // Wilayah (provinsi/kota/kecamatan/kode pos) diambil server dari lokasi
            // yang dipilih pembeli, jadi isian teksnya tidak dipercaya & tidak wajib.
            foreach (['shipping_province', 'shipping_city', 'shipping_district', 'shipping_postal_code'] as $field) {
                $rules[$field] = 'nullable|string|max:255';
            }

            $rules['shipping_destination_id'] = 'required|integer|min:1';
            $rules['shipping_courier'] = 'required|string|max:50';
            $rules['shipping_service'] = 'required|string|max:100';
        }

        $request->validate($rules, [
            'shipping_destination_id.required' => 'Cari dan pilih lokasi tujuan (kecamatan / kode pos) dulu.',
            'shipping_courier.required' => 'Pilih layanan pengiriman dulu.',
            'shipping_service.required' => 'Pilih layanan pengiriman dulu.',
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

        // Ongkir. Dengan ongkir otomatis, angkanya SELALU dihitung ulang di server
        // dari API (lokasi + berat + kurir + layanan), apa pun yang dikirim browser.
        $shippingData = [];

        if ($shippingEnabled) {
            $destination = $shipping->destinationById((int) $request->shipping_destination_id);

            if (! $destination) {
                return back()->withErrors([
                    'shipping' => 'Lokasi tujuan perlu dipilih ulang. Cari kembali kecamatan atau kode pos kamu.',
                ])->withInput();
            }

            $weight = $shipping->cartWeight($cart);

            try {
                $option = $shipping->quote($destination['id'], $weight, $request->shipping_courier, $request->shipping_service);
            } catch (ShippingException $e) {
                return back()->withErrors(['shipping' => $e->getMessage()])->withInput();
            }

            if (! $option) {
                return back()->withErrors([
                    'shipping' => 'Layanan pengiriman yang dipilih tidak tersedia untuk tujuan ini. Silakan pilih ulang.',
                ])->withInput();
            }

            $shippingCost = $option['cost'];

            $shippingData = [
                'courier' => $option['courier'],
                'courier_service' => $option['service'],
                'shipping_etd' => $option['etd'] !== '' ? $option['etd'] : null,
                'shipping_destination_id' => $destination['id'],
                'total_weight' => $weight,
                // Alamat wilayah dari data lokasi yang sah, bukan dari isian form.
                'shipping_province' => $destination['province'],
                'shipping_city' => $destination['city'],
                'shipping_district' => $destination['district'],
                'shipping_postal_code' => $destination['zip'],
            ];
        } else {
            $shippingCost = $this->estimateShippingCost();
        }

        $order = DB::transaction(function () use ($request, $validatedCartItems, $subtotal, $shippingCost, $shippingData) {
            $order = Order::create(array_merge([
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
            ], $shippingData));

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
    public function show(Order $order, ShippingService $shipping)
    {
        // Cek kepemilikan
        abort_unless($order->user_id === auth()->id(), 403);

        return view('orders.show', [
            'title' => 'PESANAN '.$order->order_number,
            'order' => $order->load('items'),
            'midtransClientKey' => config('midtrans.client_key'),
            'midtransIsProduction' => config('midtrans.is_production'),
            // Tombol "Lacak Paket" hanya muncul kalau ada resi & kurirnya bisa dilacak.
            'canTrack' => filled($order->tracking_number) && $shipping->canTrackCourier($order->courier),
        ]);
    }

    // 5. Riwayat perjalanan paket (JSON, dipanggil tombol "Lacak Paket")
    public function tracking(Order $order, ShippingService $shipping): JsonResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (blank($order->tracking_number) || blank($order->courier)) {
            return response()->json(['message' => 'Nomor resi belum tersedia. Pesananmu belum diserahkan ke kurir.'], 404);
        }

        if (! $shipping->canTrackCourier($order->courier)) {
            return response()->json([
                'message' => 'Pelacakan otomatis belum tersedia untuk kurir ini. Gunakan nomor resi di situs atau aplikasi kurir.',
            ], 422);
        }

        try {
            $result = $shipping->track($order->courier, $order->tracking_number, $order->recipient_phone);
        } catch (ShippingException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        if ($result === null) {
            return response()->json([
                'message' => 'Data pelacakan belum tersedia. Biasanya baru muncul beberapa jam setelah paket diserahkan ke kurir.',
            ], 404);
        }

        return response()->json(['data' => $result]);
    }

    /**
     * Ongkir bawaan kalau ongkir otomatis belum diaktifkan: gratis
     * (pengiriman ditangani manual/offline oleh toko).
     */
    protected function estimateShippingCost(): int
    {
        return 0;
    }
}
