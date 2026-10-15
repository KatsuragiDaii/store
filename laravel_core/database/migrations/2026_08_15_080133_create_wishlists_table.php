<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // Mencegah user memasukkan 1 produk ke wishlist berkali-kali
            $table->unique(['user_id', 'product_id']); 
        });
    }
    public function down(): void {
        Schema::dropIfExists('wishlists');
    }
};