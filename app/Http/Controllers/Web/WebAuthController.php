<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WebAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status === 'pending') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda belum disetujui oleh Super Admin.',
                ]);
            }

            if ($user->status === 'blocked') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda telah diblokir secara permanen karena pelanggaran kebijakan.',
                ]);
            }

            if ($user->status === 'frozen') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda sedang dibekukan sementara. Silakan hubungi admin.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->intended('dashboard');
        }
        
        $adminWhatsapp = \App\Models\Setting::where('key', 'admin_whatsapp')->value('value') ?? '628123456789';
        return view('auth.register', compact('adminWhatsapp'));
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => \Hash::make($request->password),
            'role' => 'petugas_kesehatan', // Default role for registration
            'is_approved' => false,
        ]);

        // Create Approval Request
        $approvalService = app(\App\Services\ApprovalService::class);
        $approvalService->submit(
            \App\Models\User::class, 
            $user->id, 
            'register_user', 
            ['name' => $user->name, 'email' => $user->email, 'role' => 'petugas_kesehatan'],
            "Registrasi pengguna baru dari Website: {$user->name}"
        );

        // Generate WA message for admin
        $adminWhatsapp = \App\Models\Setting::where('key', 'admin_whatsapp')->value('value') ?? '628123456789';
        $message = "Halo Admin DEI Health, saya baru saja mendaftar akun baru:\n\nNama: {$user->name}\nEmail: {$user->email}\nNo. HP: {$user->no_hp}\n\nMohon bantuannya untuk verifikasi akun saya. Terima kasih.";
        $waLink = "https://wa.me/{$adminWhatsapp}?text=" . urlencode($message);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan admin.')->with('waLink', $waLink);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
