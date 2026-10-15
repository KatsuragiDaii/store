<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    <!-- 🌟 Navbar -->
    @include('partials.navbar')

    <!-- 🌟 Header -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">REGISTER</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[CREATE ACCOUNT]</p>
        </div>
    </div>

    <!-- 🌟 Form Register -->
    <main class="flex-grow flex justify-center w-full bg-white" style="padding-top: 100px; padding-bottom: 120px; padding-left: 24px; padding-right: 24px;">
        <div data-animate="zoom-in" class="w-full" style="max-width: 440px;">
            
            <div class="mb-8 text-left">
                <h2 class="text-sm font-black tracking-widest uppercase text-black mb-1.5">| CREATE AN ACCOUNT</h2>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">PLEASE FILL IN THE DETAILS BELOW TO REGISTER.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 text-red-500 text-[10px] font-bold uppercase bg-red-50 py-3 px-4 rounded-lg tracking-widest border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required class="w-full px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">
                
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required class="w-full px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">
                
                <input type="password" name="password" id="regPassword" placeholder="Password (Min. 8 Char, 1 Capital, 1 Number)" required class="w-full px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">
                
                <input type="password" name="password_confirmation" id="regPasswordConfirm" placeholder="Confirm Password" required class="w-full px-4 py-3.5 border border-gray-300 rounded-lg text-sm font-medium placeholder-gray-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition bg-white">
                
                <!-- Area Checkbox -->
                <div class="flex items-center mt-1 mb-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" onclick="let p = document.getElementById('regPassword'); let c = document.getElementById('regPasswordConfirm'); p.type = c.type = this.checked ? 'text' : 'password';" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                        <span class="ml-3 text-[10px] font-bold tracking-widest uppercase text-black">SHOW PASSWORD</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-black text-white py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition">
                    REGISTER
                </button>
            </form>

            <!-- 🌟 Daftar dengan Google -->
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-[10px] font-bold tracking-widest uppercase text-gray-400">Atau</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center gap-3 bg-white text-black border border-gray-300 py-3.5 rounded-lg text-xs font-bold tracking-wide hover:border-black hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.1 8 3l6-6C33.7 5.5 29.1 3.5 24 3.5 12.7 3.5 3.5 12.7 3.5 24S12.7 44.5 24 44.5 44.5 35.3 44.5 24c0-1.2-.1-2.4-.3-3.5z"/>
                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.9 18.9 13 24 13c3.1 0 5.8 1.1 8 3l6-6C33.7 5.5 29.1 3.5 24 3.5c-7.4 0-13.8 4.2-17 10.2z"/>
                    <path fill="#4CAF50" d="M24 44.5c5 0 9.6-1.9 13-5.1l-6-4.9c-1.9 1.4-4.4 2.3-7 2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.9 40.2 16.4 44.5 24 44.5z"/>
                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.2-4.1 5.5l6 4.9c-.4.4 6.8-5 6.8-14.4 0-1.2-.1-2.4-.4-3.5z"/>
                </svg>
                Daftar dengan Google
            </a>

            <!-- 🌟 Perbaikan Jarak Teks & Tombol Sekunder -->
            <div class="mt-6 flex flex-col items-center">
                <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-4">ALREADY HAVE AN ACCOUNT?</p>
                <a href="{{ route('login') }}" class="w-full flex justify-center items-center bg-white text-black border border-gray-300 py-4 rounded-lg text-xs font-black tracking-[0.2em] uppercase hover:border-black transition shadow-sm">
                    SIGN IN INSTANTLY
                </a>
            </div>

        </div>
    </main>

    <!-- 🌟 Footer Minimalis -->
    @include('partials.footer')
</body>
</html>