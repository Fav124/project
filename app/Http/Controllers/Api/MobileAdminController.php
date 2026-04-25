<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAdminController extends BaseApiController
{
    public function overview()
    {
        return $this->success([
            'stats' => [
                'total_users' => User::where('role', '!=', 'super_admin')->count(),
                'pending' => User::where('status', 'pending')->count(),
                'approved' => User::where('status', 'approved')->where('role', '!=', 'super_admin')->count(),
                'rejected' => User::where('status', 'rejected')->count(),
                'petugas' => User::where('role', 'petugas_kesehatan')->where('status', 'approved')->count(),
                'admin' => User::where('role', 'admin')->where('status', 'approved')->count(),
            ],
        ]);
    }

    public function users(Request $request)
    {
        $query = User::where('role', '!=', 'super_admin');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate((int) $request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $users->map(fn (User $user) => $this->transformUser($user)),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function approve(User $user)
    {
        abort_if($user->isSuperAdmin(), 422, 'Aksi tidak diizinkan.');

        $user->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return $this->success([
            'item' => $this->transformUser($user->fresh()),
        ], "Akun {$user->name} berhasil disetujui.");
    }

    public function reject(Request $request, User $user)
    {
        abort_if($user->isSuperAdmin(), 422, 'Aksi tidak diizinkan.');

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return $this->success([
            'item' => $this->transformUser($user->fresh()),
        ], "Akun {$user->name} telah ditolak.");
    }

    public function changeRole(Request $request, User $user)
    {
        abort_if($user->isSuperAdmin(), 422, 'Role Super Admin tidak dapat diubah.');

        $validated = $request->validate([
            'role' => ['required', 'in:admin,petugas_kesehatan'],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return $this->success([
            'item' => $this->transformUser($user->fresh()),
        ], "Role {$user->name} berhasil diperbarui.");
    }

    public function quickResetPassword(User $user)
    {
        abort_if($user->isSuperAdmin(), 422, 'Password Super Admin tidak dapat di-reset.');

        $newPassword = Str::random(10);
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $this->success([
            'item' => $this->transformUser($user->fresh()),
            'new_password' => $newPassword,
        ], "Password {$user->name} berhasil di-reset.");
    }

    public function destroy(User $user)
    {
        abort_if($user->isSuperAdmin(), 422, 'Super Admin tidak dapat dihapus.');

        $name = $user->name;
        $user->delete();

        return $this->success([], "User {$name} berhasil dihapus.");
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'role_label' => $user->role_label,
            'status' => $user->status,
            'status_label' => $user->status_label,
            'rejection_reason' => $user->rejection_reason,
            'approved_at' => optional($user->approved_at)->toIso8601String(),
            'created_at' => optional($user->created_at)->toIso8601String(),
        ];
    }
}
