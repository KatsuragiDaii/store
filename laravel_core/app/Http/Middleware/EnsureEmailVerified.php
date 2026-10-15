<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menahan user yang emailnya belum diverifikasi.
 *
 * - Tidak melakukan apa-apa selama REQUIRE_EMAIL_VERIFICATION=false di .env
 *   (jadi aman di-upload sebelum pengiriman email siap).
 * - Admin tidak pernah ditahan, supaya pemilik toko tidak bisa terkunci.
 */
class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('fromhere.require_email_verification')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->is_admin && ! $user->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                abort(403, 'Email kamu belum diverifikasi.');
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
