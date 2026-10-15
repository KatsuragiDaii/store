<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fromhere - Clothing Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; }
    </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    <!-- 🌟 Navbar Elegan & Presisi Tengah -->
    @include('partials.navbar')

    <!-- 🌟 Hero Section -->
    <header class="relative w-full h-[85vh]">
        <div class="absolute inset-0 w-full h-full overflow-hidden bg-gray-900">
            <video autoplay loop muted playsinline class="absolute top-0 left-0 w-full h-full object-cover opacity-80">
                <source src="{{ asset('video-utama.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-black/30"></div>
        </div>
        <div class="relative z-10 h-full flex items-center max-w-7xl mx-auto px-6 md:px-12">
            <div class="max-w-xl text-white">
                <h1 data-animate="fade-up" class="text-6xl md:text-7xl font-black tracking-tighter leading-[0.9] mb-8">FROMHERE<br>CLOTHING</h1>
                <div data-animate="fade-up" data-animate-delay="120" class="flex items-center space-x-4 mb-6">
                    <div class="w-0.5 h-5 bg-white"></div>
                    <h2 class="text-xs md:text-sm font-bold tracking-[0.2em] uppercase">A New Mix Of Classics.</h2>
                </div>
                <p data-animate="fade-up" data-animate-delay="220" class="text-[11px] md:text-xs font-medium leading-relaxed tracking-wider text-gray-200 mb-10 max-w-sm">
                    UPGRADE PENAMPILAN HARIANMU DENGAN KOLEKSI STREETWEAR APPAREL TERBARU YANG DIRANCANG UNTUK KENYAMANAN MUTAKHIR.
                </p>
                <a data-animate="fade-up" data-animate-delay="320" href="{{ route('products') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-black text-[11px] font-black tracking-widest uppercase hover:bg-gray-100 transition duration-300">
                    SHOP NOW &rarr;
                </a>
            </div>
        </div>
        <div class="absolute bottom-10 left-6 md:left-12 z-10 flex space-x-3">
            <div class="w-8 h-[1px] bg-white"></div>
            <div class="w-8 h-[1px] bg-white/40"></div>
            <div class="w-8 h-[1px] bg-white/40"></div>
        </div>
    </header>

    <!-- 🌟 Featured Collection Section -->
    <main class="flex-grow">
        <section class="max-w-7xl mx-auto px-6 md:px-12 py-24">
            
            <div data-animate="fade-up" class="flex justify-between items-end border-b border-gray-200 pb-4 mb-12">
                <h2 class="text-xl md:text-2xl font-black tracking-widest uppercase text-black">
                    Featured Collection
                </h2>
            </div>

            <!-- Grid Produk -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-16">
            
                <!-- Looping data produk dari database -->
                @forelse ($products as $product)
                    <a href="{{ route('product.show', $product->id) }}" data-animate="fade-up" data-animate-delay="{{ $loop->index * 80 }}" class="group block">
                        <div class="w-full aspect-[3/4] bg-gray-100 rounded-3xl overflow-hidden mb-5 relative shadow-sm hover:shadow-xl card-hover">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-2.5 py-1 text-[10px] font-black tracking-widest rounded text-gray-900 z-10">
                                NEW
                            </div>
                            
                            <!-- Ambil gambar urutan pertama (index 0) dari Array images -->
                            @php
                                $firstImage = (is_array($product->images) && count($product->images) > 0) ? $product->images[0] : '';
                            @endphp

                            <!-- Gambar Produk -->
                            <img src="{{ asset('storage/produk/' . $firstImage) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-out"
                                 onerror="this.src='https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                        </div>
                        
                        <h3 class="text-xs font-black tracking-widest uppercase text-black mb-1">{{ $product->name }}</h3>
                        <p class="text-xs font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-gray-500 font-bold tracking-widest uppercase text-sm">Belum ada produk yang ditambahkan.</p>
                    </div>
                @endforelse

            </div> <!-- Akhir dari Grid Produk -->

            <!-- 🌟 Tombol View All Products (Dikembalikan ke sini) -->
            <div data-animate="zoom-in" class="mt-20 text-center">
                <a href="{{ route('products') }}" class="inline-flex items-center justify-center px-10 py-4 bg-black text-white text-[11px] font-black tracking-widest uppercase hover:bg-gray-900 transition duration-300 shadow-xl rounded-lg">
                    VIEW ALL PRODUCTS &rarr;
                </a>
            </div>
        </section>
    </main>

    <!-- 🌟 Footer Minimalis -->
    @include('partials.footer')

</body>
</html>