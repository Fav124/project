<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        // Prevent changing own role or super admin changing other super admins if needed
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role Anda sendiri.');
        }

        $request->validate([
            'role' => 'required|string|in:super_admin,admin,petugas_kesehatan',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role user {$user->name} berhasil diperbarui.");
    }

    public function changeStatus(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah status akun sendiri.');
        }

        $request->validate([
            'status' => 'required|string|in:active,frozen,blocked',
        ]);

        $user->update([
            'status' => $request->status,
            'is_approved' => $request->status === 'active'
        ]);

        return back()->with('success', "Status user {$user->name} berhasil diubah menjadi {$user->status_label}.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $newStatus = $user->status === 'active' ? 'frozen' : 'active';
        $user->update([
            'status' => $newStatus,
            'is_approved' => $newStatus === 'active'
        ]);

        return back()->with('success', "User {$user->name} sekarang {$user->status_label}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', "User {$user->name} berhasil dihapus.");
    }
}
