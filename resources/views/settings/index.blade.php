@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Konfigurasi Aplikasi')
@section('page_description', 'Atur identitas pondok, ambang batas stok, dan preferensi sistem lainnya.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Identitas & Operasional</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Sekolah / Institusi</label>
                            <input type="text" name="institution_name" class="form-control" value="{{ $settings['institution_name'] ?? ($settings['pondok_name'] ?? 'Dar El-Ilmi') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo Institusi</label>
                            <input type="file" name="institution_logo" class="form-control" accept="image/*">
                            @if(isset($settings['institution_logo']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['institution_logo']) }}" alt="Logo" style="height: 50px;" class="border rounded p-1">
                                    <small class="text-muted d-block">Logo saat ini</small>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Sekolah (NPSN)</label>
                            <input type="text" name="institution_npsn" class="form-control" value="{{ $settings['institution_npsn'] ?? '' }}" placeholder="Masukkan NPSN sekolah...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kepala Sekolah / Mudir</label>
                            <input type="text" name="kepala_sekolah" class="form-control" value="{{ $settings['kepala_sekolah'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP / NIY Kepala Sekolah</label>
                            <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ $settings['nip_kepala_sekolah'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat Lengkap Institusi</label>
                            <textarea name="institution_address" class="form-control" rows="1">{{ $settings['institution_address'] ?? '' }}</textarea>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3">Ambang Batas (Alert Thresholds)</h6>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Default Stok Minimum</label>
                            <div class="input-group">
                                <input type="number" name="default_min_stock" class="form-control" value="{{ $settings['default_min_stock'] ?? 5 }}">
                                <span class="input-group-text">Item</span>
                            </div>
                            <small class="text-muted">Digunakan jika stok minimum obat tidak ditentukan.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warning Kadaluarsa</label>
                            <div class="input-group">
                                <input type="number" name="batas_hampir_kadaluarsa_hari" class="form-control" value="{{ $settings['batas_hampir_kadaluarsa_hari'] ?? 90 }}">
                                <span class="input-group-text">Hari</span>
                            </div>
                            <small class="text-muted">Peringatan muncul N hari sebelum expired.</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Informasi Sistem</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Laravel Version</small>
                    <span class="fw-bold">13.0.0</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">PHP Version</small>
                    <span class="fw-bold">8.4.0</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Timezone</small>
                    <span class="fw-bold">{{ config('app.timezone') }}</span>
                </div>
                <hr>
                <p class="small text-muted">Pastikan konfigurasi di atas sesuai dengan kebijakan operasional klinik UKS Dar El-Ilmi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
