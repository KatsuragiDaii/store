<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div data-animate="fade-down" class="sm:mx-auto sm:w-full sm:max-w-xl text-center mb-8">
        <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 hover:text-black transition">&larr; Dashboard Admin</a>
        <h2 class="mt-4 text-xl font-black tracking-widest uppercase text-gray-900">
            Tambah Produk Baru
        </h2>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-xl">
        <div data-animate="fade-up" class="bg-white py-8 px-6 shadow-xl shadow-black/5 sm:rounded-2xl sm:px-10 border border-gray-100">
            
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Nama Produk & Harga (Sebaris) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Nama Produk</label>
                        <input name="name" type="text" required placeholder="Ex: LX Handprint Tee" class="mt-2 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-black focus:border-black sm:text-sm transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Harga (Rp)</label>
                        <input name="price" type="number" required placeholder="350000" class="mt-2 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-black focus:border-black sm:text-sm transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Harga Diskon (Opsional)</label>
                        <input type="number" name="discount_price" placeholder="Contoh: 250000" class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-xs">
                    <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase">*Kosongkan jika produk tidak sedang promo/sale.</p>
                    </div>
                </div>

                <!-- Kategori Gender -->
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Kategori Gender</label>
                    <select name="gender" required class="mt-2 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm text-gray-700 focus:outline-none focus:ring-black focus:border-black sm:text-sm transition">
                        <option value="Unisex">Unisex (Pria & Wanita)</option>
                        <option value="Men">Men (Pria Saja)</option>
                        <option value="Women">Women (Wanita Saja)</option>
                    </select>
                </div>

                <!-- Material / Bahan -->
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Material / Detail Bahan</label>
                    <textarea name="material" rows="3" placeholder="Ex: 100% Cotton Combed 24s. Halus dan menyerap keringat." class="mt-2 appearance-none block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-black focus:border-black sm:text-sm transition"></textarea>
                </div>

                <!-- Pilihan Ukuran + Stok per Ukuran -->
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase mb-3">Ukuran & Stok Tersedia</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['S', 'M', 'L', 'XL'] as $sizeOption)
                        <div class="border border-gray-200 rounded-md p-3">
                            <label class="flex items-center space-x-2 cursor-pointer mb-2">
                                <input type="checkbox" name="sizes[]" value="{{ $sizeOption }}" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                <span class="text-sm font-bold">{{ $sizeOption }}</span>
                            </label>
                            <input type="number" name="stock[{{ $sizeOption }}]" min="0" value="0" placeholder="Stok" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-black focus:border-black">
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase">*Centang ukurannya, lalu isi jumlah stok. Ukuran yang tidak dicentang diabaikan.</p>
                </div>

                <!-- Input Foto (Bisa Pilih Banyak Foto) -->
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Foto Produk (Bisa Pilih Lebih Dari 1)</label>
                    <!-- Perhatikan atribut name="images[]" dan multiple -->
                    <input name="images[]" type="file" multiple required accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:tracking-widest file:uppercase file:bg-gray-100 file:text-black hover:file:bg-gray-200 transition cursor-pointer border border-gray-300 rounded-md p-1">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-xs tracking-[0.2em] font-bold uppercase text-white bg-black hover:bg-gray-800 focus:outline-none transition duration-300">
                        Simpan ke Katalog
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>