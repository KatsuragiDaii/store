<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - FROMHERE</title>
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
                <h2 class="text-sm font-black tracking-widest uppercase text-black mb-1.5">| CEK EMAIL KAMU</h2>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase leading-relaxed">KAMI MENGIRIM LINK VERIFIKASI KE <span class="text-black">{{ $email }}</span>. KLIK LINK ITU UNTUK MENGAKTIFKAN AKUNMU.</p>
            </div>

            @if(session('status'))
                <div class="mb-6 text-green-700 text-[10px] font-bold uppercase bg-green-50 py-3 px-4 rounded-lg tracking-widest border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 text-red-500 text-[10px] font-bold uppercase bg-red-50 py-3 px-4 rounded-lg tracking-widest border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="text-xs text-gray-500 leading-relaxed mb-4">Link-nya boleh dibuka di perangkat lain, misalnya HP. Halaman ini akan lanjut sendiri setelah emailmu terverifikasi.</p>

            <p class="text-xs text-gray-500 leading-relaxed mb-6">Belum menerima emailnya? Cek folder spam, atau kirim ulang link di bawah. Salah mengetik email? Keluar dari akun ini, lalu daftar lagi dengan email yang benar.</p>

            <form action="{{ route('verification.send') }}" method="POST" class="flex flex-col">
                @csrf
                <button type="submit" class="w-full bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Kirim Ulang Link
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="mt-6 flex flex-col items-center">
                @csrf
                <button type="submit" class="text-[10px] font-bold tracking-widest uppercase text-gray-400 hover:text-black transition">Keluar dari akun</button>
            </form>

        </div>
    </main>

    @include('partials.footer')

    {{-- Setelah link di email diklik (di HP/perangkat lain), halaman ini lanjut sendiri. --}}
    <script>
        (function () {
            var statusUrl = @json(route('verification.status'));
            var nextUrl = @json(route('account.dashboard'));
            var done = false;

            function cek() {
                if (done) return;
                fetch(statusUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (d) {
                        if (d && d.verified) { done = true; window.location.href = nextUrl; }
                    })
                    .catch(function () { /* abaikan, coba lagi nanti */ });
            }

            setInterval(cek, 4000);
            document.addEventListener('visibilitychange', function () { if (!document.hidden) cek(); });
        })();
    </script>
</body>
</html>
