<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {--email= : Email akun (default: ADMIN_EMAIL di .env)}
        {--name= : Nama akun baru (default: ADMIN_NAME di .env)}
        {--reset-password : Ganti password akun yang sudah ada}';

    protected $description = 'Buat akun admin baru, atau jadikan akun yang sudah ada sebagai admin';

    public function handle(): int
    {
        $email = $this->option('email') ?: config('fromhere.admin.email');

        if (! $email) {
            $this->error('Email belum diisi. Pakai --email=... atau isi ADMIN_EMAIL di .env.');

            return self::FAILURE;
        }

        // Cek format email dulu, supaya pesan error-nya tidak menyesatkan
        // (mis. "password belum diisi" padahal emailnya yang salah ketik).
        if (Validator::make(['email' => trim($email)], ['email' => 'required|email|max:255'])->fails()) {
            $this->error("Email tidak valid: {$email}");

            return self::FAILURE;
        }

        $password = $this->resolvePassword($email);

        if ($password === false) {
            return self::FAILURE;
        }

        try {
            [$user, $status] = AdminProvisioner::provision(
                $email,
                $this->option('name') ?: config('fromhere.admin.name'),
                $password,
                (bool) $this->option('reset-password'),
            );
        } catch (ValidationException $e) {
            foreach ($e->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        }

        $this->info(match ($status) {
            'created'        => "Akun admin dibuat: {$user->email}",
            'promoted'       => "Akun yang sudah ada dijadikan admin: {$user->email}",
            'password_reset' => "Password diganti dan akun dipastikan admin: {$user->email}",
            default          => "Akun ini sudah admin, tidak ada perubahan: {$user->email}",
        });

        return self::SUCCESS;
    }

    /**
     * @return string|false|null string = password dipakai, null = tidak perlu password, false = batal
     */
    private function resolvePassword(string $email): string|false|null
    {
        $normalized  = strtolower(trim($email));
        $envEmail    = strtolower(trim((string) config('fromhere.admin.email')));
        $envPassword = config('fromhere.admin.password');

        // Email yang sama dengan ADMIN_EMAIL -> pakai ADMIN_PASSWORD dari .env.
        if ($envPassword && $normalized === $envEmail) {
            return $envPassword;
        }

        $needsPassword = $this->option('reset-password')
            || ! User::where('email', $normalized)->exists();

        if (! $needsPassword) {
            return null; // akun sudah ada, cukup dijadikan admin
        }

        if (! $this->input->isInteractive()) {
            $this->error('Password belum diisi. Isi ADMIN_PASSWORD di .env (dengan ADMIN_EMAIL yang sama), atau jalankan perintah ini tanpa --no-interaction.');

            return false;
        }

        $password = $this->secret("Password untuk {$normalized}");

        if ($password !== $this->secret('Ulangi password')) {
            $this->error('Password tidak sama.');

            return false;
        }

        return $password;
    }
}
