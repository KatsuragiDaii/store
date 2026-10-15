<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Rumah'); // Ex: Rumah, Kantor, Kosan
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('full_address');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('postal_code');
            $table->boolean('is_primary')->default(false); // Penanda alamat utama
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_addresses');
    }
};