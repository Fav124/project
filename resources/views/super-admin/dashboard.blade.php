@extends('layouts.app')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Otoritas & Kendali Sistem')

@section('content')

{{-- Stats Grid --}}
<div class="stats-grid" style="margin-bottom: 32px;">
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #eff6ff; color: #2563eb;"><i class="fas fa-users"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['total_users'] }}</div>
            <div class="label">Total Pengguna</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px; border: 1px solid var(--warning);">
        <div class="stat-icon-box" style="background: #fffbeb; color: #d97706;"><i class="fas fa-user-clock"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['pending'] }}</div>
            <div class="label">Menunggu Persetujuan</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #ecfdf5; color: #059669;"><i class="fas fa-user-check"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['approved'] }}</div>
            <div class="label">Akun Aktif</div>
        </div>
    </div>
    <div class="stat-card" style="border-radius: 20px;">
        <div class="stat-icon-box" style="background: #fef2f2; color: #dc2626;"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-content">
            <div class="value">{{ $stats['rejected'] }}</div>
            <div class="label">Akun Ditolak</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1.2fr; gap:32px; margin-bottom: 32px;">
    {{-- Registration Trends --}}
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-chart-column"></i> Volume Pendaftaran Baru</h2>
            <div class="badge badge-primary">7 Hari Terakhir</div>
        </x-slot>
        <div id="regChart" style="min-height: 350px;"></div>
    </x-ui.card>

    {{-- User Composition --}}
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-circle-nodes"></i> Proporsi Jabatan</h2>
        </x-slot>
        <div id="roleChart" style="min-height: 350px;"></div>
    </x-ui.card>
</div>

<div style="display:grid; grid-template-columns: 1fr 360px; gap:32px; align-items:start;">

    {{-- Pending Users Table --}}
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-id-card-clip"></i> Antrian Approval Akun</h2>
            <a href="{{ route('super-admin.users', ['status' => 'pending']) }}" class="btn btn-xs btn-outline">Lihat Semua Antrian</a>
        </x-slot>

        @if($pendingUsers->isEmpty())
            <x-ui.empty-state message="Seluruh permohonan pendaftaran telah diproses." />
        @else
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Calon Pengguna</th>
                        <th>Status</th>
                        <th style="text-align:right;">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $user)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="user-avatar" style="width:36px; height:36px; font-size:14px; background:var(--bg-main); border:1px solid var(--border);">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700; color:var(--text-main);">{{ $user->name }}</div>
                                    <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-warning" style="font-size: 10px;">MENUNGGU</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:8px; justify-content: flex-end;">
                                <form method="POST" action="{{ route('super-admin.users.approve', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-primary" title="Setujui Langsung">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-xs btn-outline" title="Detail & Penolakan">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </x-ui.card>

    {{-- System Health / Sidebar Info --}}
    <div style="display:flex; flex-direction:column; gap:32px;">
        <x-ui.card style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border: none;">
            <div style="font-size: 14px; font-weight: 700; margin-bottom: 20px; color: var(--brand-accent);">
                <i class="fas fa-shield-halved"></i> Protokol Keamanan
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; gap: 12px; font-size: 13px; opacity: 0.9; line-height: 1.5;">
                    <i class="fas fa-fingerprint" style="margin-top: 4px; color: var(--brand-accent);"></i>
                    <span>Verifikasi manual wajib dilakukan untuk setiap entri petugas kesehatan baru.</span>
                </div>
                <div style="display: flex; gap: 12px; font-size: 13px; opacity: 0.9; line-height: 1.5;">
                    <i class="fas fa-key" style="margin-top: 4px; color: var(--brand-accent);"></i>
                    <span>Otoritas reset password dikunci hanya untuk level Super Administrator.</span>
                </div>
            </div>
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center;">
                <div style="font-size: 11px; opacity: 0.5; margin-bottom: 8px;">WHATSAPP SUPPORT GATEWAY</div>
                <div style="font-weight: 700; letter-spacing: 0.1em; color: var(--brand-accent);">+{{ config('app.admin_whatsapp', '6281234567890') }}</div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-microchip"></i> System Status</h2>
            </x-slot>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 13px; font-weight: 600;">PHP Version</span>
                <span class="badge badge-outline">{{ PHP_VERSION }}</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 13px; font-weight: 600;">Laravel Framework</span>
                <span class="badge badge-outline">{{ app()->version() }}</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 13px; font-weight: 600;">Database</span>
                <span class="badge badge-success"><i class="fas fa-check"></i> Connected</span>
            </div>
        </x-ui.card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--brand-start').trim() || '#1e293b';

    // Registration Trends Chart
    const regOptions = {
        series: [{
            name: 'Pendaftar Baru',
            data: @json($registrationTrends->pluck('count'))
        }],
        chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Outfit' },
        colors: [brandPrimary],
        plotOptions: { 
            bar: { 
                borderRadius: 8, 
                columnWidth: '35%',
                dataLabels: { position: 'top' }
            } 
        },
        xaxis: {
            categories: @json($registrationTrends->pluck('date')),
            labels: { style: { fontFamily: 'Outfit', fontWeight: 600, colors: '#94a3b8' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { show: false },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        dataLabels: { 
            enabled: true,
            offsetY: -20,
            style: { fontSize: '12px', colors: ["#64748b"], fontFamily: 'Outfit' }
        }
    };
    new ApexCharts(document.querySelector("#regChart"), regOptions).render();

    // User Composition Chart
    const roleOptions = {
        series: [{{ $stats['petugas'] }}, {{ $stats['admin'] }}],
        chart: { type: 'donut', height: 350, fontFamily: 'Outfit' },
        labels: ['Petugas UKS', 'Administrator'],
        colors: ['#38bdf8', brandPrimary],
        stroke: { show: false },
        legend: { position: 'bottom', fontWeight: 600, fontSize: '14px' },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Pengguna Aktif',
                            fontSize: '14px',
                            fontWeight: 700,
                            color: '#64748b'
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#roleChart"), roleOptions).render();
</script>
@endpush
@endsection
