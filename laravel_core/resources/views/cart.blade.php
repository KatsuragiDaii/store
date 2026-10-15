<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    <!-- 🌟 Navbar -->
    @include('partials.navbar')

    <!-- 🌟 Header Keranjang -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">{{ $title }}</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[{{ count($cart) }} ITEMS]</p>
        </div>
    </div>

    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full">
        @if(count($cart) > 0)
            <!-- 🌟 KONDISI: KERANJANG TERISI -->
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
                
                <!-- Kiri: Daftar Produk -->
                <div class="flex-1">
                    <div class="flex justify-end mb-4">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold tracking-widest text-gray-400 hover:text-red-500 uppercase transition">Hapus Semua</button>
                        </form>
                    </div>

                    <div class="border-t border-gray-200">
                        @foreach($cart as $key => $item)
                        <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 70 }}" class="flex py-8 border-b border-gray-200 gap-6">
                            <!-- Foto Produk -->
                            <div class="w-24 h-32 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                                <img src="{{ asset('storage/produk/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            </div>
                            
                            <!-- Info Produk -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xs font-black tracking-widest uppercase text-black mb-1.5">{{ $item['name'] }}</h3>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">SIZE: {{ $item['size'] }}</p>
                                        <p class="text-xs font-bold text-black mt-3">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <!-- Tombol Hapus 1 Item -->
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cart_key" value="{{ $key }}">
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- 🌟 Form Update Qty Aktif -->
                                <form action="{{ route('cart.update') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="cart_key" value="{{ $key }}">
                                    <div class="flex items-center border border-gray-200 rounded h-10 px-1 w-24 bg-white">
                                        <!-- Tombol Minus (Kirim jumlah saat ini dikurangi 1) -->
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="w-8 h-full flex justify-center items-center text-gray-500 hover:text-black cursor-pointer">-</button>
                                        
                                        <!-- Angka Saat Ini -->
                                        <div class="flex-1 text-center text-[11px] font-bold">{{ $item['quantity'] }}</div>
                                        
                                        <!-- Tombol Plus (Kirim jumlah saat ini ditambah 1) -->
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="w-8 h-full flex justify-center items-center text-gray-500 hover:text-black cursor-pointer">+</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kanan: Order Summary -->
                <div data-animate="fade-left" class="w-full lg:w-[400px]">
                    <div class="bg-[#F9F9F9] p-8 rounded-2xl border border-gray-100">
                        <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Order Summary</h2>
                        
                        <div class="space-y-4 text-xs font-bold border-b border-gray-200 pb-6 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-500">SUBTOTAL</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">SHIPPING</span>
                                <span class="text-green-600">FREE</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-8">
                            <span class="text-[11px] font-black tracking-widest uppercase text-black">Total Cost</span>
                            <span class="text-lg font-black text-black">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout') }}" class="block w-full text-center bg-black text-white py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                            Proceed to Checkout &rarr;
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- 🌟 KONDISI: KERANJANG KOSONG -->
            <div data-animate="zoom-in" class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 bg-[#F9F9F9] rounded-full flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h2 class="text-sm font-black tracking-[0.2em] uppercase text-black mb-3">Your Cart is Empty</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest max-w-sm mb-10 leading-relaxed">
                    Tidak ada produk yang mengantre. Silakan kembali meluncur ke katalog untuk memilih produk favoritmu.
                </p>
                <a href="{{ route('products') }}" class="bg-black text-white px-8 py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                    Shop Our Collections
                </a>
            </div>
        @endif
    </main>

    <!-- 🌟 Footer Minimalis -->
    @include('partials.footer')

</body>
</html>