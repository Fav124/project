@extends('layouts.app')

@section('title', 'Kebijakan & Aturan Penggunaan')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-lg border-0 mt-5">
            <div class="card-header bg-primary text-white py-4">
                <h3 class="mb-0 text-center fw-bold">Kebijakan & Aturan DEI Health</h3>
            </div>
            <div class="card-body p-5">
                <section class="mb-4">
                    <h5 class="fw-bold text-primary"><i class="bi bi-shield-check me-2"></i> 1. Kerahasiaan Data</h5>
                    <p class="text-muted">Setiap pengguna wajib menjaga kerahasiaan data medis santri. Dilarang menyebarluaskan informasi kesehatan santri kepada pihak luar tanpa izin resmi dari pimpinan Pondok Pesantren.</p>
                </section>

                <section class="mb-4">
                    <h5 class="fw-bold text-primary"><i class="bi bi-person-badge me-2"></i> 2. Tanggung Jawab Akun</h5>
                    <p class="text-muted">Akun bersifat personal. Penyalahgunaan akun oleh orang lain menjadi tanggung jawab pemilik akun. Dilarang memberikan kredensial login kepada siapapun.</p>
                </section>

                <section class="mb-4">
                    <h5 class="fw-bold text-primary"><i class="bi bi-capsule me-2"></i> 3. Akurasi Input Data</h5>
                    <p class="text-muted">Petugas kesehatan wajib memasukkan data obat, dosis, dan riwayat pemeriksaan secara akurat. Kelalaian yang disengaja dalam input data medis dapat berakibat fatal dan dikenakan sanksi.</p>
                </section>

                <section class="mb-4">
                    <h5 class="fw-bold text-danger"><i class="bi bi-exclamation-octagon me-2"></i> 4. Pelanggaran & Sanksi</h5>
                    <ul class="text-muted">
                        <li><strong>Pembekuan Akun (Freeze):</strong> Dilakukan jika ditemukan aktivitas mencurigakan atau kelalaian ringan dalam prosedur input.</li>
                        <li><strong>Pemblokiran Permanen (Block):</strong> Dilakukan jika terbukti melakukan pembocoran data, sabotase sistem, atau penyalahgunaan wewenang medis yang membahayakan santri.</li>
                    </ul>
                </section>

                <div class="alert alert-info mt-5 border-0 bg-light-info text-info">
                    <i class="bi bi-info-circle-fill me-2"></i> Dengan mendaftar, Anda dianggap telah membaca, memahami, dan menyetujui seluruh aturan di atas demi keselamatan dan privasi santri.
                </div>

                <div class="text-center mt-5">
                    <a href="{{ route('register') }}" class="btn btn-primary px-5 rounded-pill shadow-sm">Kembali ke Pendaftaran</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
