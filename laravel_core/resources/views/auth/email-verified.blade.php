<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">ACCOUNT</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[VERIFIKASI EMAIL]</p>
        </div>
    </div>

    <main class="flex-grow flex justify-center w-full bg-white" style="padding-top: 100px; padding-bottom: 120px; padding-left: 24px; padding-right: 24px;">
        <div class="w-full" style="max-width: 440px;">

            <div class="mb-8 text-left">
                <h2 class="text-sm font-black tracking-widest uppercase text-black mb-1.5">| EMAIL TERVERIFIKASI</h2>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase leading-relaxed">AKUNMU SUDAH AKTIF. TERIMA KASIH!</p>
            </div>

            @if($sudahLogin)
                <a href="{{ route('account.dashboard') }}" class="block w-full text-center bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Lanjut ke Akun
                </a>
            @else
                <p class="text-xs text-gray-500 leading-relaxed mb-6">Kalau kamu mendaftar di perangkat lain (misalnya laptop), halaman di sana akan lanjut sendiri. Kamu juga bisa login di sini.</p>
                <a href="{{ route('login') }}" class="block w-full text-center bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Login
                </a>
            @endif

        </div>
    </main>

    @include('partials.footer')
</body>
</html>
