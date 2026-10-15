<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin dibuat dari ADMIN_EMAIL / ADMIN_PASSWORD di .env.
        // (Akun "Test User" dengan password bawaan sudah dihapus karena
        // berbahaya kalau sempat ter-seed di server publik.)
        $this->call(AdminUserSeeder::class);
    }
}
