@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Ringkasan')
@section('page_description', 'Selamat datang di sistem manajemen kesehatan DEI Health.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="row">
    {{-- Main Stats --}}
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon purple">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Santri Sakit</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['santri_sakit_hari_ini'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon blue">
                            <i class="bi bi-journal-medical text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Kunjungan Hari Ini</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['kunjungan_hari_ini'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon green">
                            <i class="bi bi-hospital text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Rawat Inap Aktif</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['rawat_inap_aktif'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon red">
                            <i class="bi bi-capsule text-white"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Santri</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['total_santri'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Left Column: Charts --}}
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Tren Kunjungan (7 Hari Terakhir)</h4>
            </div>
            <div class="card-body">
                <div id="chart-kunjungan"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Aktivitas Terbaru</h4>
                    <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Entitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                            <tr>
                                <td class="text-sm">{{ $activity->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img src="{{ asset('assets/images/faces/1.jpg') }}">
                                        </div>
                                        <p class="font-bold mb-0 text-sm">{{ $activity->user?->name }}</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $activity->event === 'created' ? 'success' : ($activity->event === 'deleted' ? 'danger' : 'info') }}">
                                        {{ strtoupper($activity->event) }}
                                    </span>
                                </td>
                                <td class="text-sm">{{ class_basename($activity->auditable_type) }} #{{ $activity->auditable_id }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Status & Shortcuts --}}
    <div class="col-12 col-lg-4">
        {{-- Drug Status --}}
        <div class="card">
            <div class="card-header pb-0">
                <h4>Persediaan Obat</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="obatTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1 px-2" id="stok-tab" data-bs-toggle="tab" data-bs-target="#stok-panel" type="button" role="tab">Status Stok</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-2" id="expiry-tab" data-bs-toggle="tab" data-bs-target="#expiry-panel" type="button" role="tab">Kadaluarsa</button>
                    </li>
                </ul>
                <div class="tab-content" id="obatTabContent">
                    <div class="tab-pane fade show active" id="stok-panel" role="tabpanel">
                        <div class="row small mb-3">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-success me-2"></i>Stok Aman</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusObat['aktif'] }}</strong></div>
                        </div>
                        <div class="row small mb-3">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-info me-2"></i>Stok Menipis</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusObat['stok_menipis'] }}</strong></div>
                        </div>
                        <div class="row small">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-dark me-2"></i>Stok Habis</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusObat['stok_habis'] }}</strong></div>
                        </div>
                        <div id="chart-stok-obat" class="mt-3"></div>
                    </div>
                    <div class="tab-pane fade" id="expiry-panel" role="tabpanel">
                        <div class="row small mb-3">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-success me-2"></i>Kondisi Aman</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusKadaluarsa['aman'] }}</strong></div>
                        </div>
                        <div class="row small mb-3">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-warning me-2"></i>Hampir Kadaluarsa</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusKadaluarsa['hampir_kadaluarsa'] }}</strong></div>
                        </div>
                        <div class="row small">
                            <div class="col-8"><span><i class="bi bi-circle-fill text-danger me-2"></i>Sudah Kadaluarsa</span></div>
                            <div class="col-4 text-end"><strong>{{ $statusKadaluarsa['kadaluarsa'] }}</strong></div>
                        </div>
                        <div id="chart-kadaluarsa-obat" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shortcuts --}}
        <div class="card">
            <div class="card-header">
                <h4>Akses Cepat</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kunjungan.create') }}" class="btn btn-primary text-start">
                        <i class="bi bi-plus-circle me-2"></i> Kunjungan Baru
                    </a>
                    <a href="{{ route('obat.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-capsule me-2"></i> Tambah Stok Obat
                    </a>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Laporan Harian
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
<script>
    // Tren Kunjungan Chart
    var optionsKunjungan = {
        series: [{
            name: 'Total Kunjungan',
            data: {!! json_encode(collect($trenKunjungan)->pluck('total')) !!}
        }],
        chart: {
            height: 300,
            type: 'area',
            toolbar: { show: false }
        },
        colors: ['#435ebe'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
            categories: {!! json_encode(collect($trenKunjungan)->pluck('periode')) !!},
        },
        tooltip: { x: { format: 'dd/MM/yy' } },
    };
    var chartKunjungan = new ApexCharts(document.querySelector("#chart-kunjungan"), optionsKunjungan);
    chartKunjungan.render();

    // Stok Obat Donut Chart
    var optionsStok = {
        series: [{{ $statusObat['aktif'] }}, {{ $statusObat['stok_menipis'] }}, {{ $statusObat['stok_habis'] }}],
        labels: ['Aman', 'Menipis', 'Habis'],
        colors: ['#198754', '#0dcaf0', '#212529'],
        chart: {
            type: 'donut',
            width: '100%',
            height: 250
        },
        legend: { position: 'bottom' },
        plotOptions: {
            pie: {
                donut: { size: '65%' }
            }
        }
    };
    var chartStok = new ApexCharts(document.querySelector("#chart-stok-obat"), optionsStok);
    chartStok.render();

    // Kadaluarsa Obat Donut Chart
    var optionsExpiry = {
        series: [{{ $statusKadaluarsa['aman'] }}, {{ $statusKadaluarsa['hampir_kadaluarsa'] }}, {{ $statusKadaluarsa['kadaluarsa'] }}],
        labels: ['Aman', 'Hampir Kadaluarsa', 'Kadaluarsa'],
        colors: ['#198754', '#ffc107', '#dc3545'],
        chart: {
            type: 'donut',
            width: '100%',
            height: 250
        },
        legend: { position: 'bottom' },
        plotOptions: {
            pie: {
                donut: { size: '65%' }
            }
        }
    };
    var chartExpiry = new ApexCharts(document.querySelector("#chart-kadaluarsa-obat"), optionsExpiry);
    chartExpiry.render();
</script>
@endpush
