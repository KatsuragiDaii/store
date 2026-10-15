<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Stok disimpan per ukuran, contoh: {"S": 5, "M": 0, "L": 12}
            // Konsisten dengan pola JSON yang sudah dipakai di kolom sizes & images.
            $table->json('stock')->nullable()->after('sizes');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
