<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black">{{ $title }}</h1>
        </div>
    </div>

    <main class="flex-grow max-w-5xl mx-auto px-6 md:px-12 py-16 w-full">

        <!-- Ringkasan Cepat -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-14">
            <div data-animate="fade-up" class="border border-gray-200 rounded-xl p-6">
                <p class="text-2xl font-black text-black">{{ $pendingCount }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Menunggu Bayar</p>
            </div>
            <div data-animate="fade-up" data-animate-delay="60" class="border border-gray-200 rounded-xl p-6">
                <p class="text-2xl font-black text-black">{{ $paidCount }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Sudah Dibayar</p>
            </div>
            <div data-animate="fade-up" data-animate-delay="120" class="border border-gray-200 rounded-xl p-6">
                <p class="text-2xl font-black text-black">{{ $processingCount }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Diproses</p>
            </div>
            <div data-animate="fade-up" data-animate-delay="180" class="border border-gray-200 rounded-xl p-6">
                <p class="text-2xl font-black text-black">{{ $productCount }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Produk</p>
            </div>
        </div>

        <!-- Menu -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.orders.index') }}" data-animate="fade-up" data-animate-delay="240" class="block border border-gray-200 rounded-2xl p-8 hover:border-black transition card-hover">
                <h2 class="text-xs font-black tracking-widest uppercase text-black mb-2">Kelola Pesanan</h2>
                <p class="text-[11px] text-gray-500 leading-relaxed">Lihat pesanan masuk, update status, dan isi nomor resi.</p>
            </a>
            <!-- Ubah kartu Tambah Produk menjadi Kelola Produk -->
            <a href="{{ route('admin.products.index') }}" data-animate="fade-up" data-animate-delay="280" class="p-6 border border-gray-200 rounded-xl hover:shadow-md transition bg-white card-hover">
                <h3 class="text-sm font-black uppercase tracking-widest text-black mb-2">KELOLA PRODUK</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Lihat daftar produk, edit harga, tambah foto, atau hapus produk.
                </p>
            </a>
            <a href="{{ url('/tambah-koleksi') }}" data-animate="fade-up" data-animate-delay="320" class="block border border-gray-200 rounded-2xl p-8 hover:border-black transition card-hover">
                <h2 class="text-xs font-black tracking-widest uppercase text-black mb-2">Tambah Koleksi</h2>
                <p class="text-[11px] text-gray-500 leading-relaxed">Buat koleksi baru untuk mengelompokkan produk di halaman Collections.</p>
            </a>
            <!-- KARTU MANAJEMEN STOK CEPAT -->
<a href="{{ route('admin.products.stock') }}" data-animate="fade-up" data-animate-delay="360" class="p-6 border border-gray-200 rounded-xl hover:shadow-md transition bg-white flex flex-col justify-center card-hover">
    <h3 class="text-sm font-black uppercase tracking-widest text-black mb-2">UPDATE STOK CEPAT</h3>
    <p class="text-xs text-gray-500 leading-relaxed">
        Ubah kuantitas stok produk per ukuran dalam satu layar secara instan.
    </p>
</a>
            <!-- KARTU KELUAR DARI AKUN (LOGOUT) -->
<form data-animate="fade-up" data-animate-delay="400" action="{{ route('logout') }}" method="POST" class="h-full">
    @csrf
    <button type="submit" class="w-full h-full text-left p-6 border border-red-200 bg-red-50 rounded-xl hover:bg-red-100 transition duration-300 shadow-sm flex flex-col justify-center">
        <h3 class="text-sm font-black uppercase text-red-600 mb-2">KELUAR DARI AKUN</h3>
        <p class="text-xs text-red-500 leading-relaxed">
            Akhiri sesi admin Anda dan kembali ke halaman utama situs.
        </p>
    </button>
</form>
        </div>
    </main>

</body>
</html>
