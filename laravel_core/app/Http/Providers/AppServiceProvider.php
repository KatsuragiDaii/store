<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🌟 Isi email reset password bawaan Laravel defaultnya Bahasa
        // Inggris — ini diubah ke Bahasa Indonesia biar konsisten sama
        // seluruh tampilan situs lainnya.
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Password - FROMHERE')
                ->greeting('Halo, '.$notifiable->name.'!')
                ->line('Kami menerima permintaan reset password untuk akunmu di FROMHERE.')
                ->action('Reset Password', $url)
                ->line('Link ini berlaku selama 60 menit.')
                ->line('Kalau kamu tidak merasa meminta ini, abaikan saja email ini — password kamu tidak akan berubah.');
        });
    }
}
