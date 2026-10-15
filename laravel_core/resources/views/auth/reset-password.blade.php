<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">ACCOUNT</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[SET NEW PASSWORD]</p>
        </div>
    </div>

    <main class="flex-grow flex justify-center w-full bg-white" style="padding-top: 100px; padding-bottom: 120px; padding-left: 24px; padding-right: 24px;">
        <div class="w-full" style="max-width: 440px;">

            <div class="mb-8 text-left">
                <h2 class="text-sm font-black tracking-widest uppercase text-black mb-1.5">| BUAT PASSWORD BARU</h2>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">MINIMAL 8 KARAKTER, KOMBINASI HURUF BESAR, KECIL, ANGKA & SIMBOL.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 text-red-500 text-[10px] font-bold uppercase bg-red-50 py-3 px-4 rounded-lg tracking-widest border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="flex flex-col">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <input type="email" name="email" value="{{ old('email', $email) }}" placeholder="Email Address" required
                    class="w-full mb-4 px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">

                <input type="password" name="password" placeholder="Password Baru" required
                    class="w-full mb-4 px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">

                <input type="password" name="password_confirmation" placeholder="Ulangi Password Baru" required
                    class="w-full mb-6 px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">

                <button type="submit" class="w-full bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Simpan Password Baru
                </button>
            </form>

        </div>
    </main>

    @include('partials.footer')
</body>
</html>
