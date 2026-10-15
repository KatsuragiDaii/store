<nav class="sticky top-0 w-full bg-white px-6 md:px-12 py-5 z-40 border-b border-gray-100">
    <div class="flex justify-between items-center w-full">
        <!-- Logo Kiri & Hamburger Menu (Mobile) -->
        <div class="flex-1 flex justify-start items-center">
            <!-- Tombol Hamburger (Hanya muncul di Mobile) -->
            <button type="button" id="mobileMenuBtn" class="mr-4 md:hidden text-gray-800 hover:text-black focus:outline-none transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            
            <a href="/" class="text-3xl font-bold logo-font tracking-tighter text-black">FROMHERE</a>
        </div>
        
        <!-- Menu Tengah (Hanya muncul di Desktop) -->
        <div class="absolute left-1/2 transform -translate-x-1/2 hidden md:flex space-x-12 text-[10px] font-bold tracking-[0.2em] text-gray-400">
            <a href="{{ route('men') }}" class="hover:text-black transition duration-300 {{ request()->routeIs('men') ? 'text-black' : '' }}">MEN</a>
            <a href="{{ route('women') }}" class="hover:text-black transition duration-300 {{ request()->routeIs('women') ? 'text-black' : '' }}">WOMEN</a>
            <a href="{{ route('collections') }}" class="hover:text-black transition duration-300 {{ request()->routeIs('collections') ? 'text-black' : '' }}">COLLECTIONS</a>
            <a href="{{ route('sale') }}" class="hover:text-black transition duration-300 {{ request()->routeIs('sale') ? 'text-black' : '' }}">SALE</a>
        </div>
        
        <!-- Ikon Kanan -->
        <div class="flex-1 flex justify-end items-center space-x-5 md:space-x-6 text-gray-800">
            
            <!-- Ikon Search (Membuka Dropdown) -->
            <button type="button" id="openSearchBtn" class="hover:text-black transition text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path></svg>
            </button>
            
            @auth
                <!-- Ikon Akun (User / Admin) -->
                <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('account.dashboard') }}" 
                   class="hover:text-black transition {{ request()->routeIs('account.*') || request()->routeIs('orders.*') || request()->routeIs('admin.*') ? 'text-black' : 'text-gray-400' }}" 
                   title="Akun Saya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
                </a>
                
                <!-- Ikon Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="hover:text-black transition relative flex items-center {{ request()->routeIs('wishlist.*') ? 'text-black' : 'text-gray-400' }}" title="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                    @if(auth()->user()->wishlists()->count() > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-black text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full z-10">{{ auth()->user()->wishlists()->count() }}</span>
                    @endif
                </a>
            @else
                <!-- Ikon Akun (Guest) -->
                <a href="{{ route('login') }}" class="hover:text-black transition {{ request()->routeIs('login') || request()->routeIs('register') ? 'text-black' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
                </a>
                
                <!-- Ikon Wishlist (Guest) -->
                <a href="{{ route('login') }}" class="hover:text-black transition text-gray-400" title="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path></svg>
                </a>
            @endauth
            
            <!-- Ikon Keranjang -->
            <a href="{{ route('cart.index') }}" class="hover:text-black transition relative flex items-center {{ request()->routeIs('cart.*') || request()->routeIs('checkout') ? 'text-black' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                @if(session()->has('cart') && count(session('cart')) > 0)
                <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full z-10">{{ count(session('cart')) }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Overlay Pencarian -->
    <div id="searchOverlay" class="absolute top-full left-0 w-full bg-white shadow-xl transition-all duration-300 ease-in-out transform origin-top opacity-0 scale-y-0 pointer-events-none border-b border-gray-100 z-30">
        <div class="max-w-3xl mx-auto px-6 py-12 relative">
            <form action="{{ route('products') }}" method="GET" class="w-full flex items-center border-b-2 border-gray-200 focus-within:border-black transition duration-300 pb-2">
                <svg class="w-6 h-6 text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path></svg>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari produk, misal 'tee' atau 'sandal'..." class="w-full bg-transparent border-none focus:ring-0 text-lg md:text-xl font-medium text-gray-900 placeholder-gray-400 px-0 outline-none">
                <button type="button" id="closeSearchBtn" class="ml-4 text-gray-400 hover:text-black transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- MOBILE MENU (SLIDE OVER) -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300 ease-in-out"></div>
<div id="mobileMenuSidebar" class="fixed top-0 left-0 w-72 h-full bg-white z-[70] transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
        <span class="text-3xl font-bold logo-font tracking-tighter text-black">Z</span>
        <button type="button" id="closeMobileMenuBtn" class="text-gray-400 hover:text-black transition p-2 -mr-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <div class="flex flex-col px-6 py-8 space-y-8 text-xs font-bold tracking-[0.2em] text-gray-400 flex-grow">
        <a href="{{ route('men') }}" class="hover:text-black transition {{ request()->routeIs('men') ? 'text-black' : '' }}">MEN</a>
        <a href="{{ route('women') }}" class="hover:text-black transition {{ request()->routeIs('women') ? 'text-black' : '' }}">WOMEN</a>
        <a href="{{ route('collections') }}" class="hover:text-black transition {{ request()->routeIs('collections') ? 'text-black' : '' }}">COLLECTIONS</a>
        <a href="{{ route('sale') }}" class="hover:text-black transition {{ request()->routeIs('sale') ? 'text-black' : '' }}">SALE</a>
    </div>
    <div class="px-6 py-8 border-t border-gray-100 bg-gray-50">
        @auth
            <div class="mb-4">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Signed in as</p>
                <p class="text-xs font-black text-black uppercase tracking-widest truncate">{{ auth()->user()->name }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-[10px] font-bold tracking-[0.2em] text-red-500 hover:text-red-700 transition uppercase">LOGOUT</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-[10px] font-bold tracking-[0.2em] text-black transition uppercase">LOGIN / REGISTER</a>
        @endauth
    </div>
</div>

<!-- ========================================== -->
<!-- GLOBAL TOAST NOTIFICATION                  -->
<!-- ========================================== -->
@if(session('success'))
<div id="toast-success" class="fixed bottom-8 right-8 z-[100] transform transition-all duration-500 translate-y-0 opacity-100 flex items-center bg-black text-white px-6 py-4 rounded-xl shadow-2xl">
    <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span class="text-[10px] font-bold tracking-widest uppercase leading-relaxed">{{ session('success') }}</span>
    <button type="button" onclick="document.getElementById('toast-success').style.display='none'" class="ml-6 text-gray-400 hover:text-white transition shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
@endif

<!-- SCRIPT INTERAKTIF (SEARCH, MOBILE MENU, & TOAST) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LOGIKA GLOBAL TOAST AUTO-HIDE ---
        const toast = document.getElementById('toast-success');
        if (toast) {
            setTimeout(() => {
                // Menganimasikan toast turun ke bawah & menghilang
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-10', 'opacity-0');
                // Menghapus elemen dari DOM setelah animasi selesai
                setTimeout(() => toast.remove(), 500);
            }, 3000); // Tampil selama 3 detik
        }

        // --- LOGIKA PENCARIAN (SEARCH) ---
        const openSearchBtn = document.getElementById('openSearchBtn');
        const closeSearchBtn = document.getElementById('closeSearchBtn');
        const searchOverlay = document.getElementById('searchOverlay');
        const searchInput = document.getElementById('searchInput');

        function openSearch() {
            searchOverlay.classList.remove('opacity-0', 'scale-y-0', 'pointer-events-none');
            searchOverlay.classList.add('opacity-100', 'scale-y-100');
            setTimeout(() => searchInput.focus(), 300);
        }

        function closeSearch() {
            searchOverlay.classList.remove('opacity-100', 'scale-y-100');
            searchOverlay.classList.add('opacity-0', 'scale-y-0', 'pointer-events-none');
        }

        if(openSearchBtn && closeSearchBtn && searchOverlay) {
            openSearchBtn.addEventListener('click', openSearch);
            closeSearchBtn.addEventListener('click', closeSearch);
        }

        // --- LOGIKA MOBILE MENU (HAMBURGER) ---
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileMenuSidebar = document.getElementById('mobileMenuSidebar');

        function openMobileMenu() {
            mobileMenuOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileMenuOverlay.classList.remove('opacity-0');
                mobileMenuSidebar.classList.remove('-translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden'; 
        }

        function closeMobileMenu() {
            mobileMenuOverlay.classList.add('opacity-0');
            mobileMenuSidebar.classList.add('-translate-x-full');
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = ''; 
        }

        if(mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileMenu);
        if(closeMobileMenuBtn) closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
        if(mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    });
</script>