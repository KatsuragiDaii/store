<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Kolom-kolom tambahan untuk Clothing
            $table->string('name'); // Nama baju
            $table->integer('price'); // Harga (angka murni tanpa titik)
            $table->string('type')->default('T-Shirt'); // Tipe produk
            $table->string('gender')->default('Unisex');
            $table->text('material')->nullable(); // Penjelasan detail bahan yang panjang
            $table->json('sizes')->nullable(); // Untuk menampung array ukuran ["S", "M", "L", "XL"]
            $table->json('images')->nullable(); // Untuk menampung array link gambar
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}