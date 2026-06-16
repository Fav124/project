<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if ($user->status !== 'approved') {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum disetujui atau telah ditolak. Hubungi administrator.',
            ], 403);
        }

        $token = $user->createToken('mobile-app', ['*'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => $this->formatUser($user),
            ],
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'no_hp'                 => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'nullable|in:petugas_kesehatan,admin',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'no_hp'    => $validated['no_hp'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'] ?? 'petugas_kesehatan',
            'status'   => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Tunggu persetujuan admin.',
            'data'    => $this->formatUser($user),
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => $this->formatUser($user),
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'no_hp'       => $user->no_hp ?? null,
            'role'        => $user->role,
            'role_label'  => $user->role_label,
            'is_approved' => $user->status === 'approved',
            'status'      => $user->status,
        ];
    }
}
