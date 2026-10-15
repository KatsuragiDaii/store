<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black">Hubungi Kami</h1>
        </div>
    </div>

    <main class="flex-grow max-w-3xl mx-auto px-6 md:px-12 py-16 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <!-- 🌟 GANTI nomor WhatsApp di bawah (format: 62 diikuti nomor tanpa angka 0 di depan) -->
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="block bg-black text-white rounded-2xl p-8 hover:bg-gray-900 transition">
                <h2 class="text-[11px] font-black tracking-widest uppercase mb-2">WhatsApp</h2>
                <p class="text-sm text-gray-300 mb-4">Paling cepat direspons — chat langsung buat pertanyaan produk, pesanan, atau retur.</p>
                <span class="text-xs font-black tracking-widest uppercase">Chat Sekarang &rarr;</span>
            </a>

            <!-- 🌟 GANTI alamat email di bawah -->
            <a href="mailto:hello@fromhere.shop" class="block border border-gray-200 rounded-2xl p-8 hover:border-black transition">
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-2">Email</h2>
                <p class="text-sm text-gray-600 mb-4">hello@fromhere.shop</p>
                <span class="text-xs font-black tracking-widest uppercase text-gray-500">Kirim Email &rarr;</span>
            </a>
        </div>

        <div class="border-t border-gray-100 pt-10">
            <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Jam Operasional</h2>
            <p class="text-sm font-bold text-gray-900 mb-1">Senin – Jumat: 08.00 – 18.00 WIB</p>
            <p class="text-sm text-gray-500">Sabtu, Minggu & Hari Libur Nasional: <span class="text-red-500 font-bold">Libur / Offline</span></p>
        </div>

        <div class="border-t border-gray-100 pt-10 mt-10">
            <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Pertanyaan Soal Pesanan?</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                Siapkan nomor pesananmu dulu (lihat di
                <a href="{{ route('orders.index') }}" class="text-black font-bold underline underline-offset-2">Pesanan Saya</a>)
                biar kami bisa langsung bantu cek statusnya.
            </p>
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
