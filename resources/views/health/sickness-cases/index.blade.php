@extends('layouts.app')

@section('title', 'Santri Sakit')
@section('page-title', 'Manajemen Kasus Sakit Santri')

@section('content')
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Tren Kunjungan Pasien (30 Hari Terakhir)">
            <div id="visitChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <x-ui.card title="Status Pasien">
            <div id="statusChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <x-ui.card title="Top 5 Diagnosa">
            <div id="diagnosisChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Data Santri Sakit">
            <x-slot name="header">
                <h4 class="card-title">Log Kasus Sakit</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Input Kasus Baru
                </button>
            </x-slot>

            <div class="filter-bar mb-4 p-4 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md">
                <form action="{{ route('sickness-cases.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label text-muted text-small uppercase tracking-wider font-bold">Cari Kasus</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white/5 border-white/10 text-muted"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Nama santri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted text-small uppercase tracking-wider font-bold">Status Pasien</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="observed" {{ request('status') == 'observed' ? 'selected' : '' }}>Observasi</option>
                            <option value="handled" {{ request('status') == 'handled' ? 'selected' : '' }}>Ditangani</option>
                            <option value="recovered" {{ request('status') == 'recovered' ? 'selected' : '' }}>Sembuh</option>
                            <option value="referred" {{ request('status') == 'referred' ? 'selected' : '' }}>Dirujuk</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-full" type="submit">Filter Data</button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline-secondary px-3" title="Reset"><i class="mdi mdi-refresh"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Tgl Visit</th>
                        <th>Santri</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                        <tr>
                            <td>{{ $case->visit_date->translatedFormat('d F Y') }}</td>
                            <td>{{ $case->santri->name }}</td>
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
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @if($case->status !== 'recovered')
                                        <form action="{{ route('sickness-cases.recovered', $case) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Tandai Sembuh">
                                                <i class="mdi mdi-check-all"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('sickness-cases.index', array_merge(request()->query(), ['detail' => $case->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('sickness-cases.index', array_merge(request()->query(), ['edit' => $case->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('sickness-cases.destroy', $case) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data kasus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
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
                {{ $cases->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Kasus Sakit Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('sickness-cases.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="case-rows">
                        <div class="case-row border border-secondary rounded p-3 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label text-small">Santri</label>
                                        <select name="cases[0][santri_id]" class="form-select text-white select2" required>
                                            <option value="">Pilih Santri</option>
                                            @foreach($santris as $santri)
                                                <option value="{{ $santri->id }}">{{ $santri->name }} ({{ optional($santri->schoolClass)->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-small">Tanggal Visit</label>
                                                <input type="date" name="cases[0][visit_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-small">Kasur UKS</label>
                                                <select name="cases[0][infirmary_bed_id]" class="form-select text-white">
                                                    <option value="">Tanpa Kasur</option>
                                                    @foreach($beds as $bed)
                                                        <option value="{{ $bed->id }}" {{ $bed->status == 'occupied' ? 'disabled' : '' }}>{{ $bed->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Status</label>
                                        <select name="cases[0][status]" class="form-select text-white" required>
                                            <option value="observed">Observasi</option>
                                            <option value="handled">Ditangani</option>
                                            <option value="referred">Dirujuk</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label text-small">Keluhan</label>
                                        <textarea name="cases[0][complaint]" class="form-control" rows="3" required placeholder="Apa yang dirasakan santri?"></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Diagnosa Awal / Catatan</label>
                                        <textarea name="cases[0][diagnosis]" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4 border-left border-secondary">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label text-small text-info font-weight-bold">Daftar Obat</label>
                                        <button type="button" class="btn btn-xs btn-outline-info add-medicine" data-case-index="0">
                                            <i class="mdi mdi-plus"></i> Obat
                                        </button>
                                    </div>
                                    <div class="medicine-list" id="medicine-list-0">
                                        <div class="medicine-row row mb-2 g-2">
                                            <div class="col-7">
                                                <select name="cases[0][medicines][0][id]" class="form-select form-select-sm text-white">
                                                    <option value="">Pilih Obat</option>
                                                    @foreach($medicines as $medicine)
                                                        <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <input type="number" name="cases[0][medicines][0][quantity]" class="form-control form-control-sm" value="1" min="1">
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-link btn-sm text-danger remove-medicine" style="display:none;"><i class="mdi mdi-close"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-inverse-danger btn-sm remove-row" style="display:none;">
                                    <i class="mdi mdi-delete"></i> Hapus Baris Santri
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Kasus Santri
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Semua Kasus</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
@if($editCase)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Kasus: {{ $editCase->santri->name }}</h5>
                <a href="{{ route('sickness-cases.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('sickness-cases.update', $editCase) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Santri</label>
                            <select name="santri_id" class="form-select text-white select2" required>
                                @foreach($santris as $santri)
                                    <option value="{{ $santri->id }}" {{ $editCase->santri_id == $santri->id ? 'selected' : '' }}>{{ $santri->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="visit_date" class="form-control" value="{{ $editCase->visit_date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea name="complaint" class="form-control" rows="3" required>{{ $editCase->complaint }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select text-white" required>
                                <option value="observed" {{ $editCase->status == 'observed' ? 'selected' : '' }}>Observasi</option>
                                <option value="handled" {{ $editCase->status == 'handled' ? 'selected' : '' }}>Ditangani</option>
                                <option value="recovered" {{ $editCase->status == 'recovered' ? 'selected' : '' }}>Sembuh</option>
                                <option value="referred" {{ $editCase->status == 'referred' ? 'selected' : '' }}>Dirujuk</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kasur UKS</label>
                            <select name="infirmary_bed_id" class="form-select text-white">
                                <option value="">Tanpa Kasur</option>
                                @foreach($beds as $bed)
                                    <option value="{{ $bed->id }}" {{ $editCase->infirmary_bed_id == $bed->id ? 'selected' : '' }}>{{ $bed->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="border rounded p-3 mb-3 bg-dark">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Daftar Obat</h6>
                            <button type="button" class="btn btn-xs btn-outline-info add-edit-medicine">
                                <i class="mdi mdi-plus"></i> Tambah Obat
                            </button>
                        </div>
                        <div id="edit-medicine-list">
                            @foreach($editCase->medicines as $index => $med)
                                <div class="edit-medicine-row row mb-2 g-2">
                                    <div class="col-8">
                                        <select name="medicines[{{ $index }}][id]" class="form-select form-select-sm text-white">
                                            @foreach($medicines as $m)
                                                <option value="{{ $m->id }}" {{ $med->id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" name="medicines[{{ $index }}][quantity]" class="form-control form-control-sm" value="{{ $med->pivot->quantity }}" min="1">
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-link btn-sm text-danger remove-edit-medicine"><i class="mdi mdi-close"></i></button>
                                    </div>
                                </div>
                            @endforeach
                            @if($editCase->medicines->isEmpty())
                                <div class="edit-medicine-row row mb-2 g-2">
                                    <div class="col-8">
                                        <select name="medicines[0][id]" class="form-select form-select-sm text-white">
                                            <option value="">Pilih Obat</option>
                                            @foreach($medicines as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" name="medicines[0][quantity]" class="form-control form-control-sm" value="1" min="1">
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-link btn-sm text-danger remove-edit-medicine" style="display:none;"><i class="mdi mdi-close"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $editCase->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('sickness-cases.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailCase)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Detail Kasus Medis: {{ $detailCase->santri->name }}</h5>
                <a href="{{ route('sickness-cases.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ substr($detailCase->santri->name, 0, 1) }}
                        </div>
                        <h4 class="text-white">{{ $detailCase->santri->name }}</h4>
                        <span class="badge {{ $statusMap['class'] }}">{{ $statusMap['label'] }}</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Tanggal Kunjungan</small>
                                <span class="text-white">{{ $detailCase->visit_date->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Petugas Penanggungjawab</small>
                                <span class="text-white">{{ $detailCase->handler->name }}</span>
                            </div>
                            <div class="col-12 mb-3">
                                <small class="text-muted d-block">Keluhan</small>
                                <p class="text-white border-left border-primary pl-3 py-2 bg-dark">{{ $detailCase->complaint }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="border-secondary">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h6 class="text-primary mb-3"><i class="mdi mdi-pill mr-2"></i> Pengobatan & Status Obat</h6>
                        <div class="table-responsive">
                            <table class="table table-sm text-white">
                                <thead>
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th>Jumlah</th>
                                        <th>Status Pemakaian</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailCase->medicines as $med)
                                        <tr>
                                            <td>{{ $med->name }}</td>
                                            <td>{{ $med->pivot->quantity }} {{ $med->unit }}</td>
                                            <td>
                                                <span class="badge {{ $med->pivot->status == 'taken' ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $med->pivot->status == 'taken' ? 'Sudah Diminum' : 'Belum Diminum' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                    class="btn btn-xs btn-{{ $med->pivot->status == 'taken' ? 'outline-warning' : 'success' }} update-med-status"
                                                    data-pivot-id="{{ $med->pivot->id }}"
                                                    data-status="{{ $med->pivot->status == 'taken' ? 'pending' : 'taken' }}">
                                                    {{ $med->pivot->status == 'taken' ? 'Batalkan' : 'Tandai Diminum' }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada obat yang diberikan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('sickness-cases.index') }}" class="btn btn-secondary">Tutup</a>
                <a href="{{ route('sickness-cases.notify', $detailCase) }}" class="btn btn-success">
                    <i class="mdi mdi-whatsapp"></i> Kirim Ulang WA Wali
                </a>
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

    // Visit Trend Chart (Area)
    new ApexCharts(document.querySelector("#visitChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Kunjungan', data: @json($sicknessTrends->pluck('count')) }],
        chart: { ...chartDefaults.chart, type: 'area', height: 280 },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { 
            categories: @json($sicknessTrends->pluck('date')), 
            labels: { style: { colors: '#6c7293' }, rotate: -45, hideOverlappingLabels: true } 
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } },
        colors: ['#00d25b'],
        fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0 } }
    }).render();

    // Status Chart (Donut)
    @php
        $mappedCaseStatuses = $statusStats->pluck('status')->map(fn($s) => match($s) {
            'observed' => 'Observasi',
            'handled' => 'Ditangani',
            'recovered' => 'Sembuh',
            'referred' => 'Dirujuk',
            default => ucfirst($s)
        });
    @endphp
    new ApexCharts(document.querySelector("#statusChart"), {
        ...chartDefaults,
        series: @json($statusStats->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 280 },
        labels: @json($mappedCaseStatuses),

        colors: ['#0090e7', '#ffab00', '#00d25b', '#fc424a'],
        dataLabels: { enabled: false }
    }).render();

    // Diagnosis Chart (Horizontal Bar)
    new ApexCharts(document.querySelector("#diagnosisChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Kasus', data: @json($diagnosisStats->pluck('count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 280 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: { categories: @json($diagnosisStats->pluck('diagnosis')), labels: { style: { colors: '#6c7293' } } },
        yaxis: { labels: { style: { colors: '#6c7293' }, maxWidth: 120 } },
        colors: ['#8f5fe8']
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        @if($editCase)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailCase)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Global Medicine Update Handler
        $(document).on('click', '.update-med-status', function() {
            const btn = $(this);
            const pivotId = btn.data('pivot-id');
            const status = btn.data('status');
            
            $.ajax({
                url: `/santri-sakit/medicine/${pivotId}/update-status`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });

        // Dynamic Rows Logic (Sickness Cases)
        let caseCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const caseRows = document.getElementById('case-rows');

        addRowBtn.addEventListener('click', function() {
            const firstRow = document.querySelector('.case-row');
            const newRow = firstRow.cloneNode(true);
            
            // Clean up Tom Select from clone if it exists
            newRow.querySelectorAll('.ts-wrapper').forEach(el => el.remove());
            newRow.querySelectorAll('select').forEach(el => {
                el.style.display = 'block';
                el.classList.remove('tomselected', 'ts-hidden-visually');
                if (el.tomselect) delete el.tomselect;
            });

            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    // Update index [0] to [caseCount]
                    input.setAttribute('name', name.replace(/cases\[\d+\]/, `cases[${caseCount}]`));
                }
                if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    if (!input.name.includes('[visit_date]') && !input.name.includes('[quantity]')) {
                        input.value = '';
                    }
                }
            });

            // Update medicine list ID and button data
            const medList = newRow.querySelector('.medicine-list');
            medList.id = `medicine-list-${caseCount}`;
            const addMedBtn = newRow.querySelector('.add-medicine');
            addMedBtn.setAttribute('data-case-index', caseCount);
            
            // Reset medicine rows to only one
            const medRows = medList.querySelectorAll('.medicine-row');
            for(let i = 1; i < medRows.length; i++) medRows[i].remove();
            medRows[0].querySelector('.remove-medicine').style.display = 'none';

            newRow.querySelector('.remove-row').style.display = 'inline-block';
            caseRows.appendChild(newRow);
            
            if (window.initTomSelect) window.initTomSelect();
            caseCount++;
        });

        caseRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.case-row').remove();
            }
        });

        // Nested Medicine Repeater Logic
        $(document).on('click', '.add-medicine', function() {
            const caseIdx = $(this).data('case-index');
            const list = $(`#medicine-list-${caseIdx}`);
            const firstMedRow = list.find('.medicine-row').first();
            const newMedRow = firstMedRow.cloneNode(true);
            const medIdx = list.find('.medicine-row').length;

            newMedRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    // Update medicine index [medicines][0] to [medicines][medIdx]
                    input.setAttribute('name', name.replace(/\[medicines\]\[\d+\]/, `[medicines][${medIdx}]`));
                }
                if (input.tagName === 'INPUT') input.value = '1';
                else input.selectedIndex = 0;
            });

            newMedRow.querySelector('.remove-medicine').style.display = 'inline-block';
            list.append(newMedRow);
        });

        $(document).on('click', '.remove-medicine', function() {
            $(this).closest('.medicine-row').remove();
        });

        // Edit Modal Medicine Repeater
        $(document).on('click', '.add-edit-medicine', function() {
            const list = $('#edit-medicine-list');
            const firstRow = list.find('.edit-medicine-row').first();
            const newRow = firstRow.cloneNode(true);
            const medIdx = list.find('.edit-medicine-row').length;

            newRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/medicines\[\d+\]/, `medicines[${medIdx}]`));
                }
                if (input.tagName === 'INPUT') input.value = '1';
                else input.selectedIndex = 0;
            });

            newRow.querySelector('.remove-edit-medicine').style.display = 'inline-block';
            list.append(newRow);
        });

        $(document).on('click', '.remove-edit-medicine', function() {
            $(this).closest('.edit-medicine-row').remove();
        });
    });
</script>
@endpush
@endsection
