<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 🌟 PENTING: file ini BUKAN untuk folder public/ di project lokal kamu.
// Ini khusus untuk ditaruh di public_html Hostinger, menggantikan isi
// public/index.php asli — satu-satunya beda adalah path di bawah ini
// menunjuk ke ../laravel_core/ (satu folder di atas public_html), bukan
// langsung ../ seperti versi aslinya. Ini karena paket Hostinger Single
// tidak bisa mengubah document root ke folder public/ Laravel secara
// langsung, jadi isi Laravel (app/, routes/, vendor/, dst) ditaruh di
// folder terpisah bernama laravel_core, sejajar dengan public_html.
// Lihat DEPLOY_HOSTINGER.md untuk struktur folder lengkapnya.

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../laravel_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../laravel_core/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';

$app->handleRequest(Request::capture());
