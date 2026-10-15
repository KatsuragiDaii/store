<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Koleksi - FROMHERE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F9F9F9] min-h-screen flex items-center justify-center py-12 px-6">
    <div data-animate="zoom-in" class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-xl">
        <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-bold tracking-[0.2em] uppercase text-gray-400 hover:text-black transition block mb-6">&larr; Dashboard Admin</a>
        <h1 class="text-2xl font-black tracking-widest uppercase mb-8 text-center text-black">Tambah Koleksi</h1>
        <form action="{{ route('collections.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Nama Koleksi di Card</label>
                <input type="text" name="name" placeholder="FROMHERE T-SHIRT COLLECTION" required class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-xs">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Target Tipe Produk</label>
                <input type="text" name="slug" placeholder="T-Shirt" required class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-black focus:border-black text-xs">
                <p class="text-[10px] text-gray-400 mt-1.5 font-bold uppercase tracking-wider">*Harus sama dengan nama Tipe Produk agar filternya berfungsi.</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-gray-700 uppercase">Gambar Card Koleksi</label>
                <input type="file" name="image" required class="mt-2 block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:tracking-widest file:uppercase file:bg-gray-100 file:text-black hover:file:bg-gray-200 cursor-pointer">
            </div>
            <button type="submit" class="w-full bg-black text-white py-4 rounded-lg font-bold text-[10px] tracking-[0.2em] uppercase hover:bg-gray-800 transition shadow-md mt-4">Simpan Koleksi</button>
        </form>
    </div>
</body>
</html>