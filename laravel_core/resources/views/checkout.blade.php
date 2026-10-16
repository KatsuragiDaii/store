<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .logo-font { font-family: 'Times New Roman', Times, serif; font-style: italic; } </style>
    @if($shippingEnabled)
    <style>
        .ship-search-wrap { position: relative; }
        .ship-results { position: absolute; left: 0; right: 0; top: 100%; z-index: 30; margin-top: 4px; max-height: 260px; overflow-y: auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,.08); }
        .ship-results[hidden] { display: none; }
        .ship-result { display: block; width: 100%; text-align: left; padding: 10px 14px; font-size: 12px; line-height: 1.4; color: #111; background: #fff; border: 0; border-bottom: 1px solid #f3f4f6; cursor: pointer; }
        .ship-result:last-child { border-bottom: 0; }
        .ship-result:hover, .ship-result:focus { background: #f5f5f5; outline: none; }
        .ship-note { margin-top: 8px; font-size: 11px; font-weight: 600; color: #6b7280; }
        .ship-note:empty { display: none; }
        .ship-error { color: #dc2626; }
        .ship-options { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .ship-opt { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 10px; cursor: pointer; background: #fff; }
        .ship-opt:hover { border-color: #9ca3af; }
        .ship-opt:has(input:checked) { border-color: #000; box-shadow: 0 0 0 1px #000; }
        .ship-opt input { accent-color: #000; }
        .ship-opt-info { flex: 1; display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .ship-opt-info strong { font-size: 12px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: #000; }
        .ship-opt-info small { font-size: 11px; color: #6b7280; }
        .ship-opt-price { font-size: 12px; font-weight: 800; color: #000; white-space: nowrap; }
    </style>
    @endif
</head>
<body class="text-gray-900 antialiased bg-white flex flex-col min-h-screen">
    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Header -->
    <div data-animate="fade-down" class="bg-[#F9F9F9] pt-16 pb-14 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <h1 class="text-3xl md:text-[32px] font-black tracking-[0.15em] uppercase text-black mb-3">{{ $title }}</h1>
            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase">[{{ count($cart) }} ITEMS]</p>
        </div>
    </div>

    <main class="flex-grow max-w-7xl mx-auto px-6 md:px-12 py-16 w-full">
        @if ($errors->any())
        <div class="border border-red-200 bg-red-50 rounded-lg px-6 py-4 mb-10">
            <p class="text-[11px] font-black text-red-600 uppercase tracking-widest mb-1">Ada yang perlu diperbaiki</p>
            <ul class="text-xs text-red-600 font-bold space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- TAMBAHAN: Class form-loading untuk mendeteksi submit -->
        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="form-loading"
              @if($shippingEnabled) data-shipping="1" data-url-destinations="{{ route('shipping.destinations') }}" data-url-cost="{{ route('shipping.cost') }}" data-subtotal="{{ $subtotal }}" @endif>
            @csrf
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
                <!-- Kiri: Form Alamat Pengiriman -->
                <div data-animate="fade-left" class="flex-1 space-y-6">
                    <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-2">Alamat Pengiriman</h2>
                    
                    @if($primaryAddress)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Menggunakan Alamat Utama ({{ $primaryAddress->label }})
                        </p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nama Penerima</label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name', $primaryAddress->recipient_name ?? auth()->user()->name) }}" required 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Nomor HP</label>
                            <input type="tel" name="recipient_phone" value="{{ old('recipient_phone', $primaryAddress->phone ?? auth()->user()->phone) }}" required placeholder="08xxxxxxxxxx" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Alamat Lengkap</label>
                            <textarea name="shipping_address" required rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, patokan" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">{{ old('shipping_address', $primaryAddress->full_address ?? '') }}</textarea>
                        </div>
                        
                        @if(! $shippingEnabled)
                        <!-- Input Wilayah -->
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Provinsi</label>
                            <input type="text" name="shipping_province" value="{{ old('shipping_province', $primaryAddress->province ?? '') }}" required 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Kota/Kabupaten</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city', $primaryAddress->city ?? '') }}" required 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Kecamatan</label>
                            <input type="text" name="shipping_district" value="{{ old('shipping_district', $primaryAddress->district ?? '') }}" required 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Kode Pos</label>
                            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $primaryAddress->postal_code ?? '') }}" required 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                        @else
                        <!-- Ongkir otomatis: cari lokasi, lalu pilih kurir -->
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Kecamatan / Kelurahan / Kode Pos</label>
                            <div class="ship-search-wrap">
                                <input type="text" id="ship-search" autocomplete="off"
                                    value="{{ old('shipping_destination_label', trim(($primaryAddress->district ?? '').' '.($primaryAddress->city ?? ''))) }}"
                                    placeholder="Ketik min. 3 huruf, mis. Kramat Jati atau 13540"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                                <div id="ship-results" class="ship-results" hidden></div>
                            </div>
                            <input type="hidden" name="shipping_destination_id" id="ship-destination-id" value="{{ old('shipping_destination_id') }}">
                            <input type="hidden" name="shipping_destination_label" id="ship-destination-label" value="{{ old('shipping_destination_label') }}">
                            <p id="ship-selected" class="ship-note"></p>
                            <p id="ship-search-status" class="ship-note"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Layanan Pengiriman</label>
                            <p class="ship-note" style="margin-top:0">Berat paket &plusmn; {{ number_format(($cartWeight ?? 0) / 1000, 1, ',', '.') }} kg</p>
                            <input type="hidden" name="shipping_courier" id="ship-courier" value="{{ old('shipping_courier') }}">
                            <input type="hidden" name="shipping_service" id="ship-service" value="{{ old('shipping_service') }}">
                            <div id="ship-options" class="ship-options"></div>
                            <p id="ship-status" class="ship-note">Pilih lokasi tujuan dulu untuk melihat pilihan kurir.</p>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold tracking-widest text-gray-500 uppercase mb-2">Catatan (opsional)</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Titip di satpam, dsb." 
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-black focus:border-black">
                        </div>
                    </div>
                </div>

                <!-- Kanan: Ringkasan Pesanan -->
                <div data-animate="fade-right" data-animate-delay="100" class="w-full lg:w-[400px]">
                    <div class="bg-[#F9F9F9] p-8 rounded-2xl border border-gray-100 lg:sticky lg:top-24">
                        <h2 class="text-[11px] font-black tracking-widest uppercase text-black mb-6">Ringkasan Pesanan</h2>
                        
                        <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-1">
                            @foreach($cart as $item)
                            <div class="flex justify-between gap-3 text-xs">
                                <span class="font-bold text-gray-700">{{ $item['name'] }} ({{ $item['size'] }}) &times;{{ $item['quantity'] }}</span>
                                <span class="font-bold text-black shrink-0">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="space-y-4 text-xs font-bold border-t border-gray-200 pt-6 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-500">SUBTOTAL</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">ONGKOS KIRIM</span>
                                @if($shippingEnabled)
                                <span id="ship-cost-label" class="text-gray-400">Pilih kurir</span>
                                @elseif($shippingCost > 0)
                                <span>Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                @else
                                <span class="text-green-600">GRATIS</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-8">
                            <span class="text-[11px] font-black tracking-widest uppercase text-black">Total</span>
                            <span id="order-total" class="text-lg font-black text-black">Rp {{ number_format($subtotal + $shippingCost, 0, ',', '.') }}</span>
                        </div>
                        
                        <!-- Tombol Submit -->
                        <button type="submit" class="w-full bg-black text-white flex items-center justify-center py-4 rounded-lg text-[10px] font-black tracking-[0.2em] uppercase hover:bg-gray-900 transition shadow-md">
                            Lanjut ke Pembayaran &rarr;
                        </button>
                        
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-4 text-center leading-relaxed">
                            @if($shippingEnabled) Ongkos kirim dihitung otomatis sesuai berat paket dan tujuan. @else Pengiriman ditangani langsung oleh toko (offline), jadi gratis di checkout. @endif
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer Minimalis -->
    @include('partials.footer')

    @if($shippingEnabled)
    <!-- SCRIPT ONGKIR OTOMATIS -->
    <script>
    (function () {
        var form = document.getElementById('checkout-form');
        if (!form || form.dataset.shipping !== '1') return;

        var urlDest = form.dataset.urlDestinations;
        var urlCost = form.dataset.urlCost;
        var subtotal = Number(form.dataset.subtotal) || 0;
        var tokenInput = form.querySelector('input[name="_token"]');
        var token = tokenInput ? tokenInput.value : '';

        var el = {
            search: document.getElementById('ship-search'),
            results: document.getElementById('ship-results'),
            destId: document.getElementById('ship-destination-id'),
            destLabel: document.getElementById('ship-destination-label'),
            selected: document.getElementById('ship-selected'),
            searchStatus: document.getElementById('ship-search-status'),
            courier: document.getElementById('ship-courier'),
            service: document.getElementById('ship-service'),
            options: document.getElementById('ship-options'),
            status: document.getElementById('ship-status'),
            costLabel: document.getElementById('ship-cost-label'),
            total: document.getElementById('order-total')
        };
        var costs = [];
        var searchTimer = null, searchSeq = 0, costSeq = 0;
        var DEFAULT_STATUS = 'Pilih lokasi tujuan dulu untuk melihat pilihan kurir.';

        function rupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

        function setStatus(msg, isError) {
            el.status.textContent = msg || '';
            el.status.className = 'ship-note' + (isError ? ' ship-error' : '');
        }

        function parse(res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
                if (!res.ok) {
                    var msg = data.message || (data.errors && data.errors.q && data.errors.q[0]) || 'Terjadi kesalahan. Coba lagi.';
                    throw new Error(msg);
                }
                return data;
            });
        }

        function getJson(url) {
            return fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } }).then(parse);
        }

        function postJson(url, body) {
            return fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(body)
            }).then(parse);
        }

        function labelOf(d) {
            return d.label || [d.subdistrict, d.district, d.city, d.province, d.zip].filter(Boolean).join(', ');
        }

        function resetSelection() {
            el.courier.value = '';
            el.service.value = '';
            if (el.costLabel) { el.costLabel.textContent = 'Pilih kurir'; el.costLabel.className = 'text-gray-400'; }
            el.total.textContent = rupiah(subtotal);
        }

        function selectOption(o) {
            el.courier.value = o.courier;
            el.service.value = o.service;
            el.costLabel.textContent = rupiah(o.cost);
            el.costLabel.className = '';
            el.total.textContent = rupiah(subtotal + o.cost);
            setStatus('');
        }

        function renderOptions(preCourier, preService) {
            el.options.textContent = '';
            costs.forEach(function (o, i) {
                var label = document.createElement('label');
                label.className = 'ship-opt';
                var radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = 'ship_opt';
                radio.value = String(i);
                var info = document.createElement('span');
                info.className = 'ship-opt-info';
                var title = document.createElement('strong');
                title.textContent = o.courier_name + ' ' + o.service;
                var meta = document.createElement('small');
                meta.textContent = [o.description, o.etd ? 'Estimasi ' + o.etd : ''].filter(Boolean).join(' · ');
                info.appendChild(title);
                info.appendChild(meta);
                var price = document.createElement('span');
                price.className = 'ship-opt-price';
                price.textContent = rupiah(o.cost);
                label.appendChild(radio);
                label.appendChild(info);
                label.appendChild(price);
                radio.addEventListener('change', function () { selectOption(o); });
                el.options.appendChild(label);
                if (preCourier && o.courier === preCourier && o.service === preService) {
                    radio.checked = true;
                    selectOption(o);
                }
            });
        }

        function loadCosts(id, preCourier, preService) {
            var seq = ++costSeq;
            costs = [];
            el.options.textContent = '';
            resetSelection();
            setStatus('Menghitung ongkos kirim…');
            postJson(urlCost, { destination_id: Number(id) }).then(function (data) {
                if (seq !== costSeq) return;
                costs = data.options || [];
                if (!costs.length) { setStatus('Belum ada layanan pengiriman yang melayani tujuan ini.', true); return; }
                setStatus('Pilih salah satu layanan pengiriman:');
                renderOptions(preCourier, preService);
                if (el.courier.value) setStatus('');
            }).catch(function (err) {
                if (seq !== costSeq) return;
                setStatus(err.message, true);
            });
        }

        function chooseDestination(d) {
            var label = labelOf(d);
            el.destId.value = d.id;
            el.destLabel.value = label;
            el.search.value = label;
            el.results.hidden = true;
            el.searchStatus.textContent = '';
            el.selected.textContent = 'Tujuan: ' + label;
            loadCosts(d.id);
        }

        function renderResults(list) {
            el.results.textContent = '';
            if (!list.length) {
                el.results.hidden = true;
                el.searchStatus.textContent = 'Lokasi tidak ditemukan. Coba nama kecamatan atau kode pos lain.';
                return;
            }
            el.searchStatus.textContent = '';
            list.forEach(function (d) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'ship-result';
                b.textContent = labelOf(d);
                b.addEventListener('click', function () { chooseDestination(d); });
                el.results.appendChild(b);
            });
            el.results.hidden = false;
        }

        el.search.addEventListener('input', function () {
            // Teks diubah setelah memilih lokasi: pilihan lama tidak berlaku lagi.
            if (el.destId.value && el.search.value !== el.destLabel.value) {
                el.destId.value = '';
                el.destLabel.value = '';
                el.selected.textContent = '';
                costSeq++;
                costs = [];
                el.options.textContent = '';
                resetSelection();
                setStatus(DEFAULT_STATUS);
            }

            clearTimeout(searchTimer);
            var q = el.search.value.trim();
            if (q.length < 3) { el.results.hidden = true; el.searchStatus.textContent = ''; return; }

            searchTimer = setTimeout(function () {
                var seq = ++searchSeq;
                el.searchStatus.textContent = 'Mencari…';
                getJson(urlDest + '?q=' + encodeURIComponent(q)).then(function (data) {
                    if (seq !== searchSeq) return;
                    renderResults(data.data || []);
                }).catch(function (err) {
                    if (seq !== searchSeq) return;
                    el.results.hidden = true;
                    el.searchStatus.textContent = err.message;
                });
            }, 350);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.ship-search-wrap')) el.results.hidden = true;
        });

        // Jangan biarkan form terkirim sebelum lokasi & kurir dipilih.
        // Didaftarkan lebih dulu dari skrip loading bawaan, jadi bisa menghentikannya.
        form.addEventListener('submit', function (e) {
            var msg = '';
            if (!el.destId.value) msg = 'Cari dan pilih lokasi tujuan dari daftar hasil pencarian.';
            else if (!el.courier.value || !el.service.value) msg = 'Pilih layanan pengiriman dulu.';
            if (msg) {
                e.preventDefault();
                e.stopImmediatePropagation();
                setStatus(msg, true);
                el.status.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Halaman dimuat ulang setelah error validasi: pulihkan pilihan sebelumnya.
        if (el.destId.value) {
            el.selected.textContent = el.destLabel.value ? 'Tujuan: ' + el.destLabel.value : '';
            loadCosts(el.destId.value, el.courier.value, el.service.value);
        }
    })();
    </script>
    @endif

    <!-- SCRIPT LOADING STATE -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingForms = document.querySelectorAll('.form-loading');
            loadingForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Cek apakah inputan di form valid sebelum disable (agar alert 'required' browser tetap jalan)
                    if (form.checkValidity()) {
                        const btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('opacity-75', 'cursor-not-allowed');
                            
                            // SVG Spinner Tailwind
                            btn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                MEMPROSES...
                            `;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>