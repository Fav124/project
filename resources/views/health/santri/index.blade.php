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

            <form action="{{ route('santri.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control text-white" placeholder="Cari nama, NIS, kelas..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('santri.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Asrama</th>
                        <th>Kamar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santris as $santri)
                        <tr>
                            <td>{{ $santri->name }}</td>
                            <td>{{ $santri->nis ?: '-' }}</td>
                            <td>{{ optional($santri->schoolClass)->name ?: '-' }}</td>
                            <td>{{ optional($santri->dormitory)->name ?: '-' }}</td>
                            <td>{{ $santri->dorm_room ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('santri.index', array_merge(request()->query(), ['detail' => $santri->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('santri.index', array_merge(request()->query(), ['edit' => $santri->id])) }}" class="btn btn-outline-warning btn-sm">
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
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Asrama</label>
                                        <select name="santris[0][dormitory_id]" class="form-select text-white">
                                            <option value="">Pilih Asrama</option>
                                            @foreach($dormitories as $dorm)
                                                <option value="{{ $dorm->id }}">{{ $dorm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Kamar</label>
                                        <input type="text" name="santris[0][dorm_room]" class="form-control" placeholder="No. 101">
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

{{-- Edit Modal --}}
@if($editSantri)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Santri: {{ $editSantri->name }}</h5>
                <a href="{{ route('santri.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('santri.update', $editSantri) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $editSantri->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control" value="{{ $editSantri->nis }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select text-white" required>
                                <option value="L" {{ $editSantri->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $editSantri->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="school_class_id" class="form-select text-white">
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $editSantri->school_class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan</label>
                            <select name="major_id" class="form-select text-white">
                                <option value="">Pilih Jurusan</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ $editSantri->major_id == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asrama</label>
                            <select name="dormitory_id" class="form-select text-white">
                                <option value="">Pilih Asrama</option>
                                @foreach($dormitories as $dorm)
                                    <option value="{{ $dorm->id }}" {{ $editSantri->dormitory_id == $dorm->id ? 'selected' : '' }}>{{ $dorm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kamar Asrama (No.)</label>
                            <input type="text" name="dorm_room" class="form-control" value="{{ $editSantri->dorm_room }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Wali</label>
                        <input type="text" name="guardian_name" class="form-control" value="{{ $editSantri->guardian_name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon Wali (WA)</label>
                        <input type="text" name="guardian_phone" class="form-control" value="{{ $editSantri->guardian_phone }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('santri.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailSantri)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Santri: {{ $detailSantri->name }}</h5>
                <a href="{{ route('santri.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; font-weight: 800;">
                        {{ substr($detailSantri->name, 0, 1) }}
                    </div>
                </div>
                <h4 class="text-white">{{ $detailSantri->name }}</h4>
                <p class="text-muted">NIS: {{ $detailSantri->nis ?: '-' }}</p>
                <hr class="border-secondary">
                <div class="row text-left">
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Kelas</small>
                        <span class="text-white">{{ optional($detailSantri->schoolClass)->name ?: '-' }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Jurusan</small>
                        <span class="text-white">{{ optional($detailSantri->major)->name ?: '-' }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Asrama</small>
                        <span class="text-white">{{ optional($detailSantri->dormitory)->name ?: '-' }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Kamar</small>
                        <span class="text-white">{{ $detailSantri->dorm_room ?: '-' }}</span>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted d-block">Jenis Kelamin</small>
                        <span class="text-white">{{ $detailSantri->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">Nama Wali</small>
                        <span class="text-white">{{ $detailSantri->guardian_name ?: '-' }}</span>
                    </div>
                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">Telepon Wali</small>
                        <span class="text-success font-weight-bold">{{ $detailSantri->guardian_phone ?: '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('santri.index') }}" class="btn btn-secondary">Tutup</a>
                @if($detailSantri->guardian_phone)
                    <a href="https://wa.me/{{ $detailSantri->guardian_phone }}" target="_blank" class="btn btn-success">Hubungi Wali</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDefaults = {
        chart: { theme: 'dark', background: 'transparent', toolbar: { show: false } },
        grid: { show: false },
        legend: { position: 'bottom', labels: { colors: '#6c7293' } }
    };

    // Gender Chart
    new ApexCharts(document.querySelector("#genderChart"), {
        ...chartDefaults,
        series: @json($genderStats->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 250 },
        labels: @json($genderStats->map(fn($s) => $s->gender == 'L' ? 'Laki-laki' : 'Perempuan')),
        colors: ['#0090e7', '#fc424a'],
        dataLabels: { enabled: false }
    }).render();

    // Class Chart
    new ApexCharts(document.querySelector("#classChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($classStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 250 },
        xaxis: { 
            categories: @json($classStats->pluck('name')),
            labels: { show: false }
        },
        colors: ['#ffab00']
    }).render();

    // Major Chart
    new ApexCharts(document.querySelector("#majorChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($majorStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 250 },
        xaxis: { 
            categories: @json($majorStats->pluck('name')),
            labels: { show: false }
        },
        colors: ['#8f5fe8']
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        @if($editSantri)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailSantri)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

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
