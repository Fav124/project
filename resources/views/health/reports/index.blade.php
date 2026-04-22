@extends('layouts.app')

@section('title', 'Laporan Tahunan & Bulanan')
@section('page-title', 'Pusat Laporan & Analitik')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control text-white" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control text-white" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <div class="template-demo d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-icon-text">
                                <i class="mdi mdi-refresh btn-icon-prepend"></i> Update Data
                            </button>
                            <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-icon-text">
                                <i class="mdi mdi-file-pdf btn-icon-prepend"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $summary['total_santri'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-primary ">
                            <span class="mdi mdi-account-group icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Total Santri</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $summary['rekam_kesehatan'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-info">
                            <span class="mdi mdi-clipboard-text icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Rekam Medis</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $summary['santri_sakit'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-warning">
                            <span class="mdi mdi-emoticon-sick icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Kasus Sakit</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $summary['rujukan_rs'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-danger">
                            <span class="mdi mdi-hospital-building icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Rujukan RS</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Diagnosis Terbanyak">
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Penyakit / Keluhan</th>
                        <th class="text-end">Jumlah Kasus</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topComplaints as $complaint)
                        <tr>
                            <td class="text-white font-weight-bold">{{ $complaint->diagnosis }}</td>
                            <td class="text-end">
                                <div class="badge badge-outline-info">{{ $complaint->total }} Kasus</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Stok Obat Kritis">
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th class="text-end">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockMedicines as $medicine)
                        <tr>
                            <td class="text-white font-weight-bold">{{ $medicine->name }}</td>
                            <td class="text-end">
                                <div class="badge badge-outline-danger">{{ $medicine->stock }} {{ $medicine->unit }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Semua stok aman</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>
</div>
@endsection
