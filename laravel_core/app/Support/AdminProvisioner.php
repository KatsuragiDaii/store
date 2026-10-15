<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Satu-satunya tempat yang boleh menjadikan seseorang admin.
 * Dipakai bersama oleh AdminUserSeeder dan perintah `admin:create`.
 */
class AdminProvisioner
{
    /**
     * @return array{0: User, 1: string} [user, status] -- status: 'created' | 'promoted' | 'unchanged' | 'password_reset'
     *
     * @throws ValidationException kalau email tidak valid / password lemah untuk akun baru
     */
    public static function provision(
        string $email,
        ?string $name = null,
        ?string $password = null,
        bool $resetPassword = false,
    ): array {
        $email = strtolower(trim($email));

        Validator::make(['email' => $email], ['email' => 'required|email|max:255'])->validate();

        $user = User::where('email', $email)->first();

        // Akun sudah ada: cukup pastikan flag admin menyala. Password TIDAK
        // disentuh (supaya `db:seed` ulang tidak menimpa password yang sudah
        // kamu ganti), kecuali diminta eksplisit lewat --reset-password.
        if ($user) {
            $status = 'unchanged';

            if (! $user->is_admin) {
                $user->forceFill(['is_admin' => true]);
                $status = 'promoted';
            }

            // Admin dibuat oleh pemilik toko, jadi emailnya dianggap sudah
            // terverifikasi (supaya tidak terkunci saat verifikasi email menyala).
            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()]);
            }

            if ($resetPassword) {
                self::validatePassword($password);
                $user->forceFill(['password' => Hash::make($password)]);
                $status = 'password_reset';
            }

            if ($user->isDirty()) {
                $user->save();
            }

            return [$user, $status];
        }

        // Akun baru: password wajib dan harus lolos aturan yang sama dengan
        // form registrasi.
        self::validatePassword($password);

        $user = new User();
        $user->forceFill([
            'name'     => $name ?: 'Admin',
            'email'    => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        return [$user, 'created'];
    }

    private static function validatePassword(?string $password): void
    {
        Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]],
        )->validate();
    }
}
