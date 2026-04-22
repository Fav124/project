@extends('layouts.app')

@section('title', 'Data Jurusan')
@section('page-title', 'Manajemen Data Jurusan')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Jurusan">
            <x-slot name="header">
                <h4 class="card-title">Daftar Jurusan</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Jurusan
                </button>
            </x-slot>

            <form action="{{ route('majors.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control text-white" placeholder="Cari nama jurusan..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('majors.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama Jurusan</th>
                        <th>Jumlah Santri</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($majors as $major)
                        <tr>
                            <td class="text-white font-weight-bold">{{ $major->name }}</td>
                            <td>
                                <div class="badge badge-outline-primary">{{ $major->santris_count }} Santri</div>
                            </td>
                            <td>{{ Str::limit($major->description, 70) ?: '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('majors.index', array_merge(request()->query(), ['detail' => $major->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('majors.index', array_merge(request()->query(), ['edit' => $major->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @can('manage-master-data')
                                        <form action="{{ route('majors.destroy', $major) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data jurusan ini?')">
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
                            <td colspan="3" class="text-center text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $majors->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jurusan Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('majors.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="major-rows">
                        <div class="major-row border-bottom border-secondary mb-3 pb-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Nama Jurusan</label>
                                        <input type="text" name="majors[0][name]" class="form-control" placeholder="Contoh: Teknik Komputer" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Deskripsi</label>
                                        <input type="text" name="majors[0][description]" class="form-control" placeholder="Penjelasan singkat...">
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm mt-2" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Jurusan
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
@if($editMajor)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jurusan: {{ $editMajor->name }}</h5>
                <a href="{{ route('majors.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('majors.update', $editMajor) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="name" class="form-control" value="{{ $editMajor->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="4">{{ $editMajor->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('majors.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailMajor)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Jurusan: {{ $detailMajor->name }}</h5>
                <a href="{{ route('majors.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nama Jurusan</small>
                    <span class="text-white h5">{{ $detailMajor->name }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Deskripsi</small>
                    <p class="text-white">{{ $detailMajor->description ?: 'Tidak ada deskripsi' }}</p>
                </div>
                <hr class="border-secondary">
                <div class="mb-3">
                    <h6 class="text-white">Daftar Santri ({{ $detailMajor->santris->count() }})</h6>
                    <div class="table-responsive mt-3" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm text-white">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailMajor->santris as $santri)
                                    <tr>
                                        <td>{{ $santri->name }}</td>
                                        <td>{{ $santri->nis ?: '-' }}</td>
                                        <td>{{ optional($santri->schoolClass)->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada santri di jurusan ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('majors.index') }}" class="btn btn-secondary">Tutup</a>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($editMajor)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailMajor)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const majorRows = document.getElementById('major-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.major-row').cloneNode(true);
            
            newRow.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/majors\[\d+\]/, `majors[${rowCount}]`));
                }
                input.value = '';
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            majorRows.appendChild(newRow);
            rowCount++;
        });

        majorRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.major-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
