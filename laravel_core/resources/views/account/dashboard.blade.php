<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard - FROMHERE</title>
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
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[ WELCOME BACK, {{ explode(' ', $user->name)[0] }} ]</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full flex flex-col md:flex-row gap-12 lg:gap-20">
        
        <!-- ========================================== -->
        <!-- SIDEBAR NAVIGATION (DIKEMBALIKAN UTUH)     -->
        <!-- ========================================== -->
        <aside data-animate="fade-left" class="w-full md:w-64 flex-shrink-0">
            <h3 class="text-[10px] font-black tracking-widest uppercase text-gray-400 mb-4 pl-4">ACCOUNT NAVIGATION</h3>
            <div class="space-y-1">
                
                <!-- Active Link: Biodata Diri & Alamat -->
                <a href="{{ route('account.dashboard') }}" class="flex items-center justify-between px-4 py-3.5 bg-black text-white rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">PROFIL & ALAMAT</span>
                    </div>
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
                
                <!-- Link: Daftar Transaksi -->
                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-4 py-3.5 text-gray-500 hover:text-black hover:bg-gray-50 rounded-lg transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">DAFTAR TRANSAKSI</span>
                    </div>
                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>

                @if($user->is_admin)
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
                <form action="{{ route('logout') }}" method="POST" class="px-4 form-loading">
                    @csrf
                    <button type="submit" class="flex items-center space-x-3 w-full text-left py-3.5 text-red-500 hover:text-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"></path></svg>
                        <span class="text-[11px] font-bold tracking-widest uppercase">KELUAR DARI AKUN</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- CONTENT: DETAIL BIODATA & ALAMAT           -->
        <!-- ========================================== -->
        <div class="flex-1 space-y-10">
            
            @if($errors->any())
                <div class="bg-red-50 text-red-600 text-xs font-bold uppercase tracking-widest px-6 py-4 rounded-xl border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div data-animate="fade-right" data-animate-delay="100" class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                
                <!-- Section: Detail Biodata Diri & Email -->
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">| PENGATURAN PROFIL & EMAIL</h2>
                <form action="{{ route('account.updateProfile') }}" method="POST" class="space-y-5 mb-12 form-loading">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                        <div>
                            <!-- Email Read-Only -->
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Email Address</label>
                            <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nomor Handphone Pribadi</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                    </div>
                    <button type="submit" class="bg-black text-white flex items-center justify-center px-6 py-3 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition w-full md:w-auto">
                        <span>Simpan Profil</span>
                    </button>
                </form>

                <!-- Section: Ganti Password (akun Google yang belum punya password => "Buat Password") -->
                @php $hasPassword = filled($user->password); @endphp
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6 border-t border-gray-100 pt-10">| {{ $hasPassword ? 'GANTI PASSWORD' : 'BUAT PASSWORD' }}</h2>
                @unless($hasPassword)
                    <p class="text-xs text-gray-500 leading-relaxed mb-6">Akun ini masuk lewat Google dan belum punya password. Buat password agar kamu juga bisa login dengan email dan password.</p>
                @endunless
                <form action="{{ route('account.updatePassword') }}" method="POST" class="space-y-5 mb-12 form-loading">
                    @csrf
                    <div class="grid grid-cols-1 gap-5">
                        @if($hasPassword)
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Password Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black">
                        </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Password Baru</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="bg-white border border-black text-black flex items-center justify-center px-6 py-3 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-100 transition w-full md:w-auto">
                        <span>{{ $hasPassword ? 'Update Password' : 'Buat Password' }}</span>
                    </button>
                </form>

                <!-- Section: Daftar Alamat -->
                <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6 border-t border-gray-100 pt-10">| DAFTAR ALAMAT PENGIRIMAN</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    @forelse($addresses as $address)
                    <div data-animate="fade-up" data-animate-delay="{{ $loop->index * 70 }}" class="border {{ $address->is_primary ? 'border-black bg-gray-50' : 'border-gray-200' }} rounded-xl p-5 relative">
                        @if($address->is_primary)
                        <span class="absolute top-4 right-4 bg-black text-white text-[8px] font-bold px-2 py-1 rounded uppercase tracking-widest">Utama</span>
                        @endif
                        <h3 class="text-xs font-black uppercase tracking-widest mb-1">{{ $address->label }}</h3>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">{{ $address->recipient_name }} - {{ $address->phone }}</p>
                        <p class="text-xs text-gray-600 leading-relaxed mb-5">{{ $address->full_address }}, Kec. {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                        
                        <div class="flex items-center space-x-3 border-t border-gray-200 pt-3">
                            @if(!$address->is_primary)
                            <form action="{{ route('account.setPrimaryAddress', $address->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[9px] font-bold text-gray-400 hover:text-black uppercase tracking-widest transition">Jadikan Utama</button>
                            </form>
                            @endif
                            <form action="{{ route('account.destroyAddress', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[9px] font-bold text-red-400 hover:text-red-600 uppercase tracking-widest transition">Hapus</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-6 text-center border border-dashed border-gray-300 rounded-xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Belum ada alamat tersimpan.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Form Tambah Alamat -->
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <h3 class="text-[10px] font-black tracking-widest uppercase text-black mb-4">Tambah Alamat Baru</h3>
                    <form action="{{ route('account.storeAddress') }}" method="POST" class="space-y-4 form-loading">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><input type="text" name="label" placeholder="Label (Rumah/Kantor)" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="text" name="recipient_name" placeholder="Nama Penerima" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="tel" name="phone" placeholder="No HP Penerima" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="text" name="province" placeholder="Provinsi" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="text" name="city" placeholder="Kota/Kabupaten" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="text" name="district" placeholder="Kecamatan" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div><input type="text" name="postal_code" placeholder="Kode Pos" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></div>
                            <div class="md:col-span-2">
                                <textarea name="full_address" rows="2" placeholder="Alamat Lengkap (Jalan, RT/RW)" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-xs focus:ring-black"></textarea>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 pt-2">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 text-black focus:ring-black">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jadikan Alamat Utama</span>
                        </div>
                        <button type="submit" class="bg-black text-white flex items-center justify-center px-6 py-3 rounded-lg text-[10px] font-black tracking-widest uppercase hover:bg-gray-900 transition mt-4 w-full md:w-auto">
                            <span>Simpan Alamat</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Minimalis -->
    @include('partials.footer')

    <!-- SCRIPT LOADING STATE -->
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