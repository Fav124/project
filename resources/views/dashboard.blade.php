@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pusat Analitik Kesehatan DeisaHealth')

@section('content')
{{-- Alert Obat Kadaluarsa --}}
@if($stats['obat_kadaluarsa'] > 0)
<div class="row">
    <div class="col-12 grid-margin">
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert" style="background: rgba(252, 66, 74, 0.1); border-left: 5px solid #fc424a !important;">
            <i class="mdi mdi-alert-octagon mr-3" style="font-size: 24px; color: #fc424a;"></i>
            <div>
                <h5 class="alert-heading mb-1" style="color: #fc424a; font-weight: 800;">PERINGATAN: OBAT KADALUARSA TERDETEKSI!</h5>
                <p class="mb-0 text-white">Terdapat <strong>{{ $stats['obat_kadaluarsa'] }} jenis obat</strong> yang telah melewati masa berlaku. Segera lakukan pengecekan.</p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('medicines.index', ['expired' => 1]) }}" class="btn btn-danger btn-sm font-weight-bold">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Stat Cards Row 1 --}}
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
                        <h6 class="text-muted font-weight-normal mb-1">Pasien Aktif</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['santri_sakit_aktif'] }}</h3>
                        <small class="text-white-50">Kunjungan UKS</small>
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
                        <i class="mdi mdi-bed-empty icon-md"></i>
                    </div>
                    <div>
                        <h6 class="text-muted font-weight-normal mb-1">Kasur UKS</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['kasur_tersedia'] }}</h3>
                        <small class="text-white-50">Tersedia</small>
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
                        <h6 class="text-muted font-weight-normal mb-1">Stok Obat</h6>
                        <h3 class="mb-0 font-weight-bold">{{ $stats['obat_menipis'] }}</h3>
                        <small class="text-white-50">Stok Kritis</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Charts Row --}}
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <x-ui.card title="Tren Kunjungan Pasien (14 Hari Terakhir)">
            <div id="visitChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Distribusi Kasus">
            <div id="statusChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
</div>

{{-- Secondary Charts Row --}}
<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Santri per Jurusan">
            <div id="majorChart" style="min-height: 300px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Santri per Kelas">
            <div id="classChart" style="min-height: 300px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Status Kadaluarsa Obat">
            <div id="expiryChart" style="min-height: 300px;"></div>
        </x-ui.card>
    </div>
</div>

{{-- Recent Activities & Inventory --}}
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <x-ui.card title="Aktifitas UKS Terkini">
            <x-slot name="header">
                <h4 class="card-title">Aktifitas UKS Terkini</h4>
                <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
            </x-slot>
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Santri</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Obat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCases as $case)
                        <tr>
                            <td>{{ $case->santri->name }}</td>
                            <td>{{ Str::limit($case->complaint, 20) }}</td>
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
                            <td>
                                @foreach($case->medicines as $med)
                                    <span class="badge badge-primary btn-xs">{{ $med->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada aktifitas hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>
    <div class="col-md-5 grid-margin stretch-card">
        <x-ui.card title="Inventori Kritis">
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
                                <p class="text-muted mb-0">Sisa: {{ $medicine->stock }} {{ $medicine->unit }}</p>
                            </div>
                            <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <a href="{{ route('medicines.index', ['search' => $medicine->name]) }}" class="btn btn-outline-danger btn-xs">Update</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Seluruh stok obat aman</p>
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
        chart: { theme: 'dark', background: 'transparent', toolbar: { show: false } },
        grid: { borderColor: '#191c24' },
        legend: { labels: { colors: '#6c7293' } }
    };

    // 1. Visit Trends Chart
    new ApexCharts(document.querySelector("#visitChart"), {
        ...chartDefaults,
        series: [{ name: 'Kunjungan', data: @json($sicknessTrends->pluck('count')) }],
        chart: { ...chartDefaults.chart, type: 'area', height: 350 },
        colors: ['#00d25b'],
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($sicknessTrends->pluck('date')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } }
    }).render();

    // 2. Case Distribution Chart
    new ApexCharts(document.querySelector("#statusChart"), {
        ...chartDefaults,
        series: @json($caseDistribution->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 350 },
        labels: @json($caseDistribution->pluck('status')),
        colors: ['#0090e7', '#ffab00', '#00d25b', '#fc424a'],
        plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Total', color: '#6c7293' } } } } }
    }).render();

    // 3. Major Distribution
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByMajor->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 300 },
        colors: ['#8f5fe8'],
        xaxis: {
            categories: @json($santriByMajor->pluck('name')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } }
    }).render();

    // 4. Class Distribution
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($santriByClass->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 300 },
        colors: ['#ffab00'],
        xaxis: {
            categories: @json($santriByClass->pluck('name')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } }
    }).render();

    // 5. Expiry Status
    new ApexCharts(document.querySelector("#expiryChart"), {
        ...chartDefaults,
        series: [@json($medicineExpiry['expired']), @json($medicineExpiry['expiring_soon']), @json($medicineExpiry['safe'])],
        chart: { ...chartDefaults.chart, type: 'pie', height: 300 },
        labels: ['Kadaluarsa', 'Hampir Kadaluarsa', 'Aman'],
        colors: ['#fc424a', '#ffab00', '#00d25b']
    }).render();
</script>
@endpush

<style>
    .card-gradient-primary { background: linear-gradient(to right, #0090e7, #00d25b); }
    .card-gradient-success { background: linear-gradient(to right, #00d25b, #0090e7); }
    .card-gradient-info { background: linear-gradient(to right, #8f5fe8, #0090e7); }
    .card-gradient-danger { background: linear-gradient(to right, #fc424a, #ffab00); }
    
    .icon-box-primary, .icon-box-success, .icon-box-info, .icon-box-danger {
        width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2);
    }
    .icon-md { font-size: 24px; color: white; }
</style>
