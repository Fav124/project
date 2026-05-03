# Dokumentasi Modul Santri, Wali, dan Kesehatan

## 1. Pendahuluan
Dokumen ini menjelaskan struktur, relasi, dan alur kerja untuk tiga modul utama dalam aplikasi **DEI Health**:
- Modul **Data Pokok Santri**
- Modul **Data Wali Santri**
- Modul **Data Kesehatan Santri**

Ketiga modul ini terintegrasi erat dengan sistem Role-Based Access Control (RBAC) yang telah dibangun sebelumnya, memastikan kerahasiaan dan integritas data medis maupun data pokok.

---

## 2. Struktur Database dan Relasi (Eloquent)

Skema database dirancang untuk mengakomodasi kebutuhan pencatatan data asrama dan medis pondok pesantren.

### A. Tabel `santris` (Data Pokok)
Menyimpan informasi inti tentang identitas santri.
- **Identifier Utama**: Menggunakan `nis` (Nomor Induk Santri) karena merupakan identitas pasti yang dikeluarkan oleh internal pesantren. Kolom `nisn` dibiarkan *nullable* untuk kebutuhan eksternal.
- **Foreign Keys**: `kelas_id`, `jurusan_id`, dan `kamar_id` dihubungkan ke tabel referensi masing-masing dengan batasan `nullOnDelete()`.
- **Relasi Eloquent**:
  - `Santri belongsTo Kelas, Jurusan, Kamar`
  - `Santri hasMany WaliSantri`
  - `Santri hasOne KesehatanSantri`

### B. Tabel `wali_santris`
Menyimpan kontak dan informasi tentang wali santri (Ayah, Ibu, Wali Darurat).
- **Foreign Key**: `santri_id` (`cascadeOnDelete()`).
- **Relasi Eloquent**: `WaliSantri belongsTo Santri`.
- Desain *hasMany* dipilih agar sistem dapat menyimpan lebih dari satu nomor darurat per-santri.

### C. Tabel `kesehatan_santris`
Berfungsi sebagai **Profil Dasar Medis** santri yang jarang berubah namun sangat krusial saat keadaan darurat medis.
- **Data Tersimpan**: Golongan darah, riwayat alergi bawaan, penyakit genetik/kondisi khusus, tinggi badan, dan berat badan.
- **Foreign Key**: `santri_id` (`cascadeOnDelete()`).
- **Relasi Eloquent**: `KesehatanSantri belongsTo Santri`.
- Tabel ini dirancang *One-to-One* terhadap santri. *Endpoint* akan menolak pembuatan (`POST`) rekam medis dasar baru jika santri tersebut sudah memilikinya, dan menyarankan metode pembaruan (`PUT`).

*(Catatan: Untuk pencatatan riwayat rawat inap atau berobat jalan, disarankan untuk membuat tabel baru berjenis One-to-Many di fase pengembangan selanjutnya).*

---

## 3. Pembatasan Hak Akses (Authorization)

Logika pembatasan diimplementasikan menggunakan `RoleMiddleware` langsung di level *Routing* (`routes/api.php`). Ini menjamin tidak ada celah keamanan pada tingkat *Controller*.

| Modul / Endpoint | Super Admin | Admin | Petugas Kesehatan |
| --- | --- | --- | --- |
| **Santri** (`/api/santri`) | ✅ Full Access | ✅ Full Access | ❌ Forbidden (403) |
| **Wali Santri** (`/api/wali-santri`) | ✅ Full Access | ✅ Full Access | ✅ Full Access |
| **Kesehatan Santri** (`/api/kesehatan-santri`) | ✅ Full Access | ✅ Full Access | ✅ Full Access |

**Alasan Desain:**
Petugas Kesehatan tidak boleh memiliki wewenang untuk mengubah identitas utama, status kelulusan, atau penempatan kamar santri. Namun, mereka mutlak perlu melihat/mengubah riwayat alergi (`KesehatanSantri`) dan menghubungi orang tua (`WaliSantri`) saat keadaan darurat.

---

## 4. Alur Kerja (Workflow) API

1. **Persiapan Data Referensi**: Admin harus mendaftarkan data `kelas`, `jurusans`, dan `kamars` terlebih dahulu.
2. **Pendaftaran Santri (Oleh Admin)**: 
   - Endpoint: `POST /api/santri`
   - Data pokok seperti nama, NIS, jenis kelamin dimasukkan.
3. **Pendaftaran Profil Medis Awal (Oleh Petugas Kesehatan)**:
   - Endpoint: `POST /api/kesehatan-santri`
   - Petugas kesehatan menggunakan `santri_id` dari santri yang baru didaftarkan untuk mencatat golongan darah dan alerginya.
4. **Pendaftaran Kontak Darurat (Oleh Petugas Kesehatan/Admin)**:
   - Endpoint: `POST /api/wali-santri`
   - Melampirkan `santri_id`. Dapat diulang jika ingin menambahkan profil Ibu dan profil Ayah secara terpisah.
5. **Pembaruan Data Medis (Oleh Petugas Kesehatan)**:
   - Endpoint: `PUT /api/kesehatan-santri/{id}`
   - Digunakan saat memperbarui metrik yang bisa berubah dalam jangka panjang seperti `tinggi_badan` dan `berat_badan`.

---

## 5. Validasi (*Form Request*)

Validasi data dipisahkan seluruhnya ke direktori `app/Http/Requests` agar `Controller` tetap bersih (*Thin Controller, Fat Request*).
Contoh aturan ketat yang diberlakukan:
- NIS bersifat *Unique* di seluruh database dan diecualikan ketika mode *Update*.
- `santri_id` di modul Kesehatan dan Wali divalidasi keabsahannya dengan aturan `exists:santris,id`.
- Tipe enum lokal (seperti L/P untuk gender) divalidasi dengan `in:L,P`.
