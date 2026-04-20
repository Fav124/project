<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovedMiddleware
{
    /**
     * Memastikan user sudah disetujui oleh super admin sebelum bisa mengakses aplikasi.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !$user->isSuperAdmin()) {
            if ($user->isPending()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('warning', 'Akun Anda masih menunggu persetujuan dari Super Admin.');
            }

            if ($user->isRejected()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $reason = $user->rejection_reason
                    ? ' Alasan: ' . $user->rejection_reason
                    : '';

                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah ditolak.' . $reason);
            }
        }

        return $next($request);
    }
}
