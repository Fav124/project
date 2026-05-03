@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')
@section('page_title', 'Laporan Operasional Kesehatan')
@section('page_description', 'Analisis data kunjungan, penggunaan obat, dan status kesehatan santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="bi bi-gear-fill me-2"></i> Konfigurasi Laporan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.index') }}" method="GET" id="laporanForm">
                    <div class="row">
                        {{-- Periode --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>

                        {{-- Pejabat Tanda Tangan --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Kepala Sekolah / Mudir</label>
                            <input type="text" name="kepala_sekolah" class="form-control" value="{{ \App\Models\Setting::get('kepala_sekolah', '') }}" placeholder="Nama Lengkap & Gelar">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">NIP / NIY / NPSN</label>
                            <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ \App\Models\Setting::get('nip_kepala_sekolah', '') }}" placeholder="Nomor Induk / Nomor Sekolah">
                        </div>

                        {{-- Checklist Data --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold d-block">Data yang Ingin Ditampilkan:</label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="kunjungan" id="checkKunjungan" checked>
                                    <label class="form-check-label" for="checkKunjungan">Rekap Kunjungan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="detail_pasien" id="checkDetailPasien">
                                    <label class="form-check-label" for="checkDetailPasien">Daftar Pasien & Penanganan (Sangat Detail)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="obat" id="checkObat" checked>
                                    <label class="form-check-label" for="checkObat">Obat Terlaris</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="detail_obat_kadaluarsa" id="checkDetailKadaluarsa">
                                    <label class="form-check-label" for="checkDetailKadaluarsa">Daftar Obat Kadaluarsa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="detail_obat_hampir" id="checkDetailHampir">
                                    <label class="form-check-label" for="checkDetailHampir">Daftar Obat Hampir Kadaluarsa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="detail_obat_stok" id="checkDetailStok">
                                    <label class="form-check-label" for="checkDetailStok">Daftar Stok Menipis</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="inventaris" id="checkInventaris" checked>
                                    <label class="form-check-label" for="checkInventaris">Ringkasan Statistik Inventaris</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="include_sections[]" value="rawat_inap" id="checkRawat" checked>
                                    <label class="form-check-label" for="checkRawat">Ringkasan Rawat Inap</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="action" value="filter" class="btn btn-outline-primary"><i class="bi bi-filter"></i> Perbarui Preview Dashboard</button>
                        <button type="button" onclick="printLaporan()" class="btn btn-danger px-4">
                            <i class="bi bi-printer me-2"></i> Cetak Laporan Resmi
                        </button>
                    </div>
                </form>

                {{-- Hidden Iframe for Printing --}}
                <iframe id="printIframe" style="display:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Tren Kunjungan --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Tren Kunjungan</h4>
            </div>
            <div class="card-body">
                <div id="chart-kunjungan"></div>
            </div>
        </div>
    </div>

    {{-- Status Obat --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Status Inventaris Obat</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Kadaluarsa</span>
                        <span class="fw-bold text-danger">{{ $statusObat['kadaluarsa'] }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Stok Menipis</span>
                        <span class="fw-bold text-warning">{{ $statusObat['stok_menipis'] }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Stok Habis</span>
                        <span class="fw-bold text-dark">{{ $statusObat['stok_habis'] }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-dark" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Obat Paling Sering Digunakan --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>5 Obat Paling Sering Digunakan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Obat</th>
                                <th>Frekuensi</th>
                                <th>Total Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($obatTerlaris as $obat)
                            <tr>
                                <td>{{ $obat->obat->nama_obat }}</td>
                                <td>{{ $obat->frekuensi_diberikan }} kali</td>
                                <td class="fw-bold text-primary">{{ (int)$obat->total_keluar }} {{ $obat->obat->satuan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Rawat Inap --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Ringkasan Rawat Inap</h4>
            </div>
            <div class="card-body">
                <div class="row text-center py-3">
                    @foreach($rawatInap as $ri)
                    <div class="col-4">
                        <h3 class="fw-extrabold text-{{ $ri->status_rawat === 'aktif' ? 'warning' : 'success' }}">{{ $ri->total }}</h3>
                        <p class="text-muted text-sm">{{ strtoupper($ri->status_rawat) }}</p>
                    </div>
                    @endforeach
                    @if($rawatInap->isEmpty())
                        <p class="text-muted">Tidak ada data rawat inap di periode ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
<script>
    var options = {
        series: [{
            name: 'Kunjungan',
            data: {!! json_encode($rekapKunjungan->pluck('total')) !!}
        }],
        chart: {
            height: 300,
            type: 'area'
        },
        xaxis: {
            categories: {!! json_encode($rekapKunjungan->pluck('periode')) !!}
        },
        colors: ['#435ebe']
    };
    var chart = new ApexCharts(document.querySelector("#chart-kunjungan"), options);
    chart.render();

    function printLaporan() {
        const form = document.getElementById('laporanForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            params.append(key, value);
        }
        params.append('action', 'print');
        
        const printUrl = "{{ route('laporan.index') }}?" + params.toString();
        const iframe = document.getElementById('printIframe');
        
        // Show loading state if you want, but direct load is usually fast
        iframe.src = printUrl;
    }

    function closePrintIframe() {
        const iframe = document.getElementById('printIframe');
        iframe.src = 'about:blank';
    }
</script>
@endpush
