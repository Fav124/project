@extends('layouts.app')

@section('title', 'Data Santri')
@section('page-title', 'Manajemen Data Santri')

@section('content')
<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Jenis Kelamin">
            <div id="genderChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Sebaran Kelas">
            <div id="classChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Sebaran Jurusan">
            <div id="majorChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Santri">
            <x-slot name="header">
                <h4 class="card-title">Daftar Santri</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Santri
                </button>
            </x-slot>

            <div class="filter-bar mb-4 p-4 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md">
                <form action="{{ route('santri.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label text-muted text-small uppercase tracking-wider font-bold">Cari Santri</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white/5 border-white/10 text-muted"><i class="mdi mdi-magnify"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted text-small uppercase tracking-wider font-bold">Filter Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-full" type="submit">Terapkan Filter</button>
                            @if(request('search') || request('class_id'))
                                <a href="{{ route('santri.index') }}" class="btn btn-outline-secondary px-3" title="Reset"><i class="mdi mdi-refresh"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santris as $santri)
                        <tr>
                            <td>{{ $santri->name }}</td>
                            <td>{{ $santri->nis ?: '-' }}</td>
                            <td>{{ optional($santri->schoolClass)->name ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('santri.show', $santri) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('santri.edit', $santri) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @can('manage-master-data')
                                        <form action="{{ route('santri.destroy', $santri) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data santri ini?')">
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
                {{ $santris->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Santri Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('santri.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="santri-rows">
                        <div class="santri-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Nama Lengkap</label>
                                        <input type="text" name="santris[0][name]" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">NIS</label>
                                        <input type="text" name="santris[0][nis]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">L/P</label>
                                        <select name="santris[0][gender]" class="form-select text-white" required>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Kelas</label>
                                        <select name="santris[0][school_class_id]" class="form-select text-white">
                                            <option value="">Pilih Kelas</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Jurusan</label>
                                        <select name="santris[0][major_id]" class="form-select text-white">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach($majors as $major)
                                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Nama Wali</label>
                                        <input type="text" name="santris[0][guardian_name]" class="form-control">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Telp Wali</label>
                                        <input type="text" name="santris[0][guardian_phone]" class="form-control" placeholder="628xxx">
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
                    <button type="button" class="btn btn-outline-info btn-block btn-sm" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Santri
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



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { 
            theme: 'light',
            background: 'transparent', 
            toolbar: { show: true },
            fontFamily: 'Inter, sans-serif'
        },
        grid: { borderColor: '#191c24' },
        legend: { position: 'top', labels: { colors: '#6c7293' } }
    };

    // Gender Chart
    new ApexCharts(document.querySelector("#genderChart"), {
        ...chartDefaults,
        series: @json($genderStats->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 280 },
        labels: @json($genderStats->map(fn($s) => $s->gender == 'L' ? 'Laki-laki' : 'Perempuan')),
        colors: ['#0090e7', '#fc424a'],
        dataLabels: { enabled: true }
    }).render();

    // Class Chart
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Santri', data: @json($classStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 280 },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        xaxis: { 
            categories: @json($classStats->pluck('name')),
            labels: { style: { colors: '#6c7293' }, rotate: -45, hideOverlappingLabels: true }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } },
        colors: ['#ffab00']
    }).render();

    // Major Chart (Horizontal Bar for better labels)
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: [{ name: 'Jumlah Santri', data: @json($majorStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 280 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: { 
            categories: @json($majorStats->pluck('name')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' }, maxWidth: 120 } },
        colors: ['#8f5fe8']
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const santriRows = document.getElementById('santri-rows');

        addRowBtn.addEventListener('click', function() {
            const firstRow = document.querySelector('.santri-row');
            const newRow = firstRow.cloneNode(true);
            
            // Clean up Tom Select from clone if it exists
            newRow.querySelectorAll('.ts-wrapper').forEach(el => el.remove());
            newRow.querySelectorAll('select').forEach(el => {
                el.style.display = 'block';
                el.classList.remove('tomselected', 'ts-hidden-visually');
                if (el.tomselect) delete el.tomselect;
            });

            newRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/santris\[\d+\]/, `santris[${rowCount}]`));
                }
                if (input.tagName === 'INPUT') {
                    input.value = '';
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            santriRows.appendChild(newRow);
            rowCount++;

            // Re-initialize Tom Select for new row
            if (window.initTomSelect) window.initTomSelect();
        });

        santriRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.santri-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
