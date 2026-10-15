<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Fromhere</title>
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
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[{{ $collections->count() }} CATEGORIES]</p>
        </div>
    </div>

    <!-- 🌟 Area Utama (Grid Koleksi Dinamis) -->
    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            @forelse ($collections as $collection)
               <!-- Dynamic Card -->
               <a href="{{ route('products', ['collection' => $collection->slug]) }}" data-animate="fade-up" data-animate-delay="{{ $loop->index * 100 }}" class="group block w-full aspect-[4/5] bg-gray-200 rounded-xl relative overflow-hidden transition duration-500 hover:shadow-2xl card-hover">
                   <!-- Gambar -->
                   <img src="{{ asset('images/collections/' . $collection->image) }}" alt="{{ $collection->name }}" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-105">
                   
                   <!-- Overlay Transparan Bawaan (Bisa dihapus jika ingin gambar 100% terang saat tidak di-hover, tapi di sini saya buat sangat tipis) -->
                   <div class="absolute inset-0 bg-black/10 group-hover:bg-black/30 transition duration-500 z-0"></div>

                   <!-- 🌟 Efek Hover Bawah: Gradient + Teks (Muncul saat kursor diarahkan) -->
                   <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 ease-out z-20 flex flex-col items-center justify-end pb-10">
                       <h3 class="text-white text-[13px] font-black tracking-[0.2em] uppercase mb-4 text-center px-6">
                           {{ $collection->name }}
                       </h3>
                       <span class="text-[9px] font-bold tracking-[0.2em] text-gray-300 uppercase border-b border-gray-400 pb-1 group-hover:text-white group-hover:border-white transition-colors duration-300">
                           VIEW PRODUCTS &rarr;
                       </span>
                   </div>
               </a>
            @empty
            <!-- Jika database kosong, 3 kotak lama otomatis tidak muncul -->
            <div class="col-span-full text-center py-20">
                <p class="text-gray-400 font-bold tracking-[0.15em] uppercase text-xs">Belum ada koleksi yang ditambahkan.</p>
            </div>
            @endforelse

        </div>
    </main>

    <!-- 🌟 Footer Minimalis -->
    @include('partials.footer')

</body>
</html>