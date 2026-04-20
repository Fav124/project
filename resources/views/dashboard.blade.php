@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pusat Analitik Kesehatan')

@section('content')
{{-- Hero Banner --}}
<div class="glass-card" style="background: var(--brand-start); background-image: linear-gradient(135deg, var(--brand-start), var(--brand-end)); color: white; border: none; margin-bottom: 32px; padding: 40px; position: relative; overflow: hidden; border-radius: 24px;">
    <div style="position: absolute; right: -20px; top: -20px; font-size: 180px; opacity: 0.15; transform: rotate(-15deg);">
        <i class="fas fa-heart-pulse"></i>
    </div>
    <div style="position: relative; z-index: 2;">
        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; opacity: 0.8; margin-bottom: 12px;">Selamat Datang Kembali</div>
        <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.02em;">Halo, {{ explode(' ', $user->name)[0] }}!</h1>
        <p style="font-size: 16px; opacity: 0.9; max-width: 600px; line-height: 1.6;">Sistem DeisaHealth siap membantu Anda memantau kondisi kesehatan santri dan mengelola operasional UKS hari ini.</p>
        
        <div style="margin-top: 32px; display: flex; gap: 16px;">
            <a href="{{ route('sickness-cases.index', ['create' => 1]) }}" class="btn" style="background: white; color: var(--brand-start); font-weight: 800; padding: 12px 24px; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2);">
                <i class="fas fa-plus-circle"></i> Input Kasus Baru
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white; backdrop-filter: blur(10px);">
                <i class="fas fa-chart-pie"></i> Analisis Data
            </a>
        </div>
    </div>
</div>

{{-- Main Stats --}}
<div class="stats-grid" style="margin-bottom: 32px;">
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #e0f2fe; color: #0369a1;"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['santri'] }}</div>
            <div class="label">Total Santri</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #fff7ed; color: #c2410c;"><i class="fas fa-procedures"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['santri_sakit_aktif'] }}</div>
            <div class="label">Pasien UKS</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #ecfdf5; color: #047857;"><i class="fas fa-bed"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['kasur_tersedia'] }}</div>
            <div class="label">Kasur Kosong</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #fef2f2; color: #b91c1c;"><i class="fas fa-capsules"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['obat_menipis'] }}</div>
            <div class="label">Stok Kritis</div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 32px;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-chart-line"></i> Tren Kunjungan Pasien</h2>
            <div class="badge badge-outline">7 Hari Terakhir</div>
        </x-slot>
        <div id="visitChart" style="min-height: 350px;"></div>
    </x-ui.card>
    
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-chart-pie"></i> Status Pemulihan</h2>
        </x-slot>
        <div id="statusChart" style="min-height: 350px;"></div>
    </x-ui.card>
</div>

{{-- Bottom Details --}}
<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 32px;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-clock-rotate-left"></i> Aktifitas UKS Terkini</h2>
            <a href="{{ route('sickness-cases.index') }}" class="btn btn-xs btn-outline">Lihat Log Lengkap</a>
        </x-slot>
        <x-ui.table>
            <thead>
                <tr>
                    <th>Santri & Keluhan</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCases as $case)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--brand-start);">{{ $case->santri->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($case->complaint, 35) }}</div>
                        </td>
                        <td>
                            @php
                                $statusMap = match($case->status) {
                                    'observed' => ['class' => 'badge-warning', 'label' => 'Observasi'],
                                    'handled' => ['class' => 'badge-info', 'label' => 'Ditangani'],
                                    'recovered' => ['class' => 'badge-success', 'label' => 'Sembuh'],
                                    'referred' => ['class' => 'badge-danger', 'label' => 'Dirujuk'],
                                    default => ['class' => 'badge-outline', 'label' => $case->status]
                                };
                            @endphp
                            <span class="badge {{ $statusMap['class'] }}">{{ $statusMap['label'] }}</span>
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted);">{{ $case->visit_date->diffForHumans() }}</td>
                    </tr>
                @empty
                    <x-ui.empty-state :colspan="3" message="Belum ada aktifitas medis hari ini." />
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.card>

    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-triangle-exclamation" style="color: var(--danger);"></i> Inventori Kritis</h2>
        </x-slot>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($lowStockMedicines as $medicine)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg-main); border-radius: 16px; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; font-size: 14px;">{{ $medicine->name }}</div>
                            <div style="font-size: 12px; color: var(--danger); font-weight: 600;">Sisa {{ $medicine->stock }} {{ $medicine->unit }}</div>
                        </div>
                    </div>
                    <a href="{{ route('medicines.index', ['search' => $medicine->name]) }}" class="btn btn-xs btn-primary">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            @empty
                <div style="padding: 40px; text-align: center; background: var(--bg-main); border-radius: 16px;">
                    <i class="fas fa-circle-check" style="font-size: 40px; color: var(--success); margin-bottom: 16px; display: block; opacity: 0.5;"></i>
                    <div style="font-weight: 700; color: var(--text-main);">Stok Aman</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Seluruh obat tersedia di atas batas minimum.</div>
                </div>
            @endforelse
        </div>
    </x-ui.card>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--brand-start').trim() || '#4f46e5';

    // Visit Trends Chart
    const visitOptions = {
        series: [{
            name: 'Kunjungan',
            data: @json($sicknessTrends->pluck('count'))
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Outfit'
        },
        colors: [brandPrimary],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 4 },
        xaxis: {
            categories: @json($sicknessTrends->pluck('date')),
            labels: { style: { colors: '#94a3b8', fontWeight: 500 } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8', fontWeight: 500 } }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
    };

    new ApexCharts(document.querySelector("#visitChart"), visitOptions).render();

    // Status Distribution Chart
    const statusOptions = {
        series: @json($caseDistribution->pluck('count')),
        chart: { type: 'donut', height: 350, fontFamily: 'Outfit' },
        labels: @json($caseDistribution->pluck('status')),
        colors: ['#0ea5e9', '#f59e0b', '#10b981', '#f43f5e'],
        legend: { position: 'bottom', fontWeight: 600 },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Kasus',
                            fontSize: '14px',
                            fontWeight: 700,
                            color: '#64748b'
                        }
                    }
                }
            }
        }
    };

    new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
</script>
@endpush
@endsection
