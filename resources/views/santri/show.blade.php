@extends('layouts.app')

@section('title', 'Detail Santri')
@section('page_title', 'Detail Santri: ' . $santri->nama_lengkap)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('santri.index') }}">Santri</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-center mb-4">
                    <div class="formal-photo-container">
                        <img src="{{ $santri->foto ? asset('storage/' . $santri->foto) : asset('assets/images/faces/1.jpg') }}" 
                             class="formal-photo" alt="Foto Santri">
                    </div>
                </div>
                <div class="text-center">
                    <h5 class="fw-bold">{{ $santri->nama_lengkap }}</h5>
                    <p class="text-muted">{{ $santri->nis }} / {{ $santri->nisn ?? '-' }}</p>
                    <span class="badge bg-{{ $santri->status_santri === 'aktif' ? 'success' : 'secondary' }}">
                        {{ strtoupper($santri->status_santri) }}
                    </span>
                </div>
                <hr>
                <div class="mt-4">
                    <p class="mb-1 text-muted small">Kelas</p>
                    <p class="fw-bold">{{ $santri->kelas?->nama_kelas ?? '-' }}</p>
                    
                    <p class="mb-1 text-muted small">Jurusan</p>
                    <p class="fw-bold">{{ $santri->jurusan?->nama_jurusan ?? '-' }}</p>

                    <p class="mb-1 text-muted small">Kamar</p>
                    <p class="fw-bold">{{ $santri->kamar?->nama_kamar ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profil & Wali</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="health-tab" data-bs-toggle="tab" href="#health" role="tab" aria-controls="health" aria-selected="false">Data Kesehatan</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    {{-- Tab Profil & Wali --}}
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Informasi Pribadi</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td>Tempat Lahir</td><td>: {{ $santri->tempat_lahir }}</td></tr>
                                    <tr><td>Tanggal Lahir</td><td>: {{ $santri->tanggal_lahir?->translatedFormat('d F Y') }}</td></tr>
                                    <tr><td>Jenis Kelamin</td><td>: {{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                                    <tr><td>Alamat</td><td>: {{ $santri->alamat }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Data Wali Santri</h6>
                                @forelse($santri->waliSantris as $wali)
                                <div class="border-start border-primary ps-3 mb-3">
                                    <p class="mb-0 fw-bold">{{ $wali->nama_wali }} ({{ $wali->hubungan_wali }})</p>
                                    <small class="text-muted">{{ $wali->no_hp }} | {{ $wali->pekerjaan }}</small>
                                </div>
                                @empty
                                <p class="text-muted">Belum ada data wali.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Tab Kesehatan --}}
                    <div class="tab-pane fade" id="health" role="tabpanel" aria-labelledby="health-tab">
                        <div class="row mt-3">
                            @if($santri->kesehatan)
                            <div class="col-md-6">
                                <h6>Fisik Dasar</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td>Golongan Darah</td><td>: <span class="badge bg-danger">{{ $santri->kesehatan->golongan_darah ?? '-' }}</span></td></tr>
                                    <tr><td>Tinggi Badan</td><td>: {{ $santri->kesehatan->tinggi_badan }} cm</td></tr>
                                    <tr><td>Berat Badan</td><td>: {{ $santri->kesehatan->berat_badan }} kg</td></tr>
                                    <tr><td>Tekanan Darah</td><td>: {{ $santri->kesehatan->tekanan_darah ?? '-' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Riwayat & Kondisi</h6>
                                <p class="mb-1 text-muted small">Alergi</p>
                                <p class="text-danger fw-bold">{{ $santri->kesehatan->alergi ?: 'Tidak ada' }}</p>
                                
                                <p class="mb-1 text-muted small">Riwayat Penyakit</p>
                                <p>{{ $santri->kesehatan->riwayat_penyakit ?: '-' }}</p>
                                
                                <p class="mb-1 text-muted small">Catatan Kesehatan</p>
                                <p class="italic">"{{ $santri->kesehatan->catatan_kesehatan ?: 'Tidak ada catatan.' }}"</p>
                            </div>
                            @else
                            <div class="col-12 text-center py-4">
                                <p class="text-muted">Data kesehatan utama belum diisi.</p>
                                <a href="{{ route('santri.edit-health', $santri->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-2"></i> Lengkapi Data Sekarang
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('santri.history', $santri->id) }}" class="btn btn-outline-info">
                    <i class="bi bi-file-medical"></i> Lihat Riwayat Medis Lengkap
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('santri.edit', $santri->id) }}" class="btn btn-warning text-white">Edit Data</a>
                    <a href="{{ route('santri.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
