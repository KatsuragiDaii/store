<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $product->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F9F9F9] min-h-screen text-gray-900">
    @include('partials.navbar')

    <div class="max-w-3xl mx-auto px-6 py-12">
        <h1 data-animate="fade-down" class="text-2xl font-black uppercase tracking-widest text-black mb-8">EDIT PRODUK</h1>
        
        <div data-animate="fade-up" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Kategori (Type)</label>
                        <select name="type" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black uppercase">
                            <option value="MEN" {{ $product->type == 'MEN' ? 'selected' : '' }}>MEN</option>
                            <option value="WOMEN" {{ $product->type == 'WOMEN' ? 'selected' : '' }}>WOMEN</option>
                            <option value="UNISEX" {{ $product->type == 'UNISEX' ? 'selected' : '' }}>UNISEX</option>
                            <option value="COLLECTIONS" {{ $product->type == 'COLLECTIONS' ? 'selected' : '' }}>COLLECTIONS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Harga Diskon (Kosongkan jika tidak diskon)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black">
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Berat per Item (gram) - untuk hitung ongkir</label>
                    <input type="number" name="weight" min="1" max="50000" value="{{ old('weight', $product->weight ?? config('shipping.default_weight')) }}" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black">
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Material / Deskripsi</label>
                    <textarea name="material" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-black">{{ old('material', $product->material) }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Tambah Foto Baru (Opsional)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-gray-50">
                    <p class="text-[9px] mt-2 text-gray-400">Foto baru akan ditambahkan ke koleksi foto yang sudah ada.</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('admin.products.index') }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black">Batal</a>
                    <button type="submit" class="bg-black text-white px-8 py-4 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>