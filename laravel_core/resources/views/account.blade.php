<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - FROMHERE</title>
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

        @if(session('success'))
        <div class="border border-green-200 bg-green-50 rounded-lg px-6 py-4 mb-10">
            <p class="text-[11px] font-black text-green-700 uppercase tracking-widest">{{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="border border-red-200 bg-red-50 rounded-lg px-6 py-4 mb-10">
            <ul class="text-xs text-red-600 font-bold space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">

            <!-- Kiri: Profil -->
            <div data-animate="fade-left" class="flex-1">
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Informasi Akun</h2>

                <form action="{{ route('account.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nama</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" disabled
                            class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg text-sm text-gray-400">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1.5">Email belum bisa diubah sendiri</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nomor HP</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Alamat</label>
                        <textarea name="address" rows="3" placeholder="Nama jalan, nomor rumah, kecamatan, kota, kode pos"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">{{ old('address', auth()->user()->address) }}</textarea>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1.5">Dipakai buat isi otomatis alamat pas checkout</p>
                    </div>

                    <button type="submit" class="bg-black text-white px-8 py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                        Simpan Perubahan
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="text-[10px] font-black tracking-[0.2em] uppercase text-gray-400 hover:text-red-600 transition">
                        Keluar dari Akun
                    </button>
                </form>
            </div>

            <!-- Kanan: Track Pesanan -->
            <div data-animate="fade-right" data-animate-delay="100" class="w-full lg:w-[380px]">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black">Pesanan Terbaru</h2>
                    <a href="{{ route('orders.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-black transition uppercase tracking-widest">Lihat Semua &rarr;</a>
                </div>

                @if($recentOrders->isEmpty())
                <div class="border border-gray-200 rounded-2xl p-8 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">Belum ada pesanan.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($recentOrders as $order)
                    @php
                        $badgeClasses = match($order->status->badgeColor()) {
                            'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'green' => 'bg-green-50 text-green-700 border-green-200',
                            'red' => 'bg-red-50 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-600 border-gray-200',
                        };
                    @endphp
                    <a href="{{ route('orders.show', $order) }}" class="block border border-gray-200 rounded-xl p-5 hover:border-black transition">
                        <div class="flex justify-between items-start gap-3 mb-3">
                            <p class="text-[11px] font-black tracking-widest uppercase text-black">{{ $order->order_number }}</p>
                            <span class="inline-block px-2.5 py-1 rounded-full border text-[9px] font-black uppercase tracking-widest shrink-0 {{ $badgeClasses }}">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->created_at->translatedFormat('d M Y') }}</p>
                            <p class="text-xs font-black text-black">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </main>

</body>
</html>
