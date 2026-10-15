<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // nullOnDelete: kalau produknya dihapus dari katalog, baris histori
            // pesanan ini tetap ada (product_name di bawah jadi sumber tampilan).
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot detail produk saat dibeli, supaya kalau nama/harga produk
            // berubah belakangan, isi pesanan yang sudah dibuat tidak ikut berubah.
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('size')->nullable();
            $table->unsignedInteger('price');    // harga per item saat dibeli
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('subtotal'); // price * quantity

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
