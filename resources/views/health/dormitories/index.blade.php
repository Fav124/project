@extends('layouts.app')

@section('title', 'Data Asrama')
@section('page-title', 'Manajemen Data Asrama')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <x-ui.card title="Kepadatan Santri per Asrama">
            <div id="dormChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Kategori Asrama">
            <div id="genderChart" style="min-height: 250px;"></div>
        </x-ui.card>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Asrama">
            <x-slot name="header">
                <h4 class="card-title">Daftar Gedung & Kamar Asrama</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Asrama
                </button>
            </x-slot>

            <form action="{{ route('dormitories.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control text-white" placeholder="Cari nama asrama atau gedung..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama Asrama</th>
                        <th>Gedung</th>
                        <th>Kategori</th>
                        <th>Pembimbing</th>
                        <th>Total Santri</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dormitories as $dorm)
                        <tr>
                            <td class="text-white font-weight-bold">{{ $dorm->name }}</td>
                            <td>{{ $dorm->building ?: '-' }}</td>
                            <td>
                                <span class="badge {{ $dorm->gender == 'L' ? 'badge-outline-info' : 'badge-outline-danger' }}">
                                    {{ $dorm->gender == 'L' ? 'Putra' : 'Putri' }}
                                </span>
                            </td>
                            <td>{{ $dorm->supervisor_name ?: '-' }}</td>
                            <td>{{ $dorm->santris_count }} Santri</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dormitories.index', ['edit' => $dorm->id]) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dormitories.destroy', $dorm) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus asrama ini?')">
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
                            <td colspan="6" class="text-center text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $dormitories->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Asrama Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('dormitories.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="row-repeater">
                        <div class="repeater-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-small">Nama Asrama/Kamar</label>
                                        <input type="text" name="dormitories[0][name]" class="form-control" placeholder="Abu Bakar 01" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="text-small">Gedung</label>
                                        <input type="text" name="dormitories[0][building]" class="form-control" placeholder="Gedung A">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="text-small">Kategori</label>
                                        <select name="dormitories[0][gender]" class="form-select text-white" required>
                                            <option value="L">Putra</option>
                                            <option value="P">Putri</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="text-small">Pembimbing/Musyrif</label>
                                        <input type="text" name="dormitories[0][supervisor_name]" class="form-control" placeholder="Ust. Ahmad">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="text-small">Keterangan</label>
                                        <textarea name="dormitories[0][description]" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris
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
@if($editDormitory)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Asrama: {{ $editDormitory->name }}</h5>
                <a href="{{ route('dormitories.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('dormitories.update', $editDormitory) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Asrama</label>
                        <input type="text" name="name" class="form-control" value="{{ $editDormitory->name }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Gedung</label>
                            <input type="text" name="building" class="form-control" value="{{ $editDormitory->building }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kategori</label>
                            <select name="gender" class="form-select text-white" required>
                                <option value="L" @selected($editDormitory->gender == 'L')>Putra</option>
                                <option value="P" @selected($editDormitory->gender == 'P')>Putri</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Pembimbing</label>
                        <input type="text" name="supervisor_name" class="form-control" value="{{ $editDormitory->supervisor_name }}">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control" rows="3">{{ $editDormitory->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dormitories.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
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

    // Dormitory Distribution Chart (Bar)
    new ApexCharts(document.querySelector("#dormChart"), {
        ...chartDefaults,
        series: [{ name: 'Santri', data: @json($dormitoryStats->pluck('santris_count')) }],
        chart: { ...chartDefaults.chart, type: 'bar', height: 250 },
        xaxis: { categories: @json($dormitoryStats->pluck('name')), labels: { show: false } },
        colors: ['#0090e7']
    }).render();

    // Gender Distribution Chart (Donut)
    new ApexCharts(document.querySelector("#genderChart"), {
        ...chartDefaults,
        series: @json($genderStats->pluck('count')),
        chart: { ...chartDefaults.chart, type: 'donut', height: 250 },
        labels: @json($genderStats->map(fn($s) => $s->gender == 'L' ? 'Putra' : 'Putri')),
        colors: ['#0090e7', '#fc424a'],
        dataLabels: { enabled: false }
    }).render();

    document.addEventListener('DOMContentLoaded', function() {
        @if($editDormitory)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif

        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const repeaterContainer = document.getElementById('row-repeater');

        addRowBtn.addEventListener('click', function() {
            const firstRow = document.querySelector('.repeater-row');
            const newRow = firstRow.cloneNode(true);
            
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/dormitories\[\d+\]/, `dormitories[${rowCount}]`));
                }
                if (input.tagName !== 'SELECT') {
                    input.value = '';
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            repeaterContainer.appendChild(newRow);
            rowCount++;
        });

        repeaterContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.repeater-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
