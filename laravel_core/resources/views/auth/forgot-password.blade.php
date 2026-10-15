<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">ACCOUNT</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[RESET PASSWORD]</p>
        </div>
    </div>

    <main class="flex-grow flex justify-center w-full bg-white" style="padding-top: 100px; padding-bottom: 120px; padding-left: 24px; padding-right: 24px;">
        <div class="w-full" style="max-width: 440px;">

            <div class="mb-8 text-left">
                <h2 class="text-sm font-black tracking-widest uppercase text-black mb-1.5">| LUPA PASSWORD?</h2>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">MASUKKAN EMAIL AKUNMU, KAMI KIRIMKAN LINK UNTUK RESET PASSWORD.</p>
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

            <form action="{{ route('password.email') }}" method="POST" class="flex flex-col">
                @csrf

                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus
                    class="w-full mb-6 px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">

                <button type="submit" class="w-full bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    Kirim Link Reset
                </button>
            </form>

            <div class="mt-6 flex flex-col items-center">
                <a href="{{ route('login') }}" class="text-[10px] font-bold tracking-widest uppercase text-gray-400 hover:text-black transition">&larr; Kembali ke Login</a>
            </div>

        </div>
    </main>

    @include('partials.footer')
</body>
</html>
