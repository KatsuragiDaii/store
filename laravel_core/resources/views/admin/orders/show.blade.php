<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->order_number }} - Admin FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

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

    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 md:px-12 flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 hover:text-black transition block mb-3">&larr; Kelola Pesanan</a>
                <h1 class="text-2xl md:text-3xl font-black tracking-[0.1em] uppercase text-black mb-2">{{ $order->order_number }}</h1>
                <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
            </div>
            <span class="inline-block px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $badgeClasses }}">
                {{ $order->status->label() }}
            </span>
        </div>
    </div>

    <main class="flex-grow max-w-4xl mx-auto px-6 md:px-12 py-16 w-full">

        @if(session('success'))
        <div class="border border-green-200 bg-green-50 rounded-lg px-6 py-4 mb-10">
            <p class="text-[11px] font-black text-green-700 uppercase tracking-widest">{{ session('success') }}</p>
        </div>
        @endif

        <!-- 🌟 Form Update Status -->
        <div data-animate="fade-up" class="bg-black text-white rounded-2xl p-8 mb-12">
            <h2 class="text-[11px] font-black tracking-widest uppercase text-gray-300 mb-6">Update Status Pesanan</h2>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-lg text-sm bg-white text-black focus:outline-none focus:ring-2 focus:ring-white">
                            @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-2">Nomor Resi (opsional)</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Kalau dikirim lewat kurir"
                            class="w-full px-4 py-3 rounded-lg text-sm bg-white text-black focus:outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-2">Kurir (opsional)</label>
                        <input type="text" name="courier" value="{{ old('courier', $order->courier) }}" placeholder="Diantar Sendiri / JNE / J&T / dst"
                            class="w-full px-4 py-3 rounded-lg text-sm bg-white text-black focus:outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-gray-400 uppercase mb-2">Layanan (opsional)</label>
                        <input type="text" name="courier_service" value="{{ old('courier_service', $order->courier_service) }}" placeholder="REG / Instan / dst"
                            class="w-full px-4 py-3 rounded-lg text-sm bg-white text-black focus:outline-none focus:ring-2 focus:ring-white">
                    </div>
                </div>
                <button type="submit" class="bg-white text-black px-8 py-3 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-100 transition">
                    Simpan Perubahan
                </button>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest leading-relaxed">
                    Pilih "Sudah Dibayar" cuma untuk konfirmasi manual di luar Midtrans (transfer langsung, dsb) — stok otomatis dikurangi sekali saat itu terjadi, sama seperti kalau dikonfirmasi otomatis lewat Midtrans.
                </p>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
            <!-- Kiri: Item + Pembeli -->
            <div data-animate="fade-left" class="flex-1">
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-4">Pembeli</h2>
                <div class="border border-gray-200 rounded-xl p-6 mb-10">
                    <p class="text-xs font-black text-black mb-1">{{ $order->user->name ?? 'Pengguna dihapus' }}</p>
                    <p class="text-xs text-gray-500">{{ $order->user->email ?? '-' }}</p>
                </div>

                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Item Dipesan</h2>
                <div class="border-t border-gray-200">
                    @foreach($order->items as $item)
                    <div class="flex py-6 border-b border-gray-200 gap-5">
                        <div class="w-16 h-20 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                            @if($item->product_image)
                            <img src="{{ asset('storage/produk/'.$item->product_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xs font-black tracking-widest uppercase text-black mb-1.5">{{ $item->product_name }}</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Size: {{ $item->size }} &times; {{ $item->quantity }}</p>
                            <p class="text-xs font-bold text-black">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Kanan: Ringkasan & Alamat -->
            <div data-animate="fade-right" data-animate-delay="100" class="w-full lg:w-[360px] space-y-6">
                <div class="bg-[#F9F9F9] p-8 rounded-2xl border border-gray-100">
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Ringkasan</h2>
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
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] font-black tracking-widest uppercase text-black">Total</span>
                        <span class="text-lg font-black text-black">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-2xl p-8">
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-4">Dikirim Ke</h2>
                    <p class="text-xs font-black text-black mb-1">{{ $order->recipient_name }}</p>
                    <p class="text-xs text-gray-500 font-bold mb-3">{{ $order->recipient_phone }}</p>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_district }}, {{ $order->shipping_city }}<br>
                        {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                    </p>
                    @if($order->notes)
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-4 pt-4 border-t border-gray-100">Catatan: {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </main>

</body>
</html>
