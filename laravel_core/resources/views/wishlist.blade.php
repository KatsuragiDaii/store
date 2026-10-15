<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">
    @include('partials.navbar')

    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">{{ $title }}</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[{{ $wishlists->count() }} SAVED ITEMS]</p>
        </div>
    </div>

    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full">
        @if($wishlists->isEmpty())
        <div data-animate="zoom-in" class="flex flex-col items-center justify-center py-20 text-center">
            <h2 class="text-sm font-black tracking-[0.2em] uppercase text-black mb-3">WISHLIST KOSONG</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest max-w-sm mb-10 leading-relaxed">
                Belum ada produk yang kamu simpan. Kumpulkan produk favoritmu di sini!
            </p>
            <a href="{{ route('products') }}" class="bg-black text-white px-8 py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                Jelajahi Produk
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
            @foreach ($wishlists as $wish)
            @php $product = $wish->product; @endphp
            <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 70 }}" class="group relative card-hover">
                <a href="{{ route('product.show', $product->id) }}" class="block w-full aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden mb-4 relative shadow-sm hover:shadow-lg transition-shadow duration-300">
                    @php $firstImage = (is_array($product->images) && count($product->images) > 0) ? $product->images[0] : ''; @endphp
                    <img src="{{ asset('storage/produk/' . $firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-out">
                </a>
                
                <!-- Tombol Hapus Langsung Dari Wishlist -->
                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="absolute top-4 right-4 z-10">
                    @csrf
                    <button type="submit" class="bg-white/90 text-red-500 hover:text-white hover:bg-red-500 p-2.5 rounded-full shadow-md transition duration-300">
                        <svg class="w-4 h-4" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                    </button>
                </form>

                <a href="{{ route('product.show', $product->id) }}" class="block">
                    <h3 class="text-[11px] font-black tracking-widest uppercase text-black mb-1.5">{{ $product->name }}</h3>
                    <p class="text-xs font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </main>
</body>
</html>