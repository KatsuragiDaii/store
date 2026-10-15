<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengiriman - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black">Pengiriman</h1>
        </div>
    </div>

    <main class="flex-grow max-w-3xl mx-auto px-6 md:px-12 py-16 w-full">
        <div class="space-y-10">
            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Ongkos Kirim Gratis</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Semua pesanan di FROMHERE dikirim tanpa biaya ongkos kirim tambahan. Pengiriman
                    kami tangani secara langsung/personal, bukan lewat jasa ekspedisi otomatis —
                    jadi begitu pesananmu kami proses, kami yang akan mengatur pengantarannya sampai
                    ke alamat yang kamu isi saat checkout.
                </p>
            </div>

            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Estimasi Waktu</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Pesanan diproses 1–2 hari kerja setelah pembayaran terkonfirmasi, lalu dikirim
                    sesuai jarak dan lokasi tujuan. Kami akan menghubungi lewat nomor HP yang kamu
                    daftarkan kalau ada kendala atau perkiraan waktu yang perlu disesuaikan.
                </p>
            </div>

            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Lacak Pesanan</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Status pesananmu bisa dipantau kapan saja lewat halaman
                    <a href="{{ route('orders.index') }}" class="text-black font-bold underline underline-offset-2">Pesanan Saya</a>
                    setelah login — mulai dari menunggu pembayaran, diproses, sampai dikirim.
                </p>
            </div>

            <div class="bg-[#F9F9F9] rounded-2xl p-8">
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Ada Pertanyaan?</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Kalau ada pertanyaan soal status atau estimasi pengiriman pesananmu, hubungi kami
                    langsung — responsnya jauh lebih cepat lewat WhatsApp.
                </p>
                <a href="{{ route('page.kontak') }}" class="inline-block bg-black text-white px-8 py-3.5 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
