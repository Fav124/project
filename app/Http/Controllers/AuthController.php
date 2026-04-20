<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ─── Login ───────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        // Super admin langsung masuk
        if (!$user->isSuperAdmin()) {
            if ($user->isPending()) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('warning', 'Akun Anda masih menunggu persetujuan Super Admin.');
            }

            if ($user->isRejected()) {
                $reason = $user->rejection_reason
                    ? ' Alasan: ' . $user->rejection_reason
                    : '';
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Akun Anda telah ditolak.' . $reason);
            }
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($this->dashboardRoute($user));
    }

    // ─── Register ────────────────────────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'petugas_kesehatan', // default; super admin bisa ubah
            'status'   => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan dari Super Admin.');
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function dashboardRoute(User $user): string
    {
        if ($user->isSuperAdmin()) {
            return route('super-admin.dashboard');
        }
        return route('dashboard');
    }

    private function redirectDashboard(User $user)
    {
        return redirect($this->dashboardRoute($user));
    }
}
