<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountSettingController extends Controller
{
    public function photo(User $user)
    {
        if (!$user->profile_photo_path || !Storage::disk('public')->exists($user->profile_photo_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $user->profile_photo_path));
    }

    public function edit(Request $request)
    {
        return view('account.settings', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'remove_profile_photo' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'profile_photo.image' => 'File foto harus berupa gambar.',
            'profile_photo.max' => 'Ukuran foto maksimal 2MB.',
            'current_password.required_with' => 'Password lama wajib diisi untuk mengganti password.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->job_title = $validated['job_title'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        if (!empty($validated['remove_profile_photo']) && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()
            ->route('account.settings.edit')
            ->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
