<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Tolak akses kalau user belum login ATAU login tapi bukan admin.
     * Dipasang setelah middleware 'auth' di route (lihat routes/web.php),
     * jadi saat middleware ini jalan, kita hanya perlu cek flag is_admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_admin) {
            abort(403, 'Halaman ini khusus admin.');
        }

        return $next($request);
    }
}
