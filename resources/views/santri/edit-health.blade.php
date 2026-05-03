@extends('layouts.app')

@section('title', 'Edit Data Kesehatan')
@section('page_title', 'Lengkapi Data Kesehatan: ' . $santri->nama_lengkap)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('santri.index') }}">Santri</a></li>
    <li class="breadcrumb-item"><a href="{{ route('santri.show', $santri->id) }}">Detail</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Kesehatan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                    <img src="{{ $santri->foto ? asset('storage/' . $santri->foto) : asset('assets/images/faces/1.jpg') }}" alt="Foto">
                </div>
                <h5 class="fw-bold">{{ $santri->nama_lengkap }}</h5>
                <p class="text-muted small">{{ $santri->nis }} | {{ $santri->kelas?->nama_kelas }}</p>
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-2"></i> Fokus pada pengisian data klinis santri untuk keperluan rekam medis.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <form action="{{ route('santri.update-health', $santri->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-primary"><i class="bi bi-heart-pulse me-2"></i>Informasi Klinis Dasar</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select select2">
                                <option value="">-</option>
                                <option value="A" {{ ($santri->kesehatan?->golongan_darah == 'A') ? 'selected' : '' }}>A</option>
                                <option value="B" {{ ($santri->kesehatan?->golongan_darah == 'B') ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ ($santri->kesehatan?->golongan_darah == 'AB') ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ ($santri->kesehatan?->golongan_darah == 'O') ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $santri->kesehatan?->tinggi_badan) }}" placeholder="Contoh: 170">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" class="form-control" value="{{ old('berat_badan', $santri->kesehatan?->berat_badan) }}" placeholder="Contoh: 60">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Tekanan Darah Normal</label>
                            <input type="text" name="tekanan_darah" class="form-control" value="{{ old('tekanan_darah', $santri->kesehatan?->tekanan_darah) }}" placeholder="Contoh: 120/80 mmHg">
                        </div>
                        
                        <div class="col-12 mb-3 mt-3">
                            <h6 class="border-bottom pb-2">Riwayat & Sensitivitas</h6>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-danger">Alergi (Obat / Makanan / Debu)</label>
                            <input type="text" name="alergi" class="form-control" value="{{ old('alergi', $santri->kesehatan?->alergi) }}" placeholder="Tuliskan pemicu alergi jika ada">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-warning">Riwayat Penyakit Dahulu / Kronis</label>
                            <textarea name="riwayat_penyakit" class="form-control" rows="2" placeholder="Contoh: Asma sejak kecil, pernah operasi usus buntu">{{ old('riwayat_penyakit', $santri->kesehatan?->riwayat_penyakit) }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Kondisi Khusus / Kebutuhan Tambahan</label>
                            <textarea name="kondisi_khusus" class="form-control" rows="2" placeholder="Contoh: Memakai kacamata, pasca patah tulang kaki kanan">{{ old('kondisi_khusus', $santri->kesehatan?->kondisi_khusus) }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Catatan Kesehatan Tambahan</label>
                            <textarea name="catatan_kesehatan" class="form-control" rows="3">{{ old('catatan_kesehatan', $santri->kesehatan?->catatan_kesehatan) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light">
                    <a href="{{ route('santri.show', $santri->id) }}" class="btn btn-secondary">Batal & Kembali</a>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-save me-2"></i>Simpan Perubahan Kesehatan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
