<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok Cepat - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F9F9F9] min-h-screen text-gray-900">
    
    @include('partials.navbar') <!-- Pastikan admin navbar diload (Termasuk Toast Notification) -->

    <div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
        
        <form action="{{ route('admin.products.stock.update') }}" method="POST" class="form-loading">
            @csrf
            
            <!-- Header (Sticky) -->
            <div data-animate="fade-down" class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4 bg-[#F9F9F9] sticky top-[76px] z-30 py-4 border-b border-gray-200">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-widest text-black">MANAJEMEN STOK</h1>
                    <p class="text-xs font-bold text-gray-400 mt-2">Ubah kuantitas stok per ukuran secara instan.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-black text-white px-8 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition shadow-md flex items-center justify-center">
                        Simpan Perubahan Stok
                    </button>
                </div>
            </div>

            <div data-animate="fade-up" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead class="bg-gray-50 border-b border-gray-100 text-[10px] font-black tracking-widest uppercase text-gray-500">
                        <tr>
                            <th class="py-4 px-6 w-1/3">Produk</th>
                            <th class="py-4 px-6 w-2/3">Input Stok per Ukuran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm font-medium">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            
                            <!-- Info Produk -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-4">
                                    @php $img = is_array($product->images) && count($product->images) > 0 ? $product->images[0] : ''; @endphp
                                    <img src="{{ asset('storage/produk/' . $img) }}" alt="{{ $product->name }}" class="w-14 h-16 rounded object-cover border border-gray-200 shrink-0">
                                    <div>
                                        <p class="font-black text-black uppercase tracking-widest text-xs mb-1">{{ $product->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 tracking-widest uppercase">{{ $product->type }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Grid Input Stok -->
                            <td class="py-5 px-6">
                                @php $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true); @endphp
                                
                                @if($sizes && count($sizes) > 0)
                                    <div class="flex flex-wrap gap-4">
                                        @foreach($sizes as $size)
                                        <div class="flex flex-col border border-gray-200 rounded-lg overflow-hidden bg-white w-20 shadow-sm focus-within:border-black focus-within:ring-1 focus-within:ring-black transition">
                                            <label class="bg-gray-50 text-[10px] font-black tracking-widest text-center py-1.5 border-b border-gray-200 text-black uppercase">
                                                {{ $size }}
                                            </label>
                                            <!-- Field Name Berbentuk Array Multi-dimensi -->
                                            <!-- Contoh: stocks[1][S] = 10 -->
                                            <input type="number" 
                                                   name="stocks[{{ $product->id }}][{{ $size }}]" 
                                                   value="{{ $product->stockFor($size) }}" 
                                                   min="0"
                                                   class="w-full text-center text-xs font-bold py-2 border-none focus:ring-0 text-gray-700">
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest bg-red-50 px-3 py-1.5 rounded">Tidak ada varian ukuran</span>
                                @endif
                            </td>
                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-12 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Belum ada produk di katalog.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                <p>Ubah angka pada kotak ukuran di atas, lalu klik "Simpan Perubahan Stok" di pojok kanan atas untuk memperbarui semua produk sekaligus.</p>
            </div>
        </form>
    </div>

    <!-- SCRIPT LOADING STATE (Mencegah Klik Ganda saat Save) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingForms = document.querySelectorAll('.form-loading');
            loadingForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.checkValidity()) {
                        const btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('opacity-75', 'cursor-not-allowed');
                            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> MEMPROSES...`;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>