@extends('layouts.app')

@section('title', 'Rekam Kesehatan')
@section('page-title', 'Riwayat Rekam Medis Santri')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Rekam Medis">
            <x-slot name="header">
                <h4 class="card-title">Daftar Rekam Medis</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Rekam Medis
                </button>
            </x-slot>

            <form action="{{ route('health-records.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" class="form-control text-white" placeholder="Cari santri..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" name="date_from" class="form-control text-white" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" name="date_to" class="form-control text-white" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Santri</th>
                        <th>Tinggi (cm)</th>
                        <th>Berat (kg)</th>
                        <th>Gol. Darah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->record_date->translatedFormat('d F Y') }}</td>
                            <td class="text-white">{{ $record->santri->name }}</td>
                            <td>{{ $record->height ?: '-' }}</td>
                            <td>{{ $record->weight ?: '-' }}</td>
                            <td><span class="badge badge-outline-info">{{ $record->blood_type ?: '-' }}</span></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('health-records.index', array_merge(request()->query(), ['detail' => $record->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('health-records.index', array_merge(request()->query(), ['edit' => $record->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('health-records.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rekam medis ini?')">
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
                {{ $records->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Rekam Medis Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('health-records.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="record-rows">
                        <div class="record-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Santri</label>
                                        <select name="records[0][santri_id]" class="form-select text-white select2" required>
                                            <option value="">Pilih Santri</option>
                                            @foreach($santris as $santri)
                                                <option value="{{ $santri->id }}">{{ $santri->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Tanggal</label>
                                        <input type="date" name="records[0][record_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Keluhan</label>
                                        <textarea name="records[0][complaint]" class="form-control" rows="2" required></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Diagnosis</label>
                                        <input type="text" name="records[0][diagnosis]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Tindakan/Obat</label>
                                        <textarea name="records[0][treatment]" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-small">Tekanan Darah</label>
                                                <input type="text" name="records[0][blood_pressure]" class="form-control" placeholder="120/80">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-small">Suhu (°C)</label>
                                                <input type="number" name="records[0][temperature]" class="form-control" step="0.1">
                                            </div>
                                        </div>
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
                        <i class="mdi mdi-plus"></i> Tambah Baris Rekam Medis
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
@if($editRecord)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Rekam Medis: {{ $editRecord->santri->name }}</h5>
                <a href="{{ route('health-records.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('health-records.update', $editRecord) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Santri</label>
                            <select name="santri_id" class="form-select text-white" required>
                                @foreach($santris as $santri)
                                    <option value="{{ $santri->id }}" {{ $editRecord->santri_id == $santri->id ? 'selected' : '' }}>{{ $santri->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="record_date" class="form-control" value="{{ $editRecord->record_date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tinggi (cm)</label>
                            <input type="number" name="height" class="form-control" value="{{ $editRecord->height }}" step="0.1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" name="weight" class="form-control" value="{{ $editRecord->weight }}" step="0.1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gol. Darah</label>
                            <select name="blood_type" class="form-select text-white">
                                <option value="A" {{ $editRecord->blood_type == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $editRecord->blood_type == 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ $editRecord->blood_type == 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ $editRecord->blood_type == 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Riwayat Alergi</label>
                        <textarea name="allergies" class="form-control" rows="2">{{ $editRecord->allergies }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Riwayat Penyakit</label>
                        <textarea name="medical_history" class="form-control" rows="2">{{ $editRecord->medical_history }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $editRecord->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('health-records.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailRecord)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Detail Rekam Medis: {{ $detailRecord->santri->name }}</h5>
                <a href="{{ route('health-records.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Data Antropometri</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tinggi Badan</span>
                            <span class="text-white">{{ $detailRecord->height ?: '-' }} cm</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Berat Badan</span>
                            <span class="text-white">{{ $detailRecord->weight ?: '-' }} kg</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Golongan Darah</span>
                            <span class="badge badge-info">{{ $detailRecord->blood_type ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tanggal Periksa</span>
                            <span class="text-white">{{ $detailRecord->record_date->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Informasi Lain</h6>
                        <div class="mb-2">
                            <span class="text-muted small">Dicatat Oleh</span>
                            <p class="text-white">{{ $detailRecord->recorder->name }}</p>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-danger border-bottom border-secondary pb-2"><i class="mdi mdi-alert-circle mr-2"></i> Riwayat Alergi</h6>
                        <p class="text-white p-3 bg-dark rounded">{{ $detailRecord->allergies ?: 'Tidak ada riwayat alergi yang tercatat.' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-warning border-bottom border-secondary pb-2"><i class="mdi mdi-history mr-2"></i> Riwayat Penyakit</h6>
                        <p class="text-white p-3 bg-dark rounded">{{ $detailRecord->medical_history ?: 'Tidak ada riwayat penyakit serius.' }}</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-success border-bottom border-secondary pb-2"><i class="mdi mdi-note-text mr-2"></i> Catatan Tambahan</h6>
                        <p class="text-white p-3 bg-dark rounded">{{ $detailRecord->notes ?: '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('health-records.index') }}" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($editRecord)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailRecord)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const recordRows = document.getElementById('record-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.record-row').cloneNode(true);
            
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/records\[\d+\]/, `records[${rowCount}]`));
                }
                if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    if (!input.name.includes('[record_date]')) input.value = '';
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            recordRows.appendChild(newRow);
            rowCount++;
        });

        recordRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.record-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
