@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page-title', 'Manajemen Data Kelas')

@section('content')
@push('plugin-styles')
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
@endpush

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Kepadatan Santri per Kelas">
            <div id="classChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-6 grid-margin stretch-card">
        <x-ui.card title="Peminatan Jurusan">
            <div id="majorChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Kelas">
            <x-slot name="header">
                <h4 class="card-title">Daftar Kelas</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Kelas
                </button>
            </x-slot>

            <form action="{{ route('classes.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control text-white" placeholder="Cari nama kelas atau jurusan..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Jumlah Santri</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td>{{ $class->name }}</td>
                            <td>
                                @foreach($class->majors as $major)
                                    <span class="badge badge-outline-info btn-xs">{{ $major->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="badge badge-outline-primary">{{ $class->santris_count }} Santri</div>
                            </td>
                            <td>{{ Str::limit($class->description, 50) ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('classes.index', array_merge(request()->query(), ['detail' => $class->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('classes.index', array_merge(request()->query(), ['edit' => $class->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @can('manage-master-data')
                                        <form action="{{ route('classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data kelas ini?')">
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
                            <td colspan="4" class="text-center text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $classes->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('classes.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="class-rows">
                        <div class="class-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Nama Kelas</label>
                                        <input type="text" name="classes[0][name]" class="form-control" placeholder="Contoh: X IPA 1" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="classes[0][description]" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Pilih Jurusan</label>
                                        <select name="classes[0][major_ids][]" class="form-control text-white select2-multiple" multiple style="width: 100%;">
                                            @foreach($majors as $major)
                                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Kelas
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
@if($editClass)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelas: {{ $editClass->name }}</h5>
                <a href="{{ route('classes.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('classes.update', $editClass) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" value="{{ $editClass->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Jurusan</label>
                        <select name="major_ids[]" class="form-control text-white select2-multiple" multiple style="width: 100%;">
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ $editClass->majors->contains($major->id) ? 'selected' : '' }}>{{ $major->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ $editClass->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('classes.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailClass)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kelas: {{ $detailClass->name }}</h5>
                <a href="{{ route('classes.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nama Kelas</small>
                    <span class="text-white h5">{{ $detailClass->name }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Jurusan Terkait</small>
                    @foreach($detailClass->majors as $major)
                        <span class="badge badge-info">{{ $major->name }}</span>
                    @endforeach
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Deskripsi</small>
                    <p class="text-white">{{ $detailClass->description ?: 'Tidak ada deskripsi' }}</p>
                </div>
                <hr class="border-secondary">
                <div class="mb-3">
                    <h6 class="text-white">Daftar Santri ({{ $detailClass->santris->count() }})</h6>
                    <div class="table-responsive mt-3" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm text-white">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIS</th>
                                    <th>Kamar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailClass->santris as $santri)
                                    <tr>
                                        <td>{{ $santri->name }}</td>
                                        <td>{{ $santri->nis ?: '-' }}</td>
                                        <td>{{ $santri->dorm_room ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada santri di kelas ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
@endif

@push('plugin-scripts')
    <script src="{{ asset('template-assets/vendors/select2/select2.min.js') }}"></script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { theme: 'dark', background: 'transparent', toolbar: { show: false } },
        grid: { show: false },
        legend: { position: 'bottom', labels: { colors: '#6c7293' } }
    };

    // Class Distribution Chart (Bar)
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($classStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 250 },
        xaxis: { categories: @json($classStats->pluck('name')), labels: { show: false } },
        colors: ['#ffab00']
    }).render();

    // Major Distribution Chart (Donut)
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: @json($majorStats->pluck('santris_count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 250 },
        labels: @json($majorStats->pluck('name')),
        colors: ['#0090e7', '#8f5fe8', '#00d25b', '#fc424a', '#ffab00'],
        dataLabels: { enabled: false }
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        function initSelect2() {
            $('.select2-multiple').select2({
                placeholder: "Pilih Jurusan",
                allowClear: true,
                theme: "bootstrap"
            });
        }

        initSelect2();

        @if($editClass)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailClass)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const classRows = document.getElementById('class-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.class-row').cloneNode(true);
            
            // Update input names
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/classes\[\d+\]/, `classes[${rowCount}]`));
                }
                if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    input.value = '';
                }
            });

            // Remove existing select2 container if cloned
            const select2Container = newRow.querySelector('.select2-container');
            if (select2Container) {
                select2Container.remove();
            }
            
            // Show remove button
            newRow.querySelector('.remove-row').style.display = 'block';
            
            classRows.appendChild(newRow);
            rowCount++;

            // Re-init select2 for new row
            initSelect2();
        });

        classRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.class-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
