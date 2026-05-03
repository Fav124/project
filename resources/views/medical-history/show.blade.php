@extends('layouts.app')

@section('title', 'Riwayat Kesehatan')
@section('page_title', 'Rekam Medis Kronologis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('santri.index') }}">Santri</a></li>
    <li class="breadcrumb-item active" aria-current="page">Riwayat Kesehatan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                    <img src="{{ $santri->foto ? asset('storage/' . $santri->foto) : asset('assets/images/faces/1.jpg') }}" alt="Foto Santri">
                </div>
                <h5 class="fw-bold">{{ $santri->nama_lengkap }}</h5>
                <p class="text-muted small">{{ $santri->nis }} | {{ $santri->kelas?->nama_kelas }}</p>
                <hr>
                <div class="text-start">
                    <h6>Informasi Klinis Dasar</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Gol. Darah</span>
                        <span class="badge bg-danger">{{ $santri->kesehatan?->golongan_darah ?: '-' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Alergi:</small><br>
                        <strong class="text-danger">{{ $santri->kesehatan?->alergi ?: 'Tidak ada' }}</strong>
                    </div>
                    <div>
                        <small class="text-muted">Riwayat Penyakit:</small><br>
                        <strong>{{ $santri->kesehatan?->riwayat_penyakit ?: 'Tidak ada' }}</strong>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-outline-primary w-100"><i class="bi bi-printer"></i> Cetak Resume Medis</button>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Timeline Pemeriksaan</h4>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($history as $visit)
                    <div class="pb-4 border-start border-primary border-3 ps-4 position-relative">
                        <div class="position-absolute bg-primary rounded-circle" style="width:12px; height:12px; left:-8px; top:0;"></div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">{{ $visit->tanggal_kunjungan->format('d F Y') }} <small class="text-muted">({{ $visit->tanggal_kunjungan->format('H:i') }})</small></h6>
                            <span class="badge bg-light-info text-info">Oleh: {{ $visit->petugas->name }}</span>
                        </div>
                        
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Keluhan:</small>
                                    <p class="mb-2">"{{ $visit->keluhan }}"</p>
                                    
                                    <small class="text-muted d-block">Diagnosa:</small>
                                    <p class="mb-2 fw-bold">{{ $visit->diagnosa_sementara ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tindakan:</small>
                                    <p class="mb-2">{{ $visit->tindakan ?: '-' }}</p>

                                    @if($visit->rawatInap)
                                    <div class="badge bg-warning text-dark mb-2">
                                        <i class="bi bi-hospital"></i> Rawat Inap ({{ $visit->rawatInap->status_rawat }})
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if($visit->pemberianObats->isNotEmpty())
                            <div class="mt-2 border-top pt-2">
                                <small class="text-muted d-block mb-1">Obat Diberikan:</small>
                                @foreach($visit->pemberianObats as $p)
                                <span class="badge bg-secondary me-1">{{ $p->obat->nama_obat }} ({{ $p->jumlah }} {{ $p->obat->satuan }}) - {{ $p->aturan_pakai }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <p class="text-muted">Belum ada riwayat kunjungan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
