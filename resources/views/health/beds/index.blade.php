@extends('layouts.app')

@section('title', 'Kasur UKS')
@section('page-title', 'Manajemen Kapasitas Rawat Inap UKS')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Status Kasur">
            <x-slot name="header">
                <h4 class="card-title">Monitor Kapasitas Kasur</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Kasur
                </button>
            </x-slot>

            <form action="{{ route('beds.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="search" class="form-control text-white" placeholder="Cari kode kasur atau penghuni..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="status" class="form-select text-white">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </div>
            </form>

            <div class="row">
                @foreach($beds as $bed)
                    <div class="col-md-3 mb-4">
                        <div class="card {{ $bed->status == 'occupied' ? 'bg-danger-subtle border-danger' : ($bed->status == 'available' ? 'bg-success-subtle border-success' : 'bg-secondary-subtle') }}" style="border-width: 2px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="mb-0 text-white">{{ $bed->code }}</h4>
                                        <small class="text-muted">{{ $bed->room_name }}</small>
                                    </div>
                                    @php
                                        $iconMap = match($bed->status) {
                                            'available' => 'mdi-check-circle text-success',
                                            'occupied' => 'mdi-account-clock text-danger',
                                            default => 'mdi-wrench text-secondary'
                                        };
                                    @endphp
                                    <i class="mdi {{ $iconMap }}" style="font-size: 24px;"></i>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Penghuni Saat Ini:</small>
                                    <span class="text-white">{{ $bed->occupant_name ?: 'Kosong' }}</span>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('beds.index', array_merge(request()->query(), ['edit' => $bed->id])) }}" class="btn btn-dark btn-xs"><i class="mdi mdi-pencil"></i></a>
                                    <form action="{{ route('beds.destroy', $bed) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kasur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dark btn-xs text-danger"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-slot name="footer">
                {{ $beds->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kasur Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('beds.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="bed-rows">
                        <div class="bed-row border-bottom border-secondary mb-3 pb-3">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Kode Kasur</label>
                                        <input type="text" name="beds[0][code]" class="form-control" placeholder="K-01" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Nama Ruangan</label>
                                        <input type="text" name="beds[0][room_name]" class="form-control" placeholder="Ruang Utama" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Status</label>
                                        <select name="beds[0][status]" class="form-select text-white" required>
                                            <option value="available">Tersedia</option>
                                            <option value="maintenance">Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm mt-2" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Kasur
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
@if($editBed)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kasur: {{ $editBed->code }}</h5>
                <a href="{{ route('beds.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('beds.update', $editBed) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kasur</label>
                        <input type="text" name="code" class="form-control" value="{{ $editBed->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" name="room_name" class="form-control" value="{{ $editBed->room_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select text-white" required>
                            <option value="available" {{ $editBed->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="occupied" {{ $editBed->status == 'occupied' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ $editBed->status == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                    </div>
                    <div class="mb-3" id="occupant-field" style="display: {{ $editBed->status == 'occupied' ? 'block' : 'none' }}">
                        <label class="form-label">Nama Penghuni</label>
                        <input type="text" name="occupant_name" class="form-control" value="{{ $editBed->occupant_name }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('beds.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($editBed)
            new bootstrap.Modal(document.getElementById('editModal')).show();
            
            const statusSelect = document.querySelector('select[name="status"]');
            const occupantField = document.getElementById('occupant-field');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'occupied') {
                    occupantField.style.display = 'block';
                } else {
                    occupantField.style.display = 'none';
                }
            });
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const bedRows = document.getElementById('bed-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.bed-row').cloneNode(true);
            
            newRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/beds\[\d+\]/, `beds[${rowCount}]`));
                }
                if (input.tagName === 'INPUT') {
                    input.value = '';
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            bedRows.appendChild(newRow);
            rowCount++;
        });

        bedRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.bed-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
