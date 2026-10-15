<?php

namespace Database\Seeders;

use App\Support\AdminProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Membuat (atau menyalakan flag admin pada) akun admin dari .env:
 *   ADMIN_EMAIL, ADMIN_PASSWORD, ADMIN_NAME
 *
 * Aman dijalankan berkali-kali: akun yang sudah ada tidak dibuat ulang dan
 * passwordnya tidak ditimpa.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('fromhere.admin.email');

        if (! $email) {
            $this->command?->warn('ADMIN_EMAIL belum diisi di .env, jadi akun admin tidak dibuat.');

            return;
        }

        try {
            [$user, $status] = AdminProvisioner::provision(
                $email,
                config('fromhere.admin.name'),
                config('fromhere.admin.password'),
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                'Akun admin gagal dibuat: '.implode(' ', Arr::flatten($e->errors()))
                .' (cek ADMIN_EMAIL / ADMIN_PASSWORD di .env; password minimal 8 karakter, wajib huruf besar, huruf kecil, angka, dan simbol)'
            );
        }

        $this->command?->info(match ($status) {
            'created'  => "Akun admin dibuat: {$user->email}",
            'promoted' => "Akun yang sudah ada dijadikan admin: {$user->email}",
            default    => "Admin sudah ada, tidak ada perubahan: {$user->email}",
        });
    }
}
