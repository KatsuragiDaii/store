<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\EnsureEmailVerified;

// ROUTE HALAMAN UTAMA (KATALOG)
Route::get('/', function () {
    $products = Product::latest()->take(8)->get();
    return view('welcome', compact('products'));
});

// ROUTE HALAMAN INFORMASI (FOOTER)
Route::get('/pengiriman', [PageController::class, 'pengiriman'])->name('page.pengiriman');
Route::get('/kebijakan-retur', [PageController::class, 'retur'])->name('page.retur');
Route::get('/kontak', [PageController::class, 'kontak'])->name('page.kontak');

// ROUTE PRODUK & KOLEKSI
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/men', [ProductController::class, 'men'])->name('men');
Route::get('/women', [ProductController::class, 'women'])->name('women');
Route::get('/collections', [ProductController::class, 'collections'])->name('collections');
Route::get('/products', [ProductController::class, 'products'])->name('products');
Route::get('/sale', [ProductController::class, 'sale'])->name('sale');

// ROUTE KERANJANG BELANJA (CART)
// Bebas diakses tamu (guest) sebelum checkout
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// ROUTE AKUN (LOGIN / REGISTER)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // Mencegah Brute Force: Maksimal 5 percobaan login per 1 menit
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // Mencegah Spam Pendaftaran Akun: Maksimal 5 percobaan register per 1 menit
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    // Login dengan Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Lupa Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ROUTE USER TERAUTENTIKASI (WAJIB LOGIN, BELUM PERLU VERIFIKASI EMAIL)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Verifikasi Email
    Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::get('/email/verify-status', [AuthController::class, 'verificationStatus'])->middleware('throttle:60,1')->name('verification.status');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1')->name('verification.send');
});

// Link verifikasi di email: sengaja DI LUAR grup 'auth' supaya bisa dibuka di HP
// walaupun pendaftaran dilakukan di laptop. Dilindungi signed URL + hash email.
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed', 'throttle:20,1'])->name('verification.verify');

// ROUTE USER TERAUTENTIKASI + EMAIL TERVERIFIKASI
// (verifikasi hanya ditegakkan kalau REQUIRE_EMAIL_VERIFICATION=true di .env)
Route::middleware(['auth', EnsureEmailVerified::class])->group(function () {
    // Checkout & Pesanan
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Dasbor Akun & Pengaturan Profil
    Route::get('/akun', [AccountController::class, 'index'])->name('account.dashboard');
    Route::post('/akun/profil', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
    Route::post('/akun/password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');
    
    // Kelola Multi-Alamat
    Route::post('/akun/alamat', [AccountController::class, 'storeAddress'])->name('account.storeAddress');
    Route::delete('/akun/alamat/{id}', [AccountController::class, 'destroyAddress'])->name('account.destroyAddress');
    Route::post('/akun/alamat/{id}/utama', [AccountController::class, 'setPrimaryAddress'])->name('account.setPrimaryAddress');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Review Produk
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

// ROUTE WEBHOOK MIDTRANS
// Endpoint server-to-server, tidak butuh CSRF atau Auth
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');

// ROUTE ADMIN (WAJIB LOGIN + STATUS ADMIN)
Route::middleware(['auth', 'admin'])->group(function () {
    // Kelola Katalog
    Route::get('/tambah-produk', [ProductController::class, 'create']);
    Route::post('/tambah-produk', [ProductController::class, 'store'])->name('product.store');
    Route::get('/tambah-koleksi', [ProductController::class, 'createCollection'])->name('collections.create');
    Route::post('/tambah-koleksi', [ProductController::class, 'storeCollection'])->name('collections.store');
    
    // Kelola Dasbor Admin & Pesanan
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Rute CRUD Produk
        Route::get('/produk', [ProductController::class, 'adminIndex'])->name('products.index');
        Route::get('/produk/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/produk/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/produk/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // RUTE MANAJEMEN STOK CEPAT (TAMBAHKAN INI)
        Route::get('/stok', [ProductController::class, 'stockIndex'])->name('products.stock');
        Route::post('/stok', [ProductController::class, 'stockUpdate'])->name('products.stock.update');
    });
});