<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale - Fromhere</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    <!-- 🌟 Navbar -->
    @include('partials.navbar')

    <!-- 🌟 Judul Halaman -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">{{ $title }}</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[{{ $products->count() }} RESULTS]</p>
        </div>
    </div>

    <!-- 🌟 Area Utama (Form Filter & Grid) -->
    <form method="GET" action="{{ route('sale') }}" id="saleForm" class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-12 w-full flex flex-col md:flex-row gap-12 lg:gap-20">
        
        <!-- Sidebar Filter Kiri -->
        <aside class="w-full md:w-56 flex-shrink-0 space-y-8">
            
            <!-- Filter Gender -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex justify-between items-center text-[10px] font-black tracking-widest uppercase text-black mb-5">
                    <span>Gender</span>
                </div>
                <div class="space-y-4">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="gender[]" value="Men" onchange="document.getElementById('saleForm').submit()" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black" {{ in_array('Men', request('gender', [])) ? 'checked' : '' }}>
                        <span class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Men</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="gender[]" value="Women" onchange="document.getElementById('saleForm').submit()" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black" {{ in_array('Women', request('gender', [])) ? 'checked' : '' }}>
                        <span class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Women</span>
                    </label>
                </div>
            </div>

            <!-- Filter Product Type -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex justify-between items-center text-[10px] font-black tracking-widest uppercase text-black mb-5">
                    <span>Product Type</span>
                </div>
                <div class="space-y-4">
                    <!-- 🌟 Looping Tipe Produk Secara Dinamis dari Database -->
                    @forelse($availableTypes as $type)
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="type[]" value="{{ $type }}" onchange="document.getElementById('saleForm').submit()" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black" {{ in_array($type, request('type', [])) ? 'checked' : '' }}>
                        <span class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">{{ $type }}</span>
                    </label>
                    @empty
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Belum ada tipe produk.</p>
                    @endforelse
                </div>
            </div>

        </aside>

        <!-- Kanan: Grid Produk & Sort -->
        <div class="flex-1">
            <!-- Sortir Kanan Atas -->
            <div class="flex justify-end mb-10">
                <div class="relative">
                    <select name="sort" onchange="document.getElementById('saleForm').submit()" class="appearance-none border border-gray-200 text-[11px] font-medium text-gray-700 py-2.5 pl-5 pr-12 rounded-lg focus:outline-none focus:ring-black focus:border-black bg-white cursor-pointer transition shadow-sm hover:border-gray-300">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Grid Produk -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12">
                @forelse ($products as $product)
                <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 70 }}" class="group relative card-hover">
                    <a href="{{ route('product.show', $product->id) }}" class="block w-full aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-4 relative shadow-sm hover:shadow-lg transition-shadow duration-300">
                        
                        <!-- Badge Sale Merah -->
                        <div class="absolute top-4 left-4 bg-red-600 text-white px-2.5 py-1 text-[9px] font-black tracking-widest rounded z-10 uppercase">
                            Sale
                        </div>

                        @php
                            $firstImage = (is_array($product->images) && count($product->images) > 0) ? $product->images[0] : '';
                        @endphp
                        <img src="{{ asset('storage/produk/' . $firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-out" onerror="this.src='https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                    </a>

                    <a href="{{ route('product.show', $product->id) }}" class="block">
                        <h3 class="text-[11px] font-black tracking-widest uppercase text-black mb-1.5">{{ $product->name }}</h3>
                        <div class="flex items-center space-x-3">
                            <!-- Harga Diskon Dicetak Tebal -->
                            <p class="text-xs font-bold text-red-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</p>
                            <!-- Harga Asli Dicoret -->
                            <p class="text-[11px] font-semibold text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-span-full text-center py-20">
                    <p class="text-gray-400 font-bold tracking-[0.15em] uppercase text-xs">Tidak ada produk diskon yang cocok dengan filter.</p>
                </div>
                @endforelse
            </div>
        </div>
    </form>

    <!-- 🌟 Footer Minimalis -->
    @include('partials.footer')

</body>
</html>