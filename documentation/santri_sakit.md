# Dokumentasi Sistem Santri Sakit (Medical & Sickness Workflow)

Sistem "Santri Sakit" di dalam project DEIHealth dirancang secara komprehensif untuk mencatat dan memantau alur pengobatan medis santri, dari mulai titik kunjungan ke klinik/UKS hingga selesai masa pengobatan (sembuh, rawat inap, dirujuk, atau dipulangkan).

---

## 1. Entity-Relationship & Model

Berikut adalah model-model utama yang mendukung ekosistem fitur santri sakit:

### a. `Kunjungan` (`App\Models\Kunjungan`)
Model ini merupakan *entry point* dari setiap keluhan medis santri.
*   **Fungsi:** Mencatat detail keluhan, hasil anamnesis (riwayat keluhan), tanda-tanda vital (suhu, tensi, denyut nadi, pernapasan), diagnosa sementara, tindakan yang diambil, status kunjungan, dan catatan khusus dari petugas.
*   **Relasi:**
    *   `santri()`: BelongsTo `Santri`
    *   `petugas()`: BelongsTo `User`
    *   `pemberianObats()`: HasMany `PemberianObat`
    *   `rawatInap()`: HasOne `RawatInap`
    *   `kasusSakit()`: HasOne `KasusSakit`
    *   `diagnosas()`, `keluhanMasters()`, `tindakanMasters()`: BelongsToMany (Tagging system)

### b. `KasusSakit` (`App\Models\KasusSakit`)
Model ini memanajemen siklus / *state management* kasus kesehatan yang mungkin berlangsung melewati satu sesi kunjungan (misal: dirawat berhari-hari).
*   **Fungsi:** Menyimpan status holistik saat ini (`aktif`, `pulang`, `rujuk`, `sembuh`), durasi waktu (tanggal mulai & selesai), dan diagnosa terakhir yang disepakati.
*   **Relasi:**
    *   `riwayats()`: HasMany `RiwayatPerawatan` (Mencatat perpindahan lokasi secara berurutan, misal: UKS -> RS -> Dipulangkan).
    *   `riwayatAktif()`: HasOne `RiwayatPerawatan` (Menyaring riwayat yang masih berjalan saat ini).

### c. `RawatInap` (`App\Models\RawatInap`)
Model khusus untuk mencatat detail spesifik rawat inap yang dilakukan di UKS.
*   **Fungsi:** Menyimpan rekam jejak tanggal masuk, estimasi kembali/keluar, kondisi awal saat masuk, dan kondisi saat keluar. Model ini juga dapat terhubung ke pengalokasian tempat tidur/`Kasur`.

### d. `PemberianObat` (`App\Models\PemberianObat`)
*   **Fungsi:** Mencatat detail resep dan jumlah obat yang diberikan kepada santri pada saat `Kunjungan`. Transaksi di tabel ini bertugas memotong stok fisik obat di model `Obat` melalui intervensi sistem mutasi stok.

### e. Master Medis (Tagging System)
*   **Model:** `Diagnosa`, `KeluhanMaster`, `TindakanMaster`
*   **Fungsi:** Digunakan sebagai master data untuk mempermudah standarisasi input keluhan. Fitur ini sangat penting untuk pelaporan dan analitik penyakit yang dominan terjadi di pondok.

---

## 2. Database Migration

Beberapa berkas migration utama pembentuk sistem ini:
*   `2026_04_26_133923_create_kunjungans_table.php`: Membuat tabel `kunjungans`.
*   `2026_04_26_134337_create_pemberian_obats_table.php`: Membuat tabel pivot resep obat.
*   `2026_04_26_134555_create_rawat_inaps_table.php`: Membuat tabel untuk detail in-patient.
*   `2026_04_27_204306_create_medical_case_tables.php`: Ini adalah inti yang membuat tabel `kasus_sakits` dan `riwayat_perawatans` guna melacak alur *state* perawatan.
*   `2026_05_03_204949_create_diagnosas_table.php` (beserta keluhan & tindakan master): Untuk tabel master data *tagging*.

---

## 3. Controllers & Alur Kerja (Workflow)

### a. `KunjunganController`
*(Lokasi: `app/Http/Controllers/Web/KunjunganController.php`)*
Merupakan Controller paling vital dalam alur santri sakit.
*   **`create()`**: Memuat *form* pemeriksaan dengan data agregasi Santri aktif, obat dengan stok tersedia, ketersediaan kasur, dan pemetaan seluruh Master Data (Diagnosa, dll).
*   **`store()`**: 
    1.  Memvalidasi input pemeriksaan.
    2.  Mencegah *double entry* dengan mengecek apakah santri masih berada dalam status Rawat Inap aktif.
    3.  Menggabungkan tag-tag keluhan dan tindakan, kemudian menyimpan representasinya ke tabel `kunjungans`.
    4.  Meresapkan data pada tabel-tabel pivot *many-to-many* untuk sinkronisasi tag.
    5.  Menyimpan data `PemberianObat` sekaligus mengurangi stok `Obat` melalui fungsi di `ObatService`.
    6.  Berdasarkan respon "Tindak Lanjut" (Rawat inap, Rujuk, Pulang), sistem akan memicu **`MedicalCaseService::startCase()`** untuk memulai *lifecycle* kasus penyakit.
    7.  Mengirim pesan WhatsApp (Report medis digital otomatis) ke *Wali Santri* melalui intergrasi **`WhatsappService`**.

### b. `RawatInapController`
Mengelola siklus santri yang tertahan di UKS. Termasuk memfasilitasi proses kepulangan (discharge), pengalokasian tempat tidur / *Bed Management*, atau perpanjangan estimasi pulang.

### c. `MasterMedisController`
Menyediakan fungsionalitas CRUD interaktif bagi Admin/Tenaga Medis untuk memelihara daftar tipe penyakit, tipe keluhan, dan tindakan penanganan.

---

## 4. Business Logic di Layer Services

Agar arsitektur kode tetap bersih dan mudah *testable*, proses bisnis dialihkan ke *Services*:
*   **`MedicalCaseService`**: Bertanggung jawab penuh terhadap perubahan kondisi pasien. Modul ini memastikan saat pasien pindah lokasi pengobatan (misal dari UKS kemudian dirujuk ke RSUD), `riwayat_perawatans` akan ter-update dan lokasi lama di-*close*.
*   **`ObatService`**: Melakukan validasi limitasi stok, merekam perpindahan stok ke tabel mutasi secara *history-safe*.
*   **`WhatsappService`**: Motor penggerak sistem notifikasi pihak ketiga untuk langsung memberitahu wali santri mengenai ringkasan diagnosa, lokasi dirawat, hingga dosis obat yang diberikan.
