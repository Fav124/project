@extends('layouts.app')

@section('title', 'Stok Obat')
@section('page-title', 'Manajemen Inventori Obat')

@section('content')
<div class="row">
    <div class="col-md-5 grid-margin stretch-card">
        <x-ui.card title="Peringkat Stok Terbanyak">
            <div id="stockChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <x-ui.card title="Status Kadaluarsa">
            <div id="expiryChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Top 5 Penggunaan Obat">
            <div id="usageChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Obat">
            <x-slot name="header">
                <h4 class="card-title">Daftar Inventori Obat</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Obat
                </button>
            </x-slot>

            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ route('medicines.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control text-white" placeholder="Cari nama obat..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('medicines.index', ['low_stock' => 1]) }}" class="btn btn-outline-danger btn-sm {{ request('low_stock') ? 'active' : '' }}">
                        <i class="mdi mdi-alert"></i> Stok Menipis
                    </a>
                    <a href="{{ route('medicines.index', ['expired' => 1]) }}" class="btn btn-outline-danger btn-sm {{ request('expired') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-remove"></i> Kadaluarsa
                    </a>
                    <a href="{{ route('medicines.index', ['expiring_soon' => 1]) }}" class="btn btn-outline-warning btn-sm {{ request('expiring_soon') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-clock"></i> Segera Kadaluarsa
                    </a>
                </div>
            </div>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Stok</th>
                        <th>Tgl Kadaluarsa</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $medicine)
                        <tr>
                            <td class="text-white font-weight-bold">{{ $medicine->name }}</td>
                            <td>
                                <span class="{{ $medicine->stock <= $medicine->minimum_stock ? 'text-danger font-weight-bold' : '' }}">
                                    {{ $medicine->stock }}
                                </span>
                            </td>
                            <td>
                                {{ $medicine->expiry_date ? $medicine->expiry_date->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td>
                                @if($medicine->isExpired())
                                    <div class="badge badge-outline-danger">Kadaluarsa</div>
                                @elseif($medicine->isExpiringSoon())
                                    <div class="badge badge-outline-warning">Segera Exp</div>
                                @elseif($medicine->stock <= $medicine->minimum_stock)
                                    <div class="badge badge-outline-danger">Stok Kritis</div>
                                @else
                                    <div class="badge badge-outline-success">Aman</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('medicines.index', array_merge(request()->query(), ['detail' => $medicine->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('medicines.index', array_merge(request()->query(), ['edit' => $medicine->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @can('manage-medical-data')
                                        <form action="{{ route('medicines.destroy', $medicine) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data obat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $medicines->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Obat Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('medicines.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="medicine-rows">
                        <div class="medicine-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Nama Obat</label>
                                        <input type="text" name="medicines[0][name]" class="form-control" placeholder="Paracetamol" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Satuan</label>
                                        <input type="text" name="medicines[0][unit]" class="form-control" placeholder="Tablet/Botol" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Stok Awal</label>
                                        <input type="number" name="medicines[0][stock]" class="form-control" value="0" min="0" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Tgl Kadaluarsa</label>
                                        <input type="date" name="medicines[0][expiry_date]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Stok Min.</label>
                                        <input type="number" name="medicines[0][minimum_stock]" class="form-control" value="10" min="0" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Kegunaan</label>
                                        <textarea name="medicines[0][description]" class="form-control" rows="3" placeholder="Sakit kepala..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row mb-1" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm mt-2" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Obat
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
@if($editMedicine)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Obat: {{ $editMedicine->name }}</h5>
                <a href="{{ route('medicines.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('medicines.update', $editMedicine) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Obat</label>
                        <input type="text" name="name" class="form-control" value="{{ $editMedicine->name }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" value="{{ $editMedicine->stock }}" min="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="unit" class="form-control" value="{{ $editMedicine->unit }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Stok Minimum</label>
                            <input type="number" name="minimum_stock" class="form-control" value="{{ $editMedicine->minimum_stock }}" min="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tgl Kadaluarsa</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ $editMedicine->expiry_date ? $editMedicine->expiry_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ $editMedicine->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailMedicine)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Obat: {{ $detailMedicine->name }}</h5>
                <a href="{{ route('medicines.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="bg-dark p-4 rounded d-inline-block">
                        <i class="mdi mdi-pill text-primary" style="font-size: 48px;"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Nama Obat</small>
                        <span class="text-white font-weight-bold">{{ $detailMedicine->name }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Stok Saat Ini</small>
                        <span class="text-white">{{ $detailMedicine->stock }} {{ $detailMedicine->unit }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Batas Minimum</small>
                        <span class="text-warning">{{ $detailMedicine->minimum_stock }} {{ $detailMedicine->unit }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Tgl Kadaluarsa</small>
                        @if($detailMedicine->isExpired())
                            <span class="text-danger font-weight-bold">{{ $detailMedicine->expiry_date->translatedFormat('d F Y') }} (Expired)</span>
                        @elseif($detailMedicine->isExpiringSoon())
                            <span class="text-warning font-weight-bold">{{ $detailMedicine->expiry_date->translatedFormat('d F Y') }} (Segera)</span>
                        @else
                            <span class="text-white">{{ $detailMedicine->expiry_date ? $detailMedicine->expiry_date->translatedFormat('d F Y') : '-' }}</span>
                        @endif
                    </div>
                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">Kegunaan/Deskripsi</small>
                        <p class="text-white">{{ $detailMedicine->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { 
            theme: 'dark', 
            background: 'transparent', 
            toolbar: { show: true },
            fontFamily: 'Inter, sans-serif'
        },
        grid: { borderColor: '#191c24' },
        legend: { position: 'top', labels: { colors: '#6c7293' } }
    };

    // Stock Level Chart (Horizontal Bar for better labels)
    new ApexCharts(document.querySelector("#stockChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Stok', data: @json($stockStats->pluck('stock')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 280 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: { categories: @json($stockStats->pluck('name')), labels: { style: { colors: '#6c7293' } } },
        yaxis: { labels: { style: { colors: '#6c7293' }, maxWidth: 120 } },
        colors: ['#0090e7']
    }).render();

    // Expiry Status Chart (Donut)
    new ApexCharts(document.querySelector("#expiryChart"), {
        ...chartDefaults,
        series: [@json($expiryStats['expired']), @json($expiryStats['expiring_soon']), @json($expiryStats['safe'])],
        chart: { ...chartDefaults.chart, type: 'donut', height: 280 },
        labels: ['Kadaluarsa', 'Segera Expired', 'Aman'],
        colors: ['#fc424a', '#ffab00', '#00d25b'],
        dataLabels: { enabled: true }
    }).render();

    // Usage Chart (Horizontal Bar)
    new ApexCharts(document.querySelector("#usageChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Digunakan', data: @json($usageStats->pluck('total')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 280 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: { categories: @json($usageStats->pluck('name')), labels: { style: { colors: '#6c7293' } } },
        yaxis: { labels: { style: { colors: '#6c7293' }, maxWidth: 120 } },
        colors: ['#8f5fe8']
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        @if($editMedicine)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailMedicine)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const medicineRows = document.getElementById('medicine-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.medicine-row').cloneNode(true);
            
            newRow.querySelectorAll('input, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/medicines\[\d+\]/, `medicines[${rowCount}]`));
                }
                if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    if (input.name.includes('[stock]')) input.value = '0';
                    else if (input.name.includes('[minimum_stock]')) input.value = '10';
                    else input.value = '';
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            medicineRows.appendChild(newRow);
            rowCount++;
        });

        medicineRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.medicine-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
