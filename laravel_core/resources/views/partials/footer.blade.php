<footer class="bg-[#F9F9F9] border-t border-gray-200 pt-20 pb-10 mt-auto w-full">
    <div class="max-w-7xl mx-auto px-6 md:px-12">
        <div class="flex flex-col md:flex-row justify-between items-start gap-12 md:gap-10">
            <div>
                <h4 class="text-[11px] font-black tracking-widest uppercase text-black border-b border-black pb-1.5 inline-block mb-6">INFORMATION</h4>
                <p class="text-sm font-bold text-gray-900 mb-2">Senin – Jumat: 08.00 – 18.00 WIB</p>
                <p class="text-sm text-gray-500">Sabtu, Minggu & Hari Libur Nasional: <span class="text-red-500 font-bold">Libur / Offline</span></p>
            </div>
            <div class="md:text-right">
                <h4 class="text-[11px] font-black tracking-widest uppercase text-black border-b border-black pb-1.5 inline-block mb-6">SHIPPING & RETURNS</h4>
                <div class="space-y-3">
                    <a href="{{ route('page.pengiriman') }}" class="block text-sm text-gray-500 hover:text-black transition duration-300">Shipping</a>
                    <a href="{{ route('page.retur') }}" class="block text-sm text-gray-500 hover:text-black transition duration-300">Return Policy</a>
                    <a href="{{ route('page.kontak') }}" class="block text-sm text-gray-500 hover:text-black transition duration-300">Contact Us</a>
                </div>
            </div>
        </div>
        <hr class="border-gray-200 my-10">
        <div class="flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-400 font-medium">
            <p>© {{ date('Y') }} FROMHERE. All Rights Reserved.</p>
            <p class="mt-4 md:mt-0 font-bold tracking-[0.2em] uppercase text-gray-500">Built For Quality</p>
        </div>
    </div>
</footer>
