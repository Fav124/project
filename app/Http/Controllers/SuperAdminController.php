<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    // ─── Dashboard ───────────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total_users'   => User::where('role', '!=', 'super_admin')->count(),
            'pending'       => User::where('status', 'pending')->count(),
            'approved'      => User::where('status', 'approved')->where('role', '!=', 'super_admin')->count(),
            'rejected'      => User::where('status', 'rejected')->count(),
            'petugas'       => User::where('role', 'petugas_kesehatan')->where('status', 'approved')->count(),
            'admin'         => User::where('role', 'admin')->where('status', 'approved')->count(),
        ];

        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Chart Data: User Registration Trends (Last 7 Days)
        $registrationTrends = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('role', '!=', 'super_admin')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('super-admin.dashboard', compact('stats', 'pendingUsers', 'registrationTrends'));
    }

    // ─── User List ───────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin');

        $status = $request->status ?: $request->route()->defaults['status'] ?? null;
        if ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    // ─── Show single user ────────────────────────────────────────────────────

    public function showUser(User $user)
    {
        return view('super-admin.users.show', compact('user'));
    }

    // ─── Approve User ────────────────────────────────────────────────────────

    public function approve(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        $user->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $message = "Akun {$user->name} berhasil disetujui.";
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    // ─── Reject User ─────────────────────────────────────────────────────────

    public function reject(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountRejectedMail($user, $request->rejection_reason));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send rejection email to {$user->email}: " . $e->getMessage());
        }

        $message = "Akun {$user->name} telah ditolak.";
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    // ─── Change Role ─────────────────────────────────────────────────────────

    public function changeRole(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $request->validate([
            'role' => ['required', 'in:admin,petugas_kesehatan'],
        ]);

        $oldRole   = $user->role_label;
        $user->update(['role' => $request->role]);
        $newRole = $user->fresh()->role_label;

        $message = "Role {$user->name} diubah dari {$oldRole} menjadi {$newRole}.";
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    // ─── Reset Password ──────────────────────────────────────────────────────

    public function resetPassword(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Password Super Admin tidak dapat di-reset dari sini.');
        }

        $request->validate([
            'new_password'              => ['required', 'confirmed', Password::min(8)],
            'new_password_confirmation' => ['required'],
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        $message = "Password {$user->name} berhasil di-reset.";
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    // ─── Generate Random Password (quick reset) ──────────────────────────────

    public function quickResetPassword(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Password Super Admin tidak dapat di-reset dari sini.');
        }

        $newPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return back()->with('success', "Password {$user->name} di-reset menjadi: <strong>{$newPassword}</strong>. Sampaikan ke user tersebut.");
    }

    // ─── Delete User ─────────────────────────────────────────────────────────

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Admin tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('super-admin.users')
            ->with('success', "User {$name} berhasil dihapus.");
    }
}
