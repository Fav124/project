@extends('layouts.app')

@section('title', 'Kunjungan & Pemeriksaan')
@section('page_title', 'Daftar Kunjungan Santri')
@section('page_description', 'Pantau pemeriksaan harian, keluhan santri, dan diagnosa petugas.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kunjungan</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Log Pemeriksaan Terakhir</h4>
        <a href="{{ route('kunjungan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Pemeriksaan Baru
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('kunjungan.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama Santri..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Santri</th>
                        <th>Keluhan</th>
                        <th>Diagnosa</th>
                        <th>Status</th>
                        <th>Kondisi Saat Ini</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kunjungans as $kunjungan)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $kunjungan->tanggal_kunjungan->translatedFormat('d F Y') }}</div>
                            <small class="text-muted">{{ $kunjungan->tanggal_kunjungan->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $kunjungan->santri->nama_lengkap }}</div>
                            <small class="text-muted">{{ $kunjungan->santri->nis }}</small>
                        </td>
                        <td><small>{{ Str::limit($kunjungan->keluhan_utama, 30) }}</small></td>
                        <td><span class="text-info fw-bold">{{ $kunjungan->diagnosa_sementara ?: '-' }}</span></td>
                        <td>
                            @php
                                $status = $kunjungan->status_kunjungan;
                                $badgeClass = match($status) {
                                    'sembuh' => 'success',
                                    'rawat_inap' => 'warning',
                                    'dirujuk' => 'danger',
                                    'pulang' => 'info',
                                    default => 'secondary'
                                };
                                
                                // Override if there's an associated medical case
                                if ($kunjungan->kasusSakit) {
                                    if ($kunjungan->kasusSakit->status_kasus === 'sembuh') {
                                        $status = 'sembuh';
                                        $badgeClass = 'success';
                                    } elseif ($kunjungan->kasusSakit->riwayatAktif) {
                                        $lokasi = $kunjungan->kasusSakit->riwayatAktif->lokasi_perawatan;
                                        if ($lokasi === 'rumah_sakit') {
                                            $status = 'dirujuk';
                                            $badgeClass = 'danger';
                                        } elseif ($lokasi === 'uks') {
                                            $status = 'rawat_inap';
                                            $badgeClass = 'warning';
                                        }
                                    }
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">
                                {{ strtoupper($status) }}
                            </span>
                        </td>
                        <td>
                            @if($kunjungan->kasusSakit && $kunjungan->kasusSakit->status_kasus === 'aktif')
                                @php $riwayat = $kunjungan->kasusSakit->riwayatAktif; @endphp
                                @if($riwayat)
                                    @if($riwayat->lokasi_perawatan === 'uks')
                                        <span class="text-warning small fw-bold"><i class="bi bi-hospital me-1"></i> Masih di UKS</span>
                                    @elseif($riwayat->lokasi_perawatan === 'rumah_sakit')
                                        <span class="text-danger small fw-bold"><i class="bi bi-building me-1"></i> Masih di RS</span>
                                        @if($riwayat->nama_rs)
                                            <div class="x-small text-muted">({{ $riwayat->nama_rs }})</div>
                                        @endif
                                    @elseif($riwayat->lokasi_perawatan === 'rumah')
                                        <span class="text-info small fw-bold"><i class="bi bi-house me-1"></i> Masih di Rumah</span>
                                    @else
                                        <span class="text-secondary small fw-bold"><i class="bi bi-person-check me-1"></i> Pemulihan</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-success small"><i class="bi bi-check-all"></i> Selesai / Sembuh</span>
                            @endif
                        </td>
                        <td><small>{{ $kunjungan->petugas->name }}</small></td>
                        <td>
                            <a href="{{ route('kunjungan.show', $kunjungan->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Belum ada kunjungan hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $kunjungans->links() }}
        </div>
    </div>
</div>
@endsection
