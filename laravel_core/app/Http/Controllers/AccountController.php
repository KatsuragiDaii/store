<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    // 1. Tampilkan Dashboard Akun
    public function index()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        // Ambil alamat, urutkan yang utama (is_primary = true) di paling atas
        $addresses = $user->addresses()->orderByDesc('is_primary')->latest()->get();
        
        return view('account.dashboard', compact('user', 'recentOrders', 'addresses'));
    }

    // 2. Update Biodata (Hanya update data yang tervalidasi)
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Regex Nomor HP Indonesia (08, 628, +628)
            'phone' => ['nullable', 'string', 'regex:/^(08|628|\+628)[0-9]{7,11}$/'],
        ], [
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08..., 628..., atau +628... (10-14 digit).'
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // 3. Update Password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Akun yang dibuat lewat "Lanjutkan dengan Google" belum punya password
        // (kolom password = NULL), jadi tidak ada "password lama" yang bisa dicek.
        $hasPassword = filled($user->password);

        $rules = [
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];

        // Password lama HANYA diwajibkan kalau akunnya memang sudah punya password.
        if ($hasPassword) {
            $rules['current_password'] = 'required|current_password'; // Bawaan Laravel u/ cek password lama
        }

        $request->validate($rules);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', $hasPassword
            ? 'Password berhasil diubah.'
            : 'Password berhasil dibuat. Sekarang kamu juga bisa login dengan email dan password.');
    }

    // 4. Tambah Alamat Baru (Lebih Aman dengan $validated)
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{7,11}$/'],
            'full_address' => 'required|string',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ], [
            'phone.regex' => 'Format nomor HP tidak valid.'
        ]);

        $user = Auth::user();
        
        // Jika ini alamat pertama, otomatis jadikan alamat utama
        $isPrimary = $user->addresses()->count() === 0;

        // Jika user sengaja centang "Jadikan Utama"
        if ($request->has('is_primary')) {
            $user->addresses()->update(['is_primary' => false]); // Matikan yang lain
            $isPrimary = true;
        }

        // Gunakan $validated, BUKAN $request->all()
        $user->addresses()->create(array_merge($validated, ['is_primary' => $isPrimary]));

        return back()->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    // 5. Hapus Alamat
    public function destroyAddress($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->delete();

        // Jika alamat utama dihapus dan masih ada alamat lain, jadikan alamat lain sebagai utama
        if ($address->is_primary && Auth::user()->addresses()->count() > 0) {
            Auth::user()->addresses()->first()->update(['is_primary' => true]);
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    // 6. Set Alamat Utama
    public function setPrimaryAddress($id)
    {
        $user = Auth::user();
        $user->addresses()->update(['is_primary' => false]);
        $user->addresses()->findOrFail($id)->update(['is_primary' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }
}