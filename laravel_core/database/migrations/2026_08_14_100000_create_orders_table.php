<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Sama juga dipakai sebagai order_id saat request Snap Token ke Midtrans,
            // jadi begitu order dibuat nilainya tidak boleh diubah lagi.
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // String biasa (bukan enum di level DB) supaya gampang migrasi ke
            // MySQL/PostgreSQL nanti. Nilai valid ada di App\Enums\OrderStatus.
            $table->string('status')->default('pending')->index();

            // Rupiah tanpa desimal, konsisten dengan kolom price di tabel products.
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedInteger('total');

            // Pembayaran (Midtrans)
            $table->string('payment_method')->nullable(); // ex: gopay, bank_transfer, qris
            $table->text('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Pengiriman
            $table->string('courier')->nullable();        // ex: JNE, J&T
            $table->string('courier_service')->nullable(); // ex: REG, YES
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Snapshot alamat pengiriman saat checkout — sengaja disalin langsung ke
            // sini, bukan relasi ke tabel addresses. Kalau pembeli edit/hapus alamat
            // tersimpannya nanti, histori pesanan yang sudah ada tidak ikut berubah.
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('shipping_address');
            $table->string('shipping_province');
            $table->string('shipping_city');
            $table->string('shipping_district');
            $table->string('shipping_postal_code');

            $table->text('notes')->nullable(); // catatan pembeli buat penjual/kurir

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
