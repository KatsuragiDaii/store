<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// 🌟 Deploy dengan struktur folder terpisah (lihat DEPLOY_HOSTINGER.md) —
// public_path() perlu diarahkan ke public_html yang sebenarnya, bukan ke
// folder public/ bawaan di dalam laravel_core.
//
// PERBAIKAN: sebelumnya ini dicek lewat env('PUBLIC_HTML_PATH'), tapi itu
// SALAH — file .env baru benar-benar dimuat Laravel SETELAH file ini
// selesai dieksekusi, jadi env() di sini selalu kosong walau .env sudah
// diisi benar. Sekarang dicek langsung ke filesystem: kalau ada folder
// "public_html" sejajar dengan folder laravel_core ini, otomatis dipakai.
// Di lokal (tidak ada folder public_html sejajar), baris ini otomatis
// dilewati dan tidak berpengaruh apa-apa.
$siblingPublicHtml = dirname(__DIR__, 2).'/public_html';
if (is_dir($siblingPublicHtml)) {
    $app->usePublicPath($siblingPublicHtml);
}

return $app;