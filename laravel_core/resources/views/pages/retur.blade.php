<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Retur - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-3xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black">Kebijakan Retur</h1>
        </div>
    </div>

    <main class="flex-grow max-w-3xl mx-auto px-6 md:px-12 py-16 w-full">
        <div class="space-y-10">
            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Retur & Tukar Ukuran</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Kami menerima permintaan tukar ukuran atau retur dalam <strong>3 hari</strong>
                    sejak barang diterima. Ajukan sesegera mungkin lewat WhatsApp supaya bisa
                    langsung kami bantu prosesnya.
                </p>
            </div>

            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Syarat Barang</h2>
                <ul class="text-sm text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                    <li>Belum pernah dipakai, dicuci, atau diubah</li>
                    <li>Label/tag masih menempel lengkap</li>
                    <li>Dikembalikan dalam kemasan asli beserta struk/nota pesanan</li>
                    <li>Tidak dalam kondisi rusak atau cacat akibat pemakaian</li>
                </ul>
            </div>

            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Cara Mengajukan</h2>
                <ol class="text-sm text-gray-600 leading-relaxed space-y-2 list-decimal pl-5">
                    <li>Hubungi kami lewat WhatsApp dengan menyertakan nomor pesanan (lihat di halaman Pesanan Saya)</li>
                    <li>Jelaskan alasan retur/tukar ukuran, sertakan foto produk kalau ada masalah kualitas</li>
                    <li>Kami akan konfirmasi apakah disetujui, sekaligus atur pengambilan/pengiriman baliknya</li>
                    <li>Tukar ukuran akan dikirim ulang setelah barang lama kami terima; retur dana diproses maksimal 3 hari kerja</li>
                </ol>
            </div>

            <div>
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Yang Tidak Bisa Diretur</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Produk dengan label diskon "final sale" dan barang yang sudah dipakai/dicuci
                    tidak bisa diretur atau ditukar, kecuali terbukti cacat produksi.
                </p>
            </div>

            <div class="bg-[#F9F9F9] rounded-2xl p-8">
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-3">Mau Ajukan Retur?</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Siapkan nomor pesananmu, lalu hubungi kami langsung buat mulai prosesnya.
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
