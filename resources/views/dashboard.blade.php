@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pusat Analitik Kesehatan DEIHealth')

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
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert" style="background: rgba(239, 68, 68, 0.1); border-left: 5px solid var(--danger) !important;">
            <i class="mdi mdi-alert-octagon mr-3" style="font-size: 24px; color: var(--danger);"></i>
            <div>
                <h5 class="alert-heading mb-1" style="color: var(--danger); font-weight: 800;">PERINGATAN: OBAT KADALUARSA!</h5>
                <p class="mb-0 text-dark">Ada <strong>{{ $stats['obat_kadaluarsa'] }} jenis obat</strong> yang sudah kadaluarsa. Mohon segera diperiksa.</p>
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
        <div class="stat-card stat-primary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-label">Total Santri</div>
                <div class="stat-icon">
                    <i class="mdi mdi-account-group"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['santri_total'] }}</div>
            <div class="stat-sub mt-1">{{ $stats['santri_l'] }} L / {{ $stats['santri_p'] }} P</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="stat-card stat-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-label">Santri Sakit</div>
                <div class="stat-icon">
                    <i class="mdi mdi-emoticon-sick"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['santri_sakit_aktif'] }}</div>
            <div class="stat-sub mt-1">Sedang Dirawat</div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="stat-card stat-info">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-label">Rujukan RS</div>
                <div class="stat-icon">
                    <i class="mdi mdi-hospital-building"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['rujukan'] }}</div>
            <div class="stat-sub mt-1">Periode Ini</div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="stat-card stat-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-label">Stok Kritis</div>
                <div class="stat-icon">
                    <i class="mdi mdi-pill"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['obat_menipis'] }}</div>
            <div class="stat-sub mt-1">Jenis Obat</div>
        </div>
    </div>
</div>

{{-- Grafik Utama --}}
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">Tren Kunjungan Santri Sakit</h4>
                <div id="visitChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">Status Penanganan</h4>
                <div id="statusChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">Populasi Santri per Jurusan</h4>
                <div id="majorChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">Populasi Santri per Kelas</h4>
                <div id="classChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Aktifitas & Inventori --}}
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Kunjungan UKS Terakhir</h4>
                    <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
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
                                            <div class="avatar-sm bg-primary-light text-primary rounded mr-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: rgba(2, 120, 212, 0.12);">
                                                <i class="mdi mdi-account"></i>
                                            </div>
                                            <span class="font-weight-medium">{{ $case->santri->name }}</span>
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
                                    <td>{{ $case->visit_date->translatedFormat('d F Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada aktifitas untuk ditampilkan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title mb-4">Obat Segera Habis</h4>
                <div class="preview-list">
                    @forelse($lowStockMedicines as $medicine)
                        <div class="preview-item border-bottom px-0">
                            <div class="preview-thumbnail">
                                <div class="preview-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 12px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="mdi mdi-pill"></i>
                                </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow ml-3">
                                <div class="flex-grow">
                                    <h6 class="preview-subject font-weight-medium mb-1">{{ $medicine->name }}</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Tersisa: {{ $medicine->stock }} {{ $medicine->unit }}</p>
                                </div>
                                <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                    <a href="{{ route('medicines.index', ['search' => $medicine->name]) }}" class="btn btn-outline-danger btn-sm px-3">Update</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="mdi mdi-check-decagram text-success" style="font-size: 48px;"></i>
                            <h6 class="font-weight-medium mt-3">Stok Obat Aman</h6>
                            <p class="text-muted text-sm mt-1 mb-0">Seluruh stok obat dalam kondisi aman</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { 
            theme: 'light', 
            background: 'transparent', 
            toolbar: { 
                show: true,
                tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true }
            },
            fontFamily: 'Inter, sans-serif'
        },
        grid: { borderColor: 'var(--border)', strokeDashArray: 4 },
        legend: { labels: { colors: 'var(--text)' }, position: 'top' },
        tooltip: { theme: 'light' }
    };

    // 1. Tren Kunjungan
    new ApexCharts(document.querySelector("#visitChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Santri', data: @json($sicknessTrends->pluck('count')) }],
        chart: { ...chartDefaults.chart, type: 'area', height: 350 },
        colors: ['#0278D4'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($sicknessTrends->pluck('date_label')),
            labels: { 
                style: { colors: 'var(--text-secondary)' },
                rotate: -45,
                rotateAlways: false,
                hideOverlappingLabels: true,
                maxHeight: 60
            }
        },
        yaxis: { labels: { style: { colors: 'var(--text-secondary)' } } },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.2, opacityTo: 0 } }
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

        colors: ['#F59E0B', '#0278D4', '#10B981', '#EF4444'],
        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Total', color: 'var(--text)' } } } } }
    }).render();

    // 3. Jurusan
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByMajor->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 350 },
        colors: ['#12306F'],
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: {
            categories: @json($santriByMajor->pluck('name')),
            labels: { style: { colors: 'var(--text-secondary)' } }
        },
        yaxis: { labels: { style: { colors: 'var(--text-secondary)' }, maxWidth: 150 } }
    }).render();

    // 4. Kelas
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByClass->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 350 },
        colors: ['#FF8C00'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        xaxis: {
            categories: @json($santriByClass->pluck('name')),
            labels: { 
                style: { colors: 'var(--text-secondary)' },
                rotate: -45,
                hideOverlappingLabels: true
            }
        },
        yaxis: { labels: { style: { colors: 'var(--text-secondary)' } } }
    }).render();
</script>
@endpush
