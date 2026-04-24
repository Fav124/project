@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pusat Analitik Kesehatan DeisaHealth')

@section('content')
{{-- Filter Tanggal --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <form action="{{ route('dashboard') }}" method="GET" class="row align-items-center">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate->toDateString() }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate->toDateString() }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="mdi mdi-filter"></i> Filter Data
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm btn-block"> Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Alert Obat Kadaluarsa --}}
@if($stats['obat_kadaluarsa'] > 0)
<div class="row">
    <div class="col-12 grid-margin">
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert" style="background: rgba(252, 66, 74, 0.1); border-left: 5px solid #fc424a !important;">
            <i class="mdi mdi-alert-octagon mr-3" style="font-size: 24px; color: #fc424a;"></i>
            <div>
                <h5 class="alert-heading mb-1" style="color: #fc424a; font-weight: 800;">PERINGATAN: OBAT KADALUARSA!</h5>
                <p class="mb-0 text-white">Ada <strong>{{ $stats['obat_kadaluarsa'] }} jenis obat</strong> yang sudah kadaluarsa. Mohon segera diperiksa.</p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('medicines.index', ['expired' => 1]) }}" class="btn btn-danger btn-sm font-weight-bold">Periksa Stok</a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Stat Cards --}}
<div class="row">
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-gradient-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box-primary mr-3">
                        <i class="mdi mdi-account-group icon-md"></i>
                    </div>
                    <div>
                        <h6 class="text-muted font-weight-normal mb-1">Total Santri</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['santri_total'] }}</h3>
                        <small class="text-white-50">{{ $stats['santri_l'] }} L / {{ $stats['santri_p'] }} P</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-gradient-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box-success mr-3">
                        <i class="mdi mdi-emoticon-sick icon-md"></i>
                    </div>
                    <div>
                        <h6 class="text-muted font-weight-normal mb-1">Santri Sakit</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['santri_sakit_aktif'] }}</h3>
                        <small class="text-white-50">Sedang Dirawat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-gradient-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box-info mr-3">
                        <i class="mdi mdi-hospital-building icon-md"></i>
                    </div>
                    <div>
                        <h6 class="text-muted font-weight-normal mb-1">Rujukan RS</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['rujukan'] }}</h3>
                        <small class="text-white-50">Periode Ini</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-gradient-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box-danger mr-3">
                        <i class="mdi mdi-pill icon-md"></i>
                    </div>
                    <div>
                        <h6 class="text-muted font-weight-normal mb-1">Stok Kritis</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['obat_menipis'] }}</h3>
                        <small class="text-white-50">Jenis Obat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grafik Utama --}}
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <x-ui.card title="Tren Kunjungan Santri Sakit">
            <div id="visitChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Status Penanganan">
            <div id="statusChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Populasi Santri per Jurusan">
            <div id="majorChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Populasi Santri per Kelas">
            <div id="classChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
</div>

{{-- Aktifitas & Inventori --}}
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <x-ui.card title="Kunjungan UKS Terakhir">
            <x-slot name="header">
                <h4 class="card-title">Kunjungan UKS Terakhir</h4>
                <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
            </x-slot>
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Santri</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCases as $case)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-dark p-1 rounded mr-2">
                                        <i class="mdi mdi-account text-primary"></i>
                                    </div>
                                    <span>{{ $case->santri->name }}</span>
                                </div>
                            </td>
                            <td>{{ Str::limit($case->complaint, 30) }}</td>
                            <td>
                                @php
                                    $statusMap = match($case->status) {
                                        'observed' => ['class' => 'badge-outline-warning', 'label' => 'Observasi'],
                                        'handled' => ['class' => 'badge-outline-info', 'label' => 'Ditangani'],
                                        'recovered' => ['class' => 'badge-outline-success', 'label' => 'Sembuh'],
                                        'referred' => ['class' => 'badge-outline-danger', 'label' => 'Dirujuk'],
                                        default => ['class' => 'badge-outline-secondary', 'label' => $case->status]
                                    };
                                @endphp
                                <div class="badge {{ $statusMap['class'] }}">{{ $statusMap['label'] }}</div>
                            </td>
                            <td>{{ $case->visit_date->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada aktifitas untuk ditampilkan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>
    <div class="col-md-5 grid-margin stretch-card">
        <x-ui.card title="Obat Segera Habis">
            <div class="preview-list">
                @forelse($lowStockMedicines as $medicine)
                    <div class="preview-item border-bottom">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-danger">
                                <i class="mdi mdi-pill"></i>
                            </div>
                        </div>
                        <div class="preview-item-content d-sm-flex flex-grow">
                            <div class="flex-grow">
                                <h6 class="preview-subject">{{ $medicine->name }}</h6>
                                <p class="text-muted mb-0">Tersisa: {{ $medicine->stock }} {{ $medicine->unit }}</p>
                            </div>
                            <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <a href="{{ route('medicines.index', ['search' => $medicine->name]) }}" class="btn btn-outline-danger btn-xs">Update</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 40px;"></i>
                        <p class="text-muted mt-2">Seluruh stok obat dalam kondisi aman</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { 
            theme: 'dark', 
            background: 'transparent', 
            toolbar: { 
                show: true,
                tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true }
            },
            fontFamily: 'Inter, sans-serif'
        },
        grid: { borderColor: '#191c24', strokeDashArray: 4 },
        legend: { labels: { colors: '#6c7293' }, position: 'top' },
        tooltip: { theme: 'dark' }
    };

    // 1. Tren Kunjungan
    new ApexCharts(document.querySelector("#visitChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Santri', data: @json($sicknessTrends->pluck('count')) }],
        chart: { ...chartDefaults.chart, type: 'area', height: 350 },
        colors: ['#00d25b'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($sicknessTrends->pluck('date')),
            labels: { 
                style: { colors: '#6c7293' },
                rotate: -45,
                rotateAlways: false,
                hideOverlappingLabels: true,
                maxHeight: 60
            }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } }
    }).render();

    // 2. Status Penanganan
    @php
        $mappedStatuses = $caseDistribution->pluck('status')->map(fn($s) => match($s) {
            'observed' => 'Observasi',
            'handled' => 'Ditangani',
            'recovered' => 'Sembuh',
            'referred' => 'Dirujuk',
            default => ucfirst($s)
        });
    @endphp
    new ApexCharts(document.querySelector("#statusChart"), {
        ...chartDefaults,
        series: @json($caseDistribution->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 350 },
        labels: @json($mappedStatuses),

        colors: ['#0090e7', '#ffab00', '#00d25b', '#fc424a'],
        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Total', color: '#6c7293' } } } } }
    }).render();

    // 3. Jurusan
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByMajor->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 350 },
        colors: ['#8f5fe8'],
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: {
            categories: @json($santriByMajor->pluck('name')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' }, maxWidth: 150 } }
    }).render();

    // 4. Kelas
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByClass->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 350 },
        colors: ['#ffab00'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        xaxis: {
            categories: @json($santriByClass->pluck('name')),
            labels: { 
                style: { colors: '#6c7293' },
                rotate: -45,
                hideOverlappingLabels: true
            }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } }
    }).render();
</script>
@endpush

<style>
    .card-gradient-primary { background: linear-gradient(135deg, #0090e7, #00d25b); }
    .card-gradient-success { background: linear-gradient(135deg, #00d25b, #0090e7); }
    .card-gradient-info { background: linear-gradient(135deg, #8f5fe8, #0090e7); }
    .card-gradient-danger { background: linear-gradient(135deg, #fc424a, #ffab00); }
    
    .icon-box-primary, .icon-box-success, .icon-box-info, .icon-box-danger {
        width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.15);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .icon-md { font-size: 24px; color: white; }
    
    .avatar-sm { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
</style>
