<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $role = Role::tryFrom($validated['role'] ?? '') ?? Role::PETUGAS_KESEHATAN;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'is_approved' => false, 
        ]);

        // Create Approval Request
        $approvalService = app(\App\Services\ApprovalService::class);
        $approvalService->submit(
            User::class, 
            $user->id, 
            'register_user', 
            ['name' => $user->name, 'email' => $user->email, 'role' => $user->role->value],
            "Registrasi pengguna baru: {$user->name}"
        );

        // Option: Send WA message or trigger system notification
        // For now, return success with message
        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Silakan hubungi Super Admin untuk aktivasi akun.',
            'data' => $user,
        ], 201);
    }

    /**
     * Authenticate user and generate token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.', 'data' => null], 401);
        }

        if (! $user->is_approved && $user->role !== Role::SUPER_ADMIN) {
            return response()->json(['success' => false, 'message' => 'Your account is pending approval.', 'data' => null], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $request->user()
        ]);
    }

    /**
     * Logout and revoke tokens.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'data' => null
        ]);
    }
}
