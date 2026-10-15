<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan {{ $order->order_number }} - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">
    
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Header -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 md:px-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">DETAIL PESANAN</h1>
                <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[ {{ $order->order_number }} ]</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-black uppercase tracking-widest flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full">
        
        <!-- Status Banner -->
        @php
            $badgeClasses = match($order->status->value ?? strtolower($order->status)) {
                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                'shipped' => 'bg-purple-50 text-purple-700 border-purple-200',
                'completed' => 'bg-green-50 text-green-700 border-green-200',
                'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                default => 'bg-gray-50 text-gray-600 border-gray-200',
            };
        @endphp

        <div data-animate="fade-up" class="mb-10 flex items-center justify-between border-b border-gray-100 pb-6">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Transaksi</p>
                <p class="text-xs font-black text-black uppercase tracking-widest">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $badgeClasses }}">
                    {{ is_object($order->status) && method_exists($order->status, 'label') ? $order->status->label() : strtoupper($order->status) }}
                </span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
            
            <!-- Kiri: Daftar Barang & Alamat -->
            <div data-animate="fade-left" class="flex-1 space-y-12">
                
                <!-- Daftar Produk -->
                <div>
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Produk yang Dipesan</h2>
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 60 }}" class="flex gap-5 items-start border border-gray-100 p-4 rounded-xl">
                            <!-- BUG FIX GAMBAR: Menggunakan storage/produk -->
                            <div class="w-20 h-24 bg-gray-50 rounded-lg overflow-hidden shrink-0">
                                @if($item->product_image)
                                    <img src="{{ asset('storage/produk/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="text-xs font-black uppercase tracking-widest text-black mb-1.5">{{ $item->product_name }}</h3>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Size: {{ $item->size }} &nbsp;|&nbsp; Qty: {{ $item->quantity }}</p>
                                <p class="text-xs font-black text-black">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right hidden sm:block">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Harga</p>
                                <p class="text-xs font-black text-black">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Info Pengiriman -->
                <div>
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Informasi Pengiriman</h2>
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <p class="text-xs font-black uppercase tracking-widest text-black mb-1">{{ $order->recipient_name }}</p>
                        <p class="text-[10px] font-bold text-gray-500 tracking-widest mb-4">{{ $order->recipient_phone }}</p>
                        <p class="text-xs text-gray-600 leading-relaxed mb-1">{{ $order->shipping_address }}</p>
                        <p class="text-xs text-gray-600 leading-relaxed uppercase">
                            KEC. {{ $order->shipping_district }}, {{ $order->shipping_city }}<br>
                            {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                        </p>
                        
                        @if($order->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Catatan Pesanan:</p>
                            <p class="text-xs text-gray-800 italic">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kanan: Ringkasan & Pembayaran -->
            <div data-animate="fade-right" data-animate-delay="100" class="w-full lg:w-[400px]">
                <div class="bg-[#F9F9F9] p-8 rounded-2xl border border-gray-100 lg:sticky lg:top-24">
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Ringkasan Pembayaran</h2>
                    
                    <div class="space-y-4 text-xs font-bold border-b border-gray-200 pb-6 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-500">SUBTOTAL</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ONGKOS KIRIM</span>
                            @if($order->shipping_cost > 0)
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            @else
                            <span class="text-green-600">GRATIS</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mb-8">
                        <span class="text-[11px] font-black tracking-widest uppercase text-black">Total Tagihan</span>
                        <span class="text-lg font-black text-red-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>

                    <!-- TOMBOL BAYAR MIDTRANS (Hanya jika Pending & Token Ada) -->
                    @php
                        // Normalisasi pengecekan enum jika status berupa object Enum
                        $isPending = (is_object($order->status) ? $order->status->value : strtolower($order->status)) === 'pending';
                    @endphp

                    @if($isPending)
                        @if($order->snap_token)
                            <button id="tombol-bayar" class="w-full bg-black text-white flex items-center justify-center py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Bayar Sekarang
                            </button>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-4 text-center leading-relaxed">
                                Pembayaran diproses aman oleh Midtrans.
                            </p>
                        @else
                            <div class="bg-red-50 text-red-600 p-4 rounded-lg text-center border border-red-200">
                                <p class="text-[10px] font-bold tracking-widest uppercase">Token pembayaran gagal dimuat.</p>
                                <p class="text-xs mt-1">Silakan hubungi Admin.</p>
                            </div>
                        @endif
                    @else
                        <!-- Tampilan Jika Sudah Dibayar / Selesai / Dibatalkan -->
                        <div class="w-full bg-gray-200 text-gray-500 flex items-center justify-center py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase cursor-not-allowed">
                            {{ is_object($order->status) && method_exists($order->status, 'label') ? $order->status->label() : strtoupper($order->status) }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Minimalis -->
    @include('partials.footer')

    <!-- ============================================== -->
    <!-- SCRIPT MIDTRANS SNAP MODAL                     -->
    <!-- ============================================== -->
    @if($isPending && $order->snap_token)
        <!-- Muat Script Midtrans (Sandbox atau Production berdasarkan Config) -->
        <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
                data-client-key="{{ config('midtrans.client_key') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const payButton = document.getElementById('tombol-bayar');
                const snapToken = '{{ $order->snap_token }}';

                if (payButton && snapToken) {
                    payButton.addEventListener('click', function () {
                        // Buka Pop-up Midtrans
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                // Aksi jika berhasil dibayar
                                alert("Terima kasih! Pembayaran Anda berhasil.");
                                window.location.reload(); 
                            },
                            onPending: function(result) {
                                // Aksi jika memilih metode seperti Indomaret/VA tapi belum transfer
                                alert("Menunggu pembayaran Anda. Silakan selesaikan transaksi.");
                                window.location.reload();
                            },
                            onError: function(result) {
                                // Aksi jika pembayaran ditolak/gagal
                                alert("Mohon maaf, pembayaran gagal atau kadaluarsa.");
                                window.location.reload();
                            },
                            onClose: function() {
                                // Aksi jika pop-up ditutup paksa oleh user
                                console.log('Pengguna menutup pop-up tanpa menyelesaikan pembayaran');
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</body>
</html>