@extends('layouts.app')

@section('title', 'Detail Kunjungan Medis')
@section('page_title', 'Hasil Pemeriksaan Santri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kunjungan.index') }}">Kunjungan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@push('styles')
<style>
    @media print {
        /* Hide ALL UI elements aggressively */
        #sidebar, 
        .sidebar-wrapper, 
        header.mb-3, 
        .navbar, 
        .breadcrumb-header, 
        .btn, 
        .card-footer, 
        .card-header.bg-light,
        footer {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Reset layout for print */
        #main {
            margin-left: 0 !important;
            padding: 0 !important;
            display: block !important;
        }
        
        #app {
            display: block !important;
        }

        .container-fluid, #main-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .row {
            display: block !important;
        }

        .col-md-4, .col-md-8 {
            width: 100% !important;
            display: block !important;
            margin-bottom: 20px;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Print Header */
        .print-header {
            display: block !important;
            text-align: center;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .print-header h2 {
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .print-header p {
            margin: 0;
            font-size: 14px;
        }

        /* Signature Section */
        .print-signature {
            display: flex !important;
            justify-content: flex-end;
            margin-top: 50px;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-space {
            height: 80px;
        }

        /* Typography */
        body {
            font-size: 12pt;
            color: #000;
            background: #fff !important;
        }

        h4, h5, h6 {
            color: #000 !important;
            font-weight: bold !important;
        }

        .badge {
            border: 1px solid #000 !important;
            color: #000 !important;
            background: none !important;
        }

        .bg-light {
            background: #fff !important;
            border: 1px solid #ddd !important;
        }

        .timeline {
            border-left: 1px solid #000 !important;
        }
    }

    /* Hide print elements on screen */
    .print-header, .print-signature {
        display: none;
    }
</style>
@endpush

@section('content')
{{-- Print Header (Visible only when printing) --}}
<div class="print-header">
    <h2>KLINIK PRATAMA DAR EL-ILMI</h2>
    <p>Jl. Raya Serang No. 123, Balaraja, Tangerang</p>
    <p>Telp: (021) 12345678 | Email: kesehatan@darelilmi.sch.id</p>
    <hr style="margin-top: 10px; margin-bottom: 2px; border-top: 1px solid #000;">
    <h3 class="mt-3">LAPORAN PEMERIKSAAN KESEHATAN SANTRI</h3>
</div>

<div class="row">
    {{-- Profil Singkat Santri --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="formal-photo-container mb-3">
                    <img src="{{ $kunjungan->santri->foto ? asset('storage/' . $kunjungan->santri->foto) : asset('assets/images/faces/2.jpg') }}" 
                         class="formal-photo" style="width: 100px; height: 130px; object-fit: cover; border: 1px solid #ddd;" alt="Foto Santri">
                </div>
                <h5 class="mb-1 fw-bold">{{ $kunjungan->santri->nama_lengkap }}</h5>
                <p class="text-muted small mb-3">{{ $kunjungan->santri->nis }} | {{ $kunjungan->santri->kelas?->nama_kelas }}</p>
                
                <div class="d-grid gap-2 d-print-none">
                    <a href="{{ route('santri.show', $kunjungan->santri_id) }}" class="btn btn-outline-primary btn-sm">Lihat Profil Lengkap</a>
                </div>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-transparent d-print-none">
                <h5 class="card-title">Informasi Kunjungan</h5>
            </div>
            <div class="card-body border rounded p-3">
                <div class="mb-3">
                    <label class="text-muted small d-block">Waktu Periksa</label>
                    <span class="fw-bold">{{ $kunjungan->tanggal_kunjungan->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Petugas Medis</label>
                    <span class="fw-bold">{{ $kunjungan->petugas->name }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Status Saat Ini</label>
                    @php
                        $status = $kunjungan->status_kunjungan;
                        $badgeClass = match($status) {
                            'sembuh' => 'success',
                            'rawat_inap' => 'warning',
                            'dirujuk' => 'danger',
                            default => 'secondary'
                        };

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
                    <span class="badge bg-{{ $badgeClass }}">{{ strtoupper($status) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Pemeriksaan --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light d-print-none">
                <h4 class="card-title">Hasil Diagnosa & Tindakan</h4>
            </div>
            <div class="card-body mt-2">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-1">Keluhan Utama</h6>
                        <p class="p-2 bg-light rounded border mb-0">{{ $kunjungan->keluhan_utama }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-1">Riwayat Keluhan / Anamnesis</h6>
                        <p>{{ $kunjungan->riwayat_keluhan ?: '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-1">Diagnosa Sementara</h6>
                        <p class="text-primary fw-bold">{{ $kunjungan->diagnosa_sementara ?: '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-1">Tindakan Medis yang Dilakukan</h6>
                        <p>{{ $kunjungan->tindakan ?: '-' }}</p>
                    </div>
                </div>

                @if($kunjungan->pemberianObats->count() > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="border-bottom pb-1">Pemberian Obat (Resep)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th>Jumlah</th>
                                        <th>Aturan Pakai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kunjungan->pemberianObats as $pemberian)
                                    <tr>
                                        <td>{{ $pemberian->obat->nama_obat }}</td>
                                        <td>{{ $pemberian->jumlah }} {{ $pemberian->obat->satuan }}</td>
                                        <td>{{ $pemberian->aturan_pakai }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if($kunjungan->kasusSakit)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="p-3 bg-light-warning rounded border">
                            <h6 class="mb-2 text-dark fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Penanganan Selama Sakit</h6>
                            <div class="timeline ps-3 border-start">
                                @foreach($kunjungan->kasusSakit->riwayats->sortBy('tanggal_masuk') as $riwayat)
                                <div class="mb-2 position-relative">
                                    <i class="bi bi-dot position-absolute" style="left: -23px; top: -5px; font-size: 2rem; color: #ffc107;"></i>
                                    <div class="fw-bold small">{{ $riwayat->tanggal_masuk->translatedFormat('d F Y, H:i') }}</div>
                                    <div class="small">
                                        Status: <strong>{{ strtoupper(str_replace('_', ' ', $riwayat->lokasi_perawatan)) }}</strong>
                                        @if($riwayat->lokasi_perawatan === 'rumah_sakit' && $riwayat->nama_rs)
                                            <span class="text-danger fw-bold"> - {{ $riwayat->nama_rs }}</span>
                                        @elseif($riwayat->lokasi_perawatan === 'rumah' && $riwayat->penjemput)
                                            <span class="text-info fw-bold"> - Dijemput: {{ $riwayat->penjemput }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        Kondisi: {{ $riwayat->kondisi_masuk }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-3 border-top pt-2">
                    <h6 class="small fw-bold">Catatan Tambahan / Pesan untuk Wali:</h6>
                    <p class="fst-italic text-muted small mb-0">{{ $kunjungan->catatan ?: 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>

            {{-- Signature Section (Visible only when printing) --}}
            <div class="print-signature">
                <div class="signature-box">
                    <p>Tangerang, {{ now()->translatedFormat('d F Y') }}</p>
                    <p>Petugas Medis,</p>
                    <div class="signature-space"></div>
                    <p class="fw-bold text-decoration-underline">{{ $kunjungan->petugas->name }}</p>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between d-print-none">
                <a href="{{ route('kunjungan.index') }}" class="btn btn-light">Kembali ke Daftar</a>
                <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i>Cetak Hasil Pemeriksaan</button>
            </div>
        </div>
    </div>
</div>
@endsection
