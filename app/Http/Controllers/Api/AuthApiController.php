<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends BaseApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        if (!$user->isApproved() && !$user->isSuperAdmin()) {
            return $this->error('Akun belum aktif atau belum disetujui.', 403);
        }

        if (!$user->canAccessHealthFeatures()) {
            return $this->error('Role Anda tidak memiliki akses ke aplikasi mobile.', 403);
        }

        $token = $user->createToken($credentials['device_name'] ?: 'android-device')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->role_label,
                'status' => $user->status,
            ],
        ], 'Login berhasil.');
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->role_label,
                'status' => $user->status,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success([], 'Logout berhasil.');
    }
}
