<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">

    @include('partials.navbar')

    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 md:px-12 flex items-center justify-between flex-wrap gap-4">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black">{{ $title }}</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 hover:text-black transition">&larr; Dashboard Admin</a>
        </div>
    </div>

    <main class="flex-grow max-w-6xl mx-auto px-6 md:px-12 py-16 w-full">

        <!-- Filter Status -->
        <div class="flex flex-wrap gap-2 mb-10">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-widest transition {{ !$statusFilter ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-black' }}">Semua</a>
            @foreach($statuses as $status)
            <a href="{{ route('admin.orders.index', ['status' => $status->value]) }}" class="px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-widest transition {{ $statusFilter === $status->value ? 'bg-black text-white border-black' : 'border-gray-200 text-gray-500 hover:border-black' }}">{{ $status->label() }}</a>
            @endforeach
        </div>

        @if($orders->isEmpty())
        <div class="text-center py-20">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tidak ada pesanan di kategori ini.</p>
        </div>
        @else
        <div class="space-y-4">
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
            <a href="{{ route('admin.orders.show', $order) }}" data-animate="fade-up" data-animate-delay="{{ min($loop->index * 40, 320) }}" class="block border border-gray-200 rounded-xl p-6 hover:border-black transition">
                <div class="flex flex-wrap justify-between items-start gap-4">
                    <div>
                        <p class="text-xs font-black tracking-widest uppercase text-black mb-1.5">{{ $order->order_number }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->user->name ?? 'Pengguna dihapus' }} &middot; {{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $badgeClasses }}">
                        {{ $order->status->label() }}
                    </span>
                </div>
                <div class="flex justify-between items-end mt-5 pt-5 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $order->items->count() }} item &middot; {{ $order->shipping_city }}</p>
                    <p class="text-sm font-black text-black">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $orders->links() }}
        </div>
        @endif
    </main>

</body>
</html>
