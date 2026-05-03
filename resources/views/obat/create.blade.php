@extends('layouts.app')

@section('title', 'Tambah Obat')
@section('page_title', 'Registrasi Obat Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('obat.index') }}">Obat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <form action="{{ route('obat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Obat</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG. Maks: 2MB.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Obat <span class="text-danger">*</span></label>
                            <input type="text" name="kode_obat" class="form-control" required placeholder="Contoh: OB-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_obat" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kategori" class="form-control" required placeholder="Contoh: Analgesik, Antibiotik">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Golongan Obat <span class="text-danger">*</span></label>
                            <select name="golongan" class="form-select" required>
                                <option value="bebas">Obat Bebas (Hijau)</option>
                                <option value="bebas_terbatas">Obat Bebas Terbatas (Biru)</option>
                                <option value="keras">Obat Keras (K Merah)</option>
                                <option value="narkotika">Narkotika (Palang Merah)</option>
                                <option value="psikotropika">Psikotropika (Bintang Biru)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bentuk Sediaan <span class="text-danger">*</span></label>
                            <select name="bentuk_sediaan" class="form-select" required>
                                <option value="Tablet">Tablet</option>
                                <option value="Kapsul">Kapsul</option>
                                <option value="Sirup">Sirup</option>
                                <option value="Salep">Salep</option>
                                <option value="Injeksi">Injeksi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan Terkecil <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" required placeholder="Contoh: Tablet, Botol, Pcs">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi_penyimpanan" class="form-control" required placeholder="Contoh: Rak A-1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" required min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control" required min="0" value="5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tgl Kadaluarsa <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kadaluarsa" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Deskripsi / Catatan</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('obat.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Data Obat</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Petunjuk</h4>
            </div>
            <div class="card-body">
                <ul class="text-muted small">
                    <li><strong>Kode Obat</strong> harus unik dan mudah dikenali.</li>
                    <li><strong>Stok Minimum</strong> digunakan untuk memicu peringatan jika persediaan hampir habis.</li>
                    <li><strong>Tanggal Kadaluarsa</strong> akan dipantau oleh sistem setiap hari.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
