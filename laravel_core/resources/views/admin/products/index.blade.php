<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F9F9F9] min-h-screen text-gray-900">
    
    @include('partials.navbar') <!-- Pastikan admin navbar diload -->

    <div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
        <div data-animate="fade-down" class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-widest text-black">KELOLA PRODUK</h1>
                <p class="text-xs font-bold text-gray-400 mt-2">Daftar semua produk di katalog Anda.</p>
            </div>
            <a href="/tambah-produk" class="bg-black text-white px-6 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition shadow-md">
                + Tambah Produk
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 text-green-700 text-xs font-bold uppercase tracking-widest px-6 py-4 rounded-xl border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div data-animate="fade-up" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100 text-[10px] font-black tracking-widest uppercase text-gray-500">
                    <tr>
                        <th class="py-4 px-6">Produk</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Harga</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-medium">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 flex items-center gap-4">
                            @php $img = is_array($product->images) && count($product->images) > 0 ? $product->images[0] : ''; @endphp
                            <img src="{{ asset('storage/produk/' . $img) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover border border-gray-200">
                            <span class="font-bold text-black uppercase tracking-widest text-xs">{{ $product->name }}</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-gray-500 uppercase tracking-widest">{{ $product->type }}</td>
                        <td class="py-4 px-6 text-xs font-bold text-black">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right space-x-3">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 text-[10px] font-black uppercase tracking-widest transition">Edit</a>
                            
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini dari katalog?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</body>
</html>