<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen relative">
    
    <!-- Navbar (Sudah termasuk Search Dropdown, Menu Mobile, dan Toast) -->
    @include('partials.navbar')

    @php
        // Cek apakah produk ini sudah ada di wishlist user yang sedang login
        $inWishlist = auth()->check() ? auth()->user()->wishlists()->where('product_id', $product->id)->exists() : false;
    @endphp

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-10 w-full">

        <!-- Breadcrumbs -->
        <div class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-10 flex space-x-2">
            <a href="/" class="hover:text-black transition">Home</a><span>/</span>
            <a href="{{ route('products') }}" class="hover:text-black transition">Products</a><span>/</span>
            <span class="text-black">{{ $product->name }}</span>
        </div>

        <div class="flex flex-col md:flex-row gap-12 lg:gap-20">
            
            <!-- Kiri: Area Gambar (Slider) -->
            <div data-animate="fade-left" class="md:w-1/2 relative group">
                @php
                    $images = is_array($product->images) ? $product->images : json_decode($product->images, true);
                    $totalImages = count($images);
                    $firstImage = $totalImages > 0 ? $images[0] : '';
                @endphp
                
                <div class="w-full aspect-[3/4] bg-gray-100 rounded-3xl overflow-hidden relative shadow-sm">
                    <div id="imageCounter" class="absolute top-5 right-5 bg-black text-white px-4 py-1.5 text-[10px] font-bold rounded-full z-10 tracking-widest shadow-md">
                        1 / {{ $totalImages > 0 ? $totalImages : 1 }}
                    </div>
                    
                    <!-- BUG FIX: Menggunakan storage/produk/ -->
                    <img id="mainImage" src="{{ asset('storage/produk/' . $firstImage) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition duration-500">
                    
                    @if($totalImages > 1)
                    <button onclick="prevImage()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 text-black p-3 rounded-full opacity-0 group-hover:opacity-100 transition shadow-lg hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button onclick="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 text-black p-3 rounded-full opacity-0 group-hover:opacity-100 transition shadow-lg hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Kanan: Area Info Produk -->
            <div data-animate="fade-right" data-animate-delay="100" class="md:w-1/2 flex flex-col justify-center">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight uppercase text-black mb-2 leading-none">{{ $product->name }}</h1>
                
                <div class="flex items-center space-x-3 mb-6">
                    @if($product->discount_price)
                        <p class="text-xl font-black text-red-600">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</p>
                        <p class="text-sm font-semibold text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @else
                        <p class="text-xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @endif
                </div>

                <div class="flex flex-col gap-4 mt-8">
                    <!-- Form 1: Add to Cart (Ditambahkan class form-loading) -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full form-loading">
                        @csrf
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-[10px] font-black tracking-widest uppercase text-black">Select Size</span>
                            <button type="button" onclick="openSizeGuide()" class="text-[9px] font-bold tracking-widest text-gray-400 uppercase hover:text-black transition">Size Guide</button>
                        </div>
                        
                        @if ($errors->has('stock'))
                            <p class="text-[11px] font-bold text-red-600 uppercase tracking-wide mb-4">{{ $errors->first('stock') }}</p>
                        @endif

                        <!-- Pilihan Ukuran -->
                        <div class="flex flex-wrap gap-3 mb-8">
                            @php
                                $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                $firstAvailable = $product->tracksStock() ? collect($sizes)->first(fn ($s) => $product->stockFor($s) > 0) : null;
                            @endphp
                            @if($sizes)
                                @foreach($sizes as $size)
                                @php $outOfStock = $product->tracksStock() && $product->stockFor($size) <= 0; @endphp
                                <label class="{{ $outOfStock ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                    <input type="radio" name="size" value="{{ $size }}" class="peer sr-only" {{ $outOfStock ? 'disabled' : 'required' }} {{ ($product->tracksStock() ? $size === $firstAvailable : $loop->first) ? 'checked' : '' }}>
                                    <div class="w-14 h-12 flex items-center justify-center border rounded text-[11px] font-bold transition {{ $outOfStock ? 'border-gray-100 text-gray-300 line-through' : 'border-gray-200 text-gray-900 peer-checked:bg-black peer-checked:text-white peer-checked:border-black hover:border-black' }}">
                                        {{ $size }}
                                    </div>
                                </label>
                                @endforeach
                            @endif
                        </div>

                        <div class="flex items-center gap-4 mb-4">
                            <!-- Kontrol Jumlah (Quantity) -->
                            <div class="flex items-center border border-gray-200 rounded h-14 px-2 w-32 shrink-0">
                                <button type="button" onclick="document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value) - 1)" class="w-10 h-full flex justify-center items-center text-gray-500 hover:text-black transition">-</button>
                                <input type="number" name="quantity" id="qty" value="1" min="1" class="w-full text-center text-xs font-bold border-none focus:ring-0 p-0" readonly>
                                <button type="button" onclick="document.getElementById('qty').value = parseInt(document.getElementById('qty').value) + 1" class="w-10 h-full flex justify-center items-center text-gray-500 hover:text-black transition">+</button>
                            </div>
                            
                            <!-- Tombol Add to Cart -->
                            <button type="submit" class="flex-1 bg-black text-white h-14 rounded text-[11px] font-black tracking-widest uppercase hover:bg-gray-900 transition shadow-md flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span>ADD TO CART</span>
                            </button>
                        </div>
                    </form>

                    <!-- Form 2: Toggle Wishlist (Ditambahkan class form-loading) -->
                    <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="w-full form-loading">
                        @csrf
                        @if(auth()->check())
                            <button type="submit" class="w-full h-14 flex items-center justify-center border {{ $inWishlist ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-200 text-gray-500 hover:border-black' }} rounded transition text-[11px] font-black tracking-widest uppercase">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                                <span>{{ $inWishlist ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="w-full h-14 flex items-center justify-center border border-gray-200 text-gray-500 hover:border-black rounded transition text-[11px] font-black tracking-widest uppercase">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                                Simpan ke Wishlist
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Product Details Accordion -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <button onclick="toggleDetails()" class="w-full flex justify-between items-center text-[10px] font-black tracking-widest uppercase text-black focus:outline-none">
                        <span>Product Details</span>
                        <svg id="detailsArrow" class="w-4 h-4 transform transition-transform duration-300 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="detailsContent" class="mt-6 text-xs text-gray-500 uppercase tracking-wide leading-loose transition-all duration-300 block">
                        {!! nl2br(e($product->material ?? "MATERIAL: HEAVYWEIGHT AUSTRALIA 16S 235GSM\nFIT: BOXY FIT (RELAXED)\nNECK TYPE: CREWNECK\nSLEEVE: SET-IN SHORT SLEEVE")) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ulasan Pembeli Section (Dinamis dari Database) -->
        <div class="mt-32 border-t border-gray-200 pt-16 mb-16">
            <h2 data-animate="fade-up" class="text-lg font-black tracking-widest uppercase text-black mb-4">Ulasan Pembeli</h2>
            
            @php
                $avgRating = round($product->averageRating(), 1);
                $totalReviews = $product->reviews->count();
            @endphp

            <div data-animate="fade-up" class="flex items-center space-x-3 mb-8">
                <div class="flex items-center text-yellow-400">
                    <!-- Bintang Rata-rata -->
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-black' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                </div>
                <p class="text-xs font-bold tracking-widest uppercase text-gray-500">{{ $avgRating }} / 5.0 <span class="text-gray-300 mx-2">|</span> {{ $totalReviews }} Ulasan</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Daftar Ulasan -->
                <div class="flex-1 space-y-8">
                    @if($totalReviews === 0)
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Belum ada review untuk produk ini.</p>
                    @else
                        @foreach($product->reviews as $review)
                        <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 60 }}" class="border-b border-gray-100 pb-8">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-xs font-black text-black uppercase tracking-widest">{{ $review->user->name }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-black' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    @endif
                </div>

                <!-- Form Tambah Ulasan -->
                <div data-animate="fade-left" class="w-full lg:w-1/3">
                    @if(isset($canReview) && $canReview)
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <h3 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Tulis Ulasan Anda</h3>
                            
                            @if ($errors->has('review'))
                                <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mb-4">{{ $errors->first('review') }}</p>
                            @endif

                            <form action="{{ route('reviews.store', $product->id) }}" method="POST" class="form-loading">
                                @csrf
                                <div class="mb-5">
                                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-3">Penilaian (Bintang)</label>
                                    <select name="rating" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs font-bold text-black focus:ring-black">
                                        <option value="5">5 Bintang - Sangat Bagus</option>
                                        <option value="4">4 Bintang - Bagus</option>
                                        <option value="3">3 Bintang - Cukup</option>
                                        <option value="2">2 Bintang - Kurang</option>
                                        <option value="1">1 Bintang - Sangat Kurang</option>
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-3">Komentar & Pengalaman</label>
                                    <textarea name="comment" rows="4" required placeholder="Ceritakan pengalaman Anda memakai produk ini..." class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-black text-white py-3.5 rounded-lg text-[10px] font-black tracking-widest uppercase hover:bg-gray-900 transition flex justify-center items-center">
                                    <span>Kirim Ulasan</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                                Hanya pelanggan yang telah menerima pesanan produk ini yang dapat meninggalkan ulasan.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </main>

    <!-- Modal Size Guide -->
    <div id="sizeGuideModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeSizeGuide()"></div>
        <div class="relative bg-white rounded-2xl w-full max-w-[420px] mx-4 p-8 shadow-2xl transform transition-transform scale-95 opacity-0" id="modalContent">
            <button onclick="closeSizeGuide()" class="absolute top-6 right-6 text-gray-400 hover:text-black transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-sm font-black tracking-widest uppercase text-black mb-1">Panduan Ukuran</h3>
            <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-8">T-Shirt & Apparel</p>
            <div class="border border-gray-100 rounded-xl overflow-hidden mb-8">
                <table class="w-full text-xs text-center">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="py-4 font-bold tracking-widest uppercase text-black w-1/3">Size</th>
                            <th class="py-4 font-bold tracking-widest uppercase text-black w-1/3">Lebar Dada</th>
                            <th class="py-4 font-bold tracking-widest uppercase text-black w-1/3">Panjang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-bold text-gray-500">
                        <tr><td class="py-4 text-black">S</td><td class="py-4">50 CM</td><td class="py-4">70 CM</td></tr>
                        <tr><td class="py-4 text-black">M</td><td class="py-4">53 CM</td><td class="py-4">72 CM</td></tr>
                        <tr><td class="py-4 text-black">L</td><td class="py-4">56 CM</td><td class="py-4">74 CM</td></tr>
                        <tr><td class="py-4 text-black">XL</td><td class="py-4">59 CM</td><td class="py-4">76 CM</td></tr>
                    </tbody>
                </table>
            </div>
            <button onclick="closeSizeGuide()" class="w-full bg-black text-white py-4 rounded-lg font-bold text-[10px] tracking-widest uppercase hover:bg-gray-800 transition shadow-md">
                Tutup Panduan
            </button>
        </div>
    </div>

    <!-- Footer Minimalis -->
    @include('partials.footer')

    <!-- SCRIPT INTERAKTIF (VANILLA JS) -->
    <script>
        // 1. Logika Slider Gambar (Menggunakan storage/produk/)
        const images = {!! json_encode($images) !!};
        const assetUrl = "{{ asset('storage/produk') }}/";
        let currentImgIdx = 0;
        
        function updateImageDisplay() {
            if(images.length > 0) {
                const imgEl = document.getElementById('mainImage');
                imgEl.style.opacity = '0.5';
                setTimeout(() => {
                    imgEl.src = assetUrl + images[currentImgIdx];
                    imgEl.style.opacity = '1';
                }, 150);
                document.getElementById('imageCounter').innerText = (currentImgIdx + 1) + ' / ' + images.length;
            }
        }
        function nextImage() {
            if(images.length > 0) {
                currentImgIdx = (currentImgIdx + 1) % images.length;
                updateImageDisplay();
            }
        }
        function prevImage() {
            if(images.length > 0) {
                currentImgIdx = (currentImgIdx - 1 + images.length) % images.length;
                updateImageDisplay();
            }
        }

        // 2. Logika Modal Size Guide
        const modal = document.getElementById('sizeGuideModal');
        const modalContent = document.getElementById('modalContent');
        function openSizeGuide() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        function closeSizeGuide() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // 3. Logika Accordion Product Details
        let detailsOpen = true; 
        function toggleDetails() {
            detailsOpen = !detailsOpen;
            const content = document.getElementById('detailsContent');
            const arrow = document.getElementById('detailsArrow');
            
            if (detailsOpen) {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }

        // 4. Logika Loading State pada form submission (Mencegah Klik Ganda)
        document.addEventListener('DOMContentLoaded', function() {
            const loadingForms = document.querySelectorAll('.form-loading');
            loadingForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.checkValidity()) {
                        const btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('opacity-75', 'cursor-not-allowed');
                            
                            // Ganti isi tombol dengan spinner
                            btn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-current inline-block shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>MEMPROSES...</span>
                            `;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>