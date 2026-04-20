@extends('layouts.app')

@section('title', 'Laporan Tahunan & Bulanan')
@section('page-title', 'Pusat Laporan & Analitik')

@section('content')
{{-- Filter & Action Bar --}}
<div class="glass-card" style="padding: 24px; margin-bottom: 24px;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">Mulai Tanggal</label>
            <input type="date" name="start_date" class="form-input" value="{{ $startDate->format('Y-m-d') }}">
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-arrows-rotate"></i> Update Data
            </button>
            <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);">
                <i class="fas fa-file-pdf"></i> Cetak Laporan (PDF)
            </a>
        </div>
    </form>
</div>

{{-- Summary Stats --}}
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon-box" style="background: #e0f2fe; color: #0369a1;"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-content">
            <div class="value">{{ $summary['total_santri'] }}</div>
            <div class="label">Total Santri</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box" style="background: #fef9c3; color: #854d0e;"><i class="fas fa-file-medical"></i></div>
        <div class="stat-content">
            <div class="value">{{ $summary['rekam_kesehatan'] }}</div>
            <div class="label">Rekam Medis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box" style="background: #dcfce7; color: #15803d;"><i class="fas fa-stethoscope"></i></div>
        <div class="stat-content">
            <div class="value">{{ $summary['santri_sakit'] }}</div>
            <div class="label">Kasus Sakit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-box" style="background: #fee2e2; color: #b91c1c;"><i class="fas fa-truck-medical"></i></div>
        <div class="stat-content">
            <div class="value">{{ $summary['rujukan_rs'] }}</div>
            <div class="label">Rujukan RS</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    {{-- Top Diagnosis --}}
    <x-ui.card title="Penyakit / Diagnosis Terbanyak">
        <x-ui.table>
            <thead>
                <tr>
                    <th>Diagnosis</th>
                    <th style="text-align: right;">Jumlah Kasus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topComplaints as $complaint)
                    <tr>
                        <td style="font-weight: 700;">{{ $complaint->diagnosis }}</td>
                        <td style="text-align: right;">
                            <span class="badge badge-info">{{ $complaint->total }} Kasus</span>
                        </td>
                    </tr>
                @empty
                    <x-ui.empty-state :colspan="2" message="Data diagnosis tidak ditemukan." />
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.card>

    {{-- Stock Alert --}}
    <x-ui.card title="Stok Obat Kritis">
        <x-ui.table>
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th style="text-align: right;">Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockMedicines as $medicine)
                    <tr>
                        <td style="font-weight: 700;">{{ $medicine->name }}</td>
                        <td style="text-align: right;">
                            <span class="badge badge-danger">{{ $medicine->stock }} {{ $medicine->unit }}</span>
                        </td>
                    </tr>
                @empty
                    <x-ui.empty-state :colspan="2" message="Semua stok obat aman." />
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.card>
</div>
@endsection
