@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page_title', 'Master Data Kelas')
@section('page_description', 'Kelola daftar kelas untuk pengelompokan santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kelas</li>
@endsection

@push('styles')
<style>
.stat-card { border-radius:12px; padding:20px; color:#fff !important; display:flex; align-items:center; gap:16px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.stat-card .icon { font-size:2.5rem; opacity:.9; color:#fff !important; }
.stat-card .info h3 { font-size:2rem; font-weight:800; margin:0; color:#fff !important; }
.stat-card .info p { margin:0; opacity:.9; font-size:.85rem; color:#fff !important; }
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

<div class="row">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
            <div class="icon"><i class="bi bi-building"></i></div>
            <div class="info"><h3>{{ $kelas->count() }}</h3><p>Total Kelas</p></div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kelas Baru</h5>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Misal: 10A, 11B" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warna Label</label>
                        <input type="color" name="warna" class="form-control form-control-color w-100" value="#0d6efd" title="Pilih warna untuk kelas ini">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Kelas</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-primary-header">
                            <tr>
                                <th>Nama Kelas</th>
                                <th class="text-center">Jumlah Santri</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $k)
                            <tr>
                                <td class="fw-bold">
                                    <span class="badge text-white" style="background-color: {{ $k->warna ?? '#6c757d' }};">
                                        {{ $k->nama_kelas }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $k->santris_count }} Santri</span></td>
                                <td class="text-center">
                                    <a href="{{ route('kelas.show', $k->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Edit" 
                                            onclick="editKelas('{{ $k->id }}', '{{ addslashes($k->nama_kelas) }}', '{{ $k->warna }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data kelas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Kelas Modal --}}
<div class="modal fade" id="editKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKelasForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warna Label</label>
                        <input type="color" name="warna" id="edit_warna_kelas" class="form-control form-control-color w-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editKelas(id, nama, warna) {
        document.getElementById('edit_nama_kelas').value = nama;
        document.getElementById('edit_warna_kelas').value = warna || '#6c757d';
        document.getElementById('editKelasForm').action = `/master/kelas/${id}`;
        new bootstrap.Modal(document.getElementById('editKelasModal')).show();
    }
</script>
@endpush
