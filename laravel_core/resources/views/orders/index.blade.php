<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">
    
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Header -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">ACCOUNT DASHBOARD</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[ DAFTAR TRANSAKSI SAYA ]</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full flex flex-col md:flex-row gap-12 lg:gap-20">
        
        <!-- Sidebar Navigation -->
        <aside data-animate="fade-left" class="w-full md:w-64 flex-shrink-0">
            <h3 class="text-[10px] font-black tracking-widest uppercase text-gray-400 mb-4 pl-4">ACCOUNT NAVIGATION</h3>
            <div class="space-y-1">
                
                <!-- Link: Biodata Diri & Alamat (Tidak Aktif) -->
                <a href="{{ route('account.dashboard') }}" class="flex items-center justify-between px-4 py-3.5 text-gray-500 hover:text-black hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">PROFIL & ALAMAT</span>
                    </div>
                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
                
                <!-- Active Link: Daftar Transaksi (Aktif) -->
                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-4 py-3.5 bg-black text-white rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">DAFTAR TRANSAKSI</span>
                    </div>
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>

                @if(auth()->user()->is_admin)
                <!-- Link: Admin Panel (Muncul jika Admin) -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-4 py-3.5 text-gray-500 hover:text-black hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">ADMIN PANEL</span>
                    </div>
                </a>
                @endif
                
                <hr class="border-gray-100 my-4 mx-4">
                
                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="px-4">
                    @csrf
                    <button type="submit" class="flex items-center space-x-3 w-full text-left py-3.5 text-red-500 hover:text-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">KELUAR DARI AKUN</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Content: Riwayat Pesanan -->
        <div data-animate="fade-right" data-animate-delay="100" class="flex-1 space-y-10">
            <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-8 border-b border-gray-100 pb-6">| RIWAYAT PESANAN SAYA</h2>

                @if($orders->isEmpty())
                <!-- Tampilan Jika Belum Ada Pesanan -->
                <div class="flex flex-col items-center justify-center py-12 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <h2 class="text-sm font-black tracking-[0.2em] uppercase text-black mb-3">BELUM ADA PESANAN</h2>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest max-w-sm mb-8 leading-relaxed">
                        Semua pesanan yang kamu buat bakal muncul di sini.
                    </p>
                    <a href="{{ route('products') }}" class="bg-black text-white px-8 py-3.5 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                        Mulai Belanja
                    </a>
                </div>
                @else
                <!-- Tampilan Daftar Pesanan -->
                <div class="space-y-5">
                    @foreach($orders as $order)
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
                    <a href="{{ route('orders.show', $order) }}" data-animate="fade-up" data-animate-delay="{{ $loop->index * 60 }}" class="block border border-gray-200 rounded-xl p-6 hover:border-black transition">
                        <div class="flex flex-wrap justify-between items-start gap-4">
                            <div>
                                <p class="text-xs font-black tracking-widest uppercase text-black mb-1.5">{{ $order->order_number }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $badgeClasses }}">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end mt-5 pt-5 border-t border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->items->count() }} item</p>
                            <p class="text-sm font-black text-black">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-10">
                    {{ $orders->links() }}
                </div>
                @endif

            </div>
        </div>
    </main>

    <!-- Footer Minimalis -->
    @include('partials.footer')
</body>
</html>