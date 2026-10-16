<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Berat per satuan dalam GRAM. Dipakai untuk menghitung ongkir.
            // Produk lama otomatis dapat 300 gram (kaos rata-rata); admin bisa
            // mengubahnya per produk lewat form edit.
            $table->unsignedInteger('weight')->default(300);
        });

        Schema::table('orders', function (Blueprint $table) {
            // ID lokasi tujuan di sistem RajaOngkir, dan total berat paket (gram),
            // disimpan sebagai catatan ongkir yang dihitung saat checkout.
            $table->unsignedBigInteger('shipping_destination_id')->nullable();
            $table->unsignedInteger('total_weight')->nullable();
            // Estimasi lama pengiriman dari kurir, mis. "2-3 hari".
            $table->string('shipping_etd')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_destination_id', 'total_weight', 'shipping_etd']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
