<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email verifikasi berbahasa Indonesia. Link-nya dibuat oleh Laravel
 * (signed URL, berlaku sesuai auth.verification.expire, default 60 menit).
 */
class VerifyEmailId extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);
        $menit = config('auth.verification.expire', 60);

        return (new MailMessage())
            ->subject('Verifikasi email akun FROMHERE')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih sudah mendaftar di FROMHERE. Klik tombol di bawah untuk memverifikasi email kamu.')
            ->action('Verifikasi Email', $url)
            ->line("Link ini berlaku {$menit} menit. Kalau kamu tidak merasa mendaftar di FROMHERE, abaikan saja email ini.")
            ->salutation('Salam, FROMHERE');
    }
}
