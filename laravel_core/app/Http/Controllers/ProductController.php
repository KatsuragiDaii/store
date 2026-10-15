<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Collection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'price'     => 'required|numeric',
            'type'      => 'nullable|string',
            'gender'    => 'required|string', // 👈 Validasi gender
            'material'  => 'nullable|string',
            'sizes'     => 'nullable|array',
            'stock'     => 'nullable|array',
            'stock.*'   => 'nullable|integer|min:0',
            'images'    => 'required|array',
            'images.*'  => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $imageNames = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Disimpan langsung ke folder web (BUKAN storage disk), di
                // <web root>/storage/produk. Alamat ini sama dengan yang dibaca
                // semua view: asset('storage/produk/' . namafile).
                // Sengaja begini karena hosting shared seperti Hostinger paket
                // Single tidak mendukung symlink, jadi storage:link tidak bisa
                // dipakai di sana.
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move($this->productImageDir(), $imageName);
                $imageNames[] = $imageName;
            }
        }

        // 🌟 Susun stok per ukuran. Cuma simpan stok untuk ukuran yang memang
        // dicentang di field 'sizes', dan default ke 0 kalau angkanya dikosongkan.
        $stockData = [];
        foreach ($request->sizes ?? [] as $size) {
            $stockData[$size] = (int) ($request->input("stock.$size") ?? 0);
        }

        Product::create([
            'name'           => $request->name,
            'price'          => $request->price,
            'discount_price' => $request->discount_price, // 👈 Simpan diskon
            'type'           => $request->type ?? 'T-Shirt',
            'gender'         => $request->gender,
            'material'       => $request->material,
            'sizes'          => $request->sizes,
            'stock'          => $stockData,
            'images'         => $imageNames,
        ]);

        return redirect('/');
    }

    // 🌟 FUNGSI PEMBANTU: Folder tempat foto produk disimpan.
    // Harus folder yang benar-benar dilayani web server, karena view memanggilnya
    // lewat asset('storage/produk/...'). DOCUMENT_ROOT adalah web root yang dipakai
    // server (di Hostinger = public_html); kalau tidak tersedia, pakai public_path().
    private function productImageDir(): string
    {
        $root = (string) request()->server('DOCUMENT_ROOT', '');
        if ($root === '' || ! is_dir($root)) {
            $root = public_path();
        }

        return rtrim($root, '/\\') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'produk';
    }

// 🌟 FUNGSI PEMBANTU: Untuk menyaring dan mengurutkan data
private function getFilteredProducts($query, Request $request)
{
    // 1. Filter Pencarian (Search) <-- TAMBAHKAN BLOK INI
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('type', 'like', '%' . $search . '%');
        });
    }

    // 2. Filter Ukuran (Mencari di dalam kolom JSON)
    if ($request->filled('size')) {
        $sizes = $request->size;
        $query->where(function ($q) use ($sizes) {
            foreach ($sizes as $size) {
                $q->orWhereJsonContains('sizes', $size);
            }
        });
    }

    // 3. Sorting (Urutan)
    if ($request->filled('sort')) {
        if ($request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest(); // Newest
        }
    } else {
        $query->latest(); // Default Newest
    }

    return $query->get();
}

// 🌟 HALAMAN MEN
public function men(Request $request)
{
    // Ambil produk pria/unisex, lalu saring pakai fungsi pembantu di atas
    $query = Product::whereIn('gender', ['Men', 'Unisex']);
    $products = $this->getFilteredProducts($query, $request);

    return view('katalog', [
        'title' => "All Men's Products",
        'products' => $products
    ]);
}

// 🌟 HALAMAN WOMEN
public function women(Request $request)
{
    // Ambil produk wanita/unisex, lalu saring pakai fungsi pembantu di atas
    $query = Product::whereIn('gender', ['Women', 'Unisex']);
    $products = $this->getFilteredProducts($query, $request);

    return view('katalog', [
        'title' => "All Women's Products",
        'products' => $products
    ]);
}

    // Fungsi untuk menampilkan detail produk
    public function show($id)
{
    // Muat produk beserta ulasannya
    $product = Product::with(['reviews.user'])->findOrFail($id);
    
    $canReview = false;
    
    if (auth()->check()) {
        $user = auth()->user();
        
        // Cek riwayat pembelian yang sudah "Selesai"
        $hasPurchased = $user->orders()
            ->where('status', \App\Enums\OrderStatus::Completed)
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })->exists();
            
        // Cek apakah belum pernah di-review
        $alreadyReviewed = $product->reviews()->where('user_id', $user->id)->exists();
        
        $canReview = $hasPurchased && !$alreadyReviewed;
    }

    return view('show', compact('product', 'canReview'));
}

    // 🌟 HALAMAN COLLECTIONS
    public function collections()
    {
        $collections = Collection::latest()->get(); // 👈 Ambil dari database
        return view('collections', [
            'title' => "All Collections",
            'collections' => $collections
        ]);
    }

    // 🌟 HALAMAN SEMUA PRODUK (Atau Filter Koleksi Tertentu)
    public function products(Request $request)
    {
        $query = Product::query();
        $title = "All Products";
        
        // Secara default, Filter Tipe Produk DITAMPILKAN
        $hideTypeFilter = false;

        // JIKA sedang memfilter berdasarkan koleksi dari halaman Collections
        if ($request->has('collection')) {
            $hideTypeFilter = true; // Sembunyikan filter tipe karena sudah spesifik
            $slug = $request->collection;
            $query->where('type', 'like', '%' . $slug . '%');
            $koleksi = Collection::where('slug', $slug)->first();
            $title = $koleksi ? $koleksi->name : strtoupper($slug) . " COLLECTION";
        }
        
        // JIKA bukan dari koleksi (Klik Shop Now / View All), filter tipe akan AKTIF
        // Filter Type (Jika ada yang dicentang di sidebar)
        if ($request->filled('type')) {
            $query->whereIn('type', $request->type);
        }

        // Filter Size (Dari fungsi pembantu) & Sorting
        $products = $this->getFilteredProducts($query, $request);

        return view('katalog', [
            'title' => $title,
            'products' => $products,
            'hideTypeFilter' => $hideTypeFilter
        ]);
    }

    // 🌟 FUNGSI BARU: Halaman Form Tambah Koleksi
 public function createCollection()
 {
     return view('create-collection');
 }

 // 🌟 FUNGSI BARU: Proses Simpan Koleksi
 public function storeCollection(Request $request)
 {
     $request->validate([
         'name' => 'required',
         'slug' => 'required',
         'image' => 'required|image'
     ]);

     $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();  
     $request->image->move(public_path('images/collections'), $imageName);

     Collection::create([
         'name' => $request->name,
         'slug' => $request->slug, // Kata kunci pencarian (misal: T-Shirt)
         'image' => $imageName
     ]);

     return redirect('/collections');
 }

 // 🌟 HALAMAN SALE
 public function sale(Request $request)
 {
     // Ambil daftar tipe produk unik yang sudah ada di database (Otomatis!)
     $availableTypes = Product::whereNotNull('type')->distinct()->pluck('type');

     // Ambil produk yang nilai discount_price-nya tidak kosong (bukan null)
     $query = Product::whereNotNull('discount_price');

     // 1. Filter Gender (Men, Women, Unisex)
     if ($request->filled('gender')) {
         $genders = $request->gender;
         $query->where(function ($q) use ($genders) {
             foreach ($genders as $gender) {
                 $q->orWhere('gender', $gender)
                   ->orWhere('gender', 'Unisex');
             }
         });
     }

     // 2. Filter Product Type
     if ($request->filled('type')) {
         $query->whereIn('type', $request->type);
     }

     // 3. Sorting (Urutan)
     if ($request->filled('sort')) {
         if ($request->sort == 'price_asc') {
             $query->orderBy('discount_price', 'asc');
         } elseif ($request->sort == 'price_desc') {
             $query->orderBy('discount_price', 'desc');
         } else {
             $query->latest();
         }
     } else {
         $query->latest();
     }

     $products = $query->get();

     return view('sale', [
         'title' => "Sale",
         'products' => $products,
         'availableTypes' => $availableTypes // 👈 Kirim data tipe produk ke view
     ]);
 }
 
 // --- TAMBAHAN UNTUK FITUR ADMIN CRUD PRODUK ---

    // 1. Tampilkan daftar produk di tabel admin
    public function adminIndex()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    // 2. Tampilkan form edit
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    // 3. Proses Update ke Database
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'type' => 'required|string',
            'material' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
            // Sesuaikan validasi sizes/stock jika ada di sistem Anda
        ]);

        // Tangani Gambar (Jika admin upload gambar baru, tambahkan ke array lama)
        $imagePaths = is_array($product->images) ? $product->images : json_decode($product->images, true) ?? [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Sama seperti di store(): simpan langsung ke <web root>/storage/produk,
                // bukan storage disk, karena hosting shared (mis. Hostinger Single)
                // tidak mendukung symlink untuk storage:link.
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move($this->productImageDir(), $imageName);
                $imagePaths[] = $imageName;
            }
        }

        $product->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'type' => $validated['type'],
            'material' => $validated['material'],
            'images' => $imagePaths,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // 4. Proses Hapus
    // - Produk yang PERNAH dipesan: soft delete (baris tetap ada di database dengan
    //   deleted_at terisi), supaya riwayat pesanan dan foto di pesanan tetap aman.
    // - Produk yang BELUM pernah dipesan: dihapus permanen dari database beserta file fotonya.
    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            $product->delete();
            return back()->with('success', 'Produk disembunyikan dari katalog. Datanya tetap disimpan karena pernah dipesan.');
        }

        $images = is_array($product->images) ? $product->images : (json_decode($product->images, true) ?: []);
        $id = $product->id;

        $product->forceDelete(); // wishlist & review produk ini ikut terhapus (cascade)

        // Hapus file foto, kecuali kalau file yang sama masih dipakai produk lain
        foreach ($images as $name) {
            $name = basename((string) $name);
            $dipakaiLain = Product::withTrashed()
                ->where('id', '!=', $id)
                ->where('images', 'like', '%"' . $name . '"%')
                ->exists();

            if ($name !== '' && ! $dipakaiLain) {
                @unlink($this->productImageDir() . DIRECTORY_SEPARATOR . $name);
            }
        }

        return back()->with('success', 'Produk berhasil dihapus permanen.');
    }
    // --- FITUR MANAJEMEN STOK CEPAT ---

    // 5. Tampilkan Halaman Tabel Stok
    public function stockIndex()
    {
        // Ambil semua produk, urutkan dari yang terbaru
        $products = Product::latest()->get();
        return view('admin.products.stock', compact('products'));
    }

    // 6. Proses Update Stok Massal
    public function stockUpdate(Request $request)
    {
        $request->validate([
            'stocks' => 'required|array',
        ]);

        // Looping array inputan form
        // Format input: name="stocks[product_id][size]"
        foreach ($request->stocks as $productId => $stockData) {
            $product = Product::find($productId);
            
            if ($product) {
                // Pastikan nilai yang masuk adalah angka (integer)
                $formattedStock = array_map('intval', $stockData);
                
                // Update kolom 'stock' ke database
                $product->update([
                    'stock' => $formattedStock
                ]);
            }
        }

        return back()->with('success', 'Stok seluruh produk berhasil diperbarui secara instan.');
    }
}