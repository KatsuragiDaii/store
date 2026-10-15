<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLogin() {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Arahkan ke dashboard akun yang baru setelah login sukses
            return redirect()->intended('/akun'); 
        }

        // Pesan error generik untuk mencegah serangan Enumerasi Akun
        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.'
        ])->onlyInput('email');
    }

    // Menampilkan form register
    public function showRegister() {
        return view('auth.register');
    }

    // Memproses registrasi
    public function register(Request $request) {
        $wajibVerifikasi = (bool) config('fromhere.require_email_verification');

        // Saat verifikasi email menyala, domain email juga dicek lewat DNS
        // (menolak mis. abc@asdfgh.xyz). Email palsu di domain asli tetap
        // ditangkap oleh link verifikasi.
        $aturanEmail = ($wajibVerifikasi && config('fromhere.validate_email_dns')) ? 'email:rfc,dns' : 'email';

        // Validasi pendaftaran dengan aturan password yang kuat
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', $aturanEmail, 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'confirmed', 
                PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols()
            ]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Pendaftar baru SELALU user biasa (is_admin default false di database).
            // Akun admin dibuat lewat `php artisan admin:create` atau diubah manual di database.
        ]);

        // Langsung login setelah register berhasil
        Auth::login($user);

        // Verifikasi email menyala: kirim link, lalu minta user cek emailnya.
        // Kalau pengiriman gagal, pendaftaran tetap berhasil (jangan 500);
        // user bisa menekan "Kirim Ulang" di halaman berikutnya.
        if ($wajibVerifikasi) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('verification.notice');
        }

        // Arahkan ke dashboard akun yang baru
        return redirect('/akun');
    }

    // Memproses logout
    public function logout(Request $request) {
        Auth::logout();
        
        // Hapus dan reset token session demi keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // 🌟 Arahkan ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 🌟 Google balik ke sini setelah user login/setuju di sisi Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login dengan Google gagal atau dibatalkan, silakan coba lagi.',
            ]);
        }

        // Sudah pernah login pakai Google akun ini sebelumnya?
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // Belum — tapi kalau emailnya sama dengan akun yang sudah ada
            // (daftar manual sebelumnya), sambungkan ke akun itu alih-alih
            // bikin akun duplikat. Email dari Google sudah pasti terverifikasi,
            // jadi aman dijadikan dasar pencocokan.
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // Akun baru dari Google SELALU user biasa (bukan admin).
                // forceFill dipakai karena email_verified_at tidak ada di
                // #[Fillable] model User; dengan create() nilainya diam-diam
                // dibuang, sehingga akun Google tersimpan sebagai "belum verifikasi".
                $user = new User();
                $user->forceFill([
                    'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'email_verified_at' => now(),
                ])->save();
            }
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/akun');
    }

    // 🌟 Halaman "Cek email kamu" untuk user yang belum verifikasi
    public function showVerifyNotice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }

        return view('auth.verify-email', ['email' => $request->user()->email]);
    }

    // 🌟 Link di email diklik.
    // TIDAK wajib login: user sering mendaftar di laptop tapi membuka email di HP
    // (browser HP belum login). Keamanannya ada di signed URL (tanda tangan + masa
    // berlaku) dan hash email. Link ini hanya MEMBUKTIKAN email milik pendaftar;
    // tidak memberi login ke siapa pun.
    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return view('auth.email-verified', [
            'sudahLogin' => Auth::check() && Auth::id() === $user->id,
        ]);
    }

    // 🌟 Dipanggil otomatis oleh halaman "Cek email kamu" tiap beberapa detik,
    // supaya halaman di laptop lanjut sendiri setelah link diklik di HP.
    public function verificationStatus(Request $request)
    {
        return response()->json(['verified' => $request->user()->hasVerifiedEmail()]);
    }

    // 🌟 Kirim ulang link verifikasi
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['email' => 'Email gagal dikirim. Coba lagi beberapa saat lagi.']);
        }

        return back()->with('status', 'Link verifikasi baru sudah dikirim. Cek juga folder spam.');
    }

    // 🌟 Lupa password — tampilkan form isi email
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // 🌟 Kirim link reset ke email yang diisi
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Pakai broker bawaan Laravel — otomatis bikin token, simpan ke tabel
        // password_resets, dan kirim email lewat notifikasi ResetPassword.
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset password sudah dikirim ke email kamu. Cek juga folder spam kalau belum muncul.')
            : back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
    }

    // 🌟 Tampilkan form reset (link dari email membawa token + email lewat query string)
    public function showResetPassword(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // 🌟 Proses password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password berhasil diubah, silakan login dengan password baru.')
            : back()->withErrors(['email' => 'Link reset tidak valid atau sudah kedaluwarsa, silakan minta link baru.']);
    }
}