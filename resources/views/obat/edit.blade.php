@extends('layouts.app')

@section('title', 'Edit Obat')
@section('page_title', 'Perbarui Data Obat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('obat.index') }}">Obat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <form action="{{ route('obat.update', $obat->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Obat</label>
                            @if($obat->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $obat->foto) }}" class="img-thumbnail" style="width: 150px;">
                                </div>
                            @endif
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Obat <span class="text-danger">*</span></label>
                            <input type="text" name="kode_obat" class="form-control" value="{{ old('kode_obat', $obat->kode_obat) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_obat" class="form-control" value="{{ old('nama_obat', $obat->nama_obat) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $obat->kategori) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Golongan Obat <span class="text-danger">*</span></label>
                            <select name="golongan" class="form-select" required>
                                <option value="bebas" {{ $obat->golongan == 'bebas' ? 'selected' : '' }}>Obat Bebas (Hijau)</option>
                                <option value="bebas_terbatas" {{ $obat->golongan == 'bebas_terbatas' ? 'selected' : '' }}>Obat Bebas Terbatas (Biru)</option>
                                <option value="keras" {{ $obat->golongan == 'keras' ? 'selected' : '' }}>Obat Keras (K Merah)</option>
                                <option value="narkotika" {{ $obat->golongan == 'narkotika' ? 'selected' : '' }}>Narkotika (Palang Merah)</option>
                                <option value="psikotropika" {{ $obat->golongan == 'psikotropika' ? 'selected' : '' }}>Psikotropika (Bintang Biru)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bentuk Sediaan <span class="text-danger">*</span></label>
                            <select name="bentuk_sediaan" class="form-select" required>
                                <option value="Tablet" {{ $obat->bentuk_sediaan == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Kapsul" {{ $obat->bentuk_sediaan == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                                <option value="Sirup" {{ $obat->bentuk_sediaan == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                                <option value="Salep" {{ $obat->bentuk_sediaan == 'Salep' ? 'selected' : '' }}>Salep</option>
                                <option value="Injeksi" {{ $obat->bentuk_sediaan == 'Injeksi' ? 'selected' : '' }}>Injeksi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan Terkecil <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $obat->satuan) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi_penyimpanan" class="form-control" value="{{ old('lokasi_penyimpanan', $obat->lokasi_penyimpanan) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok (Read Only)</label>
                            <input type="number" class="form-control bg-light" value="{{ $obat->stok }}" readonly>
                            <small class="text-muted">Gunakan modul Mutasi untuk ubah stok.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $obat->stok_minimum) }}" required min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tgl Kadaluarsa <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kadaluarsa" class="form-control" value="{{ old('tanggal_kadaluarsa', $obat->tanggal_kadaluarsa?->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Deskripsi / Catatan</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $obat->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('obat.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning text-white">Perbarui Data Obat</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
