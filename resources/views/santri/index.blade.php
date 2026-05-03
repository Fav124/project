@extends('layouts.app')

@section('title', 'Data Santri')
@section('page_title', 'Manajemen Data Santri')
@section('page_description', 'Kelola data pokok santri, wali, dan informasi kesehatan dasar.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Santri</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Daftar Santri</h4>
            <a href="{{ route('santri.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Tambah Santri
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('santri.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIS..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="80">Foto</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>JK</th>
                        <th>Kelas / Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santris as $santri)
                    <tr>
                        <td>
                            <img src="{{ $santri->foto ? asset('storage/' . $santri->foto) : asset('assets/images/faces/1.jpg') }}" 
                                 class="formal-photo" style="width: 40px; height: 60px; min-width: 40px;" alt="Foto">
                        </td>
                        <td><code>{{ $santri->nis }}</code></td>
                        <td class="fw-bold">{{ $santri->nama_lengkap }}</td>
                        <td>{{ $santri->jenis_kelamin }}</td>
                        <td>
                            <span class="badge bg-light-primary text-primary">{{ $santri->kelas?->nama_kelas ?? '-' }}</span>
                            <br>
                            <small class="text-muted">{{ $santri->jurusan?->nama_jurusan ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $santri->status_santri === 'aktif' ? 'success' : 'secondary' }}">
                                {{ strtoupper($santri->status_santri) }}
                            </span>
                            <br>
                            @if($santri->is_sakit)
                                <span class="badge bg-light-danger text-danger mt-1">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Sedang Sakit
                                </span>
                            @else
                                <span class="badge bg-light-success text-success mt-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Sehat
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('santri.show', $santri->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('santri.edit', $santri->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="deleteSantri({{ $santri->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $santris->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteSantri(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data santri akan dipindahkan ke tempat sampah!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Form delete logic here
                alert('Fungsi hapus dipicu untuk ID: ' + id);
            }
        })
    }
</script>
@endpush
