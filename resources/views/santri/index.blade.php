@extends('layouts.app')

@section('title', 'Data Santri')
@section('page_title', 'Manajemen Data Santri')
@section('page_description', 'Kelola data pokok santri, wali, dan informasi kesehatan dasar.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Santri</li>
@endsection

@push('styles')
<style>
.stat-card { border-radius:12px; padding:20px; color:#fff !important; display:flex; align-items:center; gap:16px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.stat-card .icon { font-size:2.5rem; opacity:.9; color:#fff !important; }
.stat-card .info h3 { font-size:2rem; font-weight:800; margin:0; color:#fff !important; }
.stat-card .info p { margin:0; opacity:.9; font-size:.85rem; color:#fff !important; }
.table-actions .btn { padding:3px 8px; font-size:.78rem; }
.badge-active   { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600; }
.badge-inactive { background:#fee2e2; color:#991b1b; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600; }
.formal-photo { border-radius: 6px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.table-primary-header th { background-color: var(--bs-primary) !important; color: #fff !important; border-bottom: none; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-2">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0aa2c0)">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="info"><h3>{{ $totalAktif }}</h3><p>Total Santri Aktif</p></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
            <div class="icon"><i class="bi bi-gender-male"></i></div>
            <div class="info"><h3>{{ $totalPutra }}</h3><p>Total Laki-laki</p></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#e83e8c,#c43376)">
            <div class="icon"><i class="bi bi-gender-female"></i></div>
            <div class="info"><h3>{{ $totalPutri }}</h3><p>Total Perempuan</p></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#b02a37)">
            <div class="icon"><i class="bi bi-bandaid-fill"></i></div>
            <div class="info"><h3>{{ $totalSakit }}</h3><p>Sedang Sakit</p></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center pb-2">
        <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Daftar Santri</h5>
        <a href="{{ route('santri.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Santri
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-5">
                <form action="{{ route('santri.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIS..." value="{{ request('search') }}">
                        <button class="btn btn-secondary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-primary-header">
                    <tr>
                        <th width="60" class="text-center">Foto</th>
                        <th width="120">NIS</th>
                        <th>Nama Lengkap</th>
                        <th width="100" class="text-center">L/P</th>
                        <th>Kelas / Jurusan</th>
                        <th width="150" class="text-center">Status</th>
                        <th width="130" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santris as $santri)
                    <tr>
                        <td class="text-center">
                            <img src="{{ $santri->foto ? asset('storage/' . $santri->foto) : asset('assets/images/faces/1.jpg') }}" 
                                 class="formal-photo" style="width: 36px; height: 48px;" alt="Foto">
                        </td>
                        <td><span class="badge bg-secondary">{{ $santri->nis }}</span></td>
                        <td class="fw-semibold">{{ $santri->nama_lengkap }}</td>
                        <td class="text-center">{{ $santri->jenis_kelamin }}</td>
                        <td>
                            @if($santri->kelas)
                                <span class="badge text-white shadow-sm" style="background-color: {{ $santri->kelas->warna ?? '#6c757d' }}">{{ $santri->kelas->nama_kelas }}</span>
                            @else
                                <span class="badge bg-light text-dark border">-</span>
                            @endif
                            <br>
                            @if($santri->jurusan)
                                <small class="text-muted fw-semibold" style="color: {{ $santri->jurusan->warna ?? '#6c757d' }} !important;">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>{{ $santri->jurusan->nama_jurusan }}
                                </small>
                            @else
                                <small class="text-muted"><i class="bi bi-bookmark me-1"></i>-</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="{{ $santri->status_santri === 'aktif' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($santri->status_santri) }}
                            </span>
                            @if($santri->is_sakit)
                                <div class="mt-1"><span class="badge bg-danger" style="font-size:0.65rem;"><i class="bi bi-heart-pulse-fill me-1"></i>Sakit</span></div>
                            @endif
                        </td>
                        <td class="text-center table-actions">
                            <a href="{{ route('santri.show', $santri->id) }}" class="btn btn-outline-info btn-sm" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('santri.edit', $santri->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('santri.destroy', $santri->id) }}" method="POST" class="d-inline form-delete-santri">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada data santri ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $santris->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.form-delete-santri');
            Swal.fire({
                title: 'Hapus Data Santri?',
                text: "Data santri ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
