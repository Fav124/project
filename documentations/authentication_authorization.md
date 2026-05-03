# Dokumentasi Autentikasi dan Otorisasi DEI Health

## 1. Arsitektur Sistem

Sistem autentikasi dan otorisasi aplikasi **DEI Health** dibangun menggunakan **Laravel 13** dengan arsitektur **Role-Based Access Control (RBAC)** berbasis **Native Enum** dan **Middleware**.

Pendekatan ini dipilih karena peran (role) dalam sistem ini sangat spesifik dan memiliki batasan yang terstruktur. Penggunaan package seperti *Spatie Permission* dinilai berlebihan (*overkill*) untuk kebutuhan ini, karena tidak ada keperluan bagi user untuk membuat role kustom secara dinamis beserta permission yang sangat granular melalui antarmuka pengguna (UI).

Sistem autentikasi berbasis API menggunakan **Laravel Sanctum**.

## 2. Definisi Role & Hak Akses

Aplikasi memiliki 3 jenis role utama:

1. **`super_admin`**
   - Memiliki akses absolut ke seluruh sistem.
   - Otomatis menembus semua pembatasan di level middleware.
   - Satu-satunya role yang berhak menyetujui (approve) pengguna baru.

2. **`admin`**
   - Memiliki akses ke hampir seluruh fitur aplikasi (termasuk manajemen data pokok).
   - **Pengecualian:** Tidak dapat menyetujui/menerima pendaftaran user baru.

3. **`petugas_kesehatan`**
   - **Akses Ditolak:** Tidak boleh mengubah data pokok seperti:
     - Data Santri
     - Data Kelas
     - Data Jurusan
     - Data Kasur
   - **Akses Diizinkan:** Boleh mengelola data layanan kesehatan (misal: Obat, Riwayat Sakit, dll).

## 3. Struktur Database

Tabel `users` dimodifikasi dengan penambahan 2 kolom baru:
- `role` (string): Menyimpan nilai role dari Enum `App\Enums\Role`. Default: `petugas_kesehatan`.
- `is_approved` (boolean): Menentukan apakah user sudah disetujui oleh `super_admin` dan boleh login. Default: `false`.

## 4. Middleware & Registrasi

Logika otorisasi utama ditangani oleh `App\Http\Middleware\RoleMiddleware`.
Middleware ini didaftarkan dengan alias `role` pada file konfigurasi modern Laravel `bootstrap/app.php`.

**Cara Kerja Middleware `role`:**
- Mengecek apakah pengguna sudah terautentikasi.
- Jika role adalah `super_admin`, permintaan otomatis diteruskan tanpa pengecekan lebih lanjut.
- Jika bukan `super_admin`, middleware akan memvalidasi apakah role pengguna saat ini terdapat di dalam daftar role yang dideklarasikan pada *route definition*.

## 5. API Endpoints

Berikut adalah endpoints yang tersedia untuk autentikasi dasar:

### `POST /api/register`
Mendaftarkan pengguna baru ke sistem DEI Health. Pengguna baru otomatis bersatus `is_approved = false` dan belum bisa melakukan login sebelum disetujui.

### `POST /api/login`
Autentikasi menggunakan Email dan Password.
- Akan ditolak (`403 Forbidden`) jika `is_approved` masih `false` (kecuali user tersebut adalah `super_admin`).
- Mengembalikan API Token (Sanctum) bertipe Bearer jika berhasil.

### `GET /api/profile`
*(Wajib Login)* Mengembalikan data profil pengguna yang sedang login.

### `POST /api/logout`
*(Wajib Login)* Menghapus token yang sedang digunakan (Logout).

## 6. Contoh Penggunaan Middleware pada Route

Pemisahan hak akses dapat dilakukan secara bersih pada `routes/api.php` dengan cara mengelompokkan route berdasarkan middleware `role:{nama_role}`.

```php
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Modul Data Pokok
    // Hanya Admin (dan Super Admin yang menembus otomatis) yang dapat mengakses
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('santri', SantriController::class);
        Route::apiResource('kelas', KelasController::class);
    });

    // 2. Modul Approval User
    // Khusus Super Admin
    Route::middleware('role:super_admin')->group(function () {
        Route::post('/users/{id}/approve', [UserController::class, 'approve']);
    });

    // 3. Modul Layanan Kesehatan
    // Petugas Kesehatan diizinkan, Admin diizinkan (Super Admin menembus otomatis)
    Route::middleware('role:petugas_kesehatan,admin')->group(function () {
        Route::apiResource('obat', ObatController::class);
        Route::apiResource('riwayat-sakit', RiwayatSakitController::class);
    });

});
```

## 7. Skalabilitas ke Depan

- **Penambahan Modul:** Saat Anda menambahkan fitur baru seperti `Jurusan` atau `Kasur`, cukup masukkan route tersebut ke dalam *group* `Route::middleware('role:admin')`.
- **Kebijakan Lebih Spesifik (Policy):** Jika di masa depan `petugas_kesehatan` hanya boleh mengedit rekam medis yang **dia buat sendiri**, Anda tetap bisa menggunakan struktur ini dan menambahkannya dengan fitur **Laravel Policy** di dalam controllernya (`$this->authorize('update', $riwayatSakit)`).
