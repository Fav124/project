@extends('layouts.app')

@section('title', 'Data Jurusan')
@section('page_title', 'Master Data Jurusan')
@section('page_description', 'Kelola daftar jurusan / konsentrasi keahlian santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Jurusan</li>
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
        <div class="stat-card" style="background:linear-gradient(135deg,#e83e8c,#c43376)">
            <div class="icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div class="info"><h3>{{ $jurusans->count() }}</h3><p>Total Jurusan</p></div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Jurusan Baru</h5>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('jurusan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control" placeholder="Misal: IPA, IPS, RPL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warna Label</label>
                        <input type="color" name="warna" class="form-control form-control-color w-100" value="#e83e8c" title="Pilih warna untuk jurusan ini">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Jurusan</button>
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
                                <th>Nama Jurusan</th>
                                <th class="text-center">Jumlah Santri</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurusans as $j)
                            <tr>
                                <td class="fw-bold">
                                    <span class="badge text-white" style="background-color: {{ $j->warna ?? '#6c757d' }};">
                                        {{ $j->nama_jurusan }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $j->santris_count }} Santri</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Edit" 
                                            onclick="editJurusan('{{ $j->id }}', '{{ addslashes($j->nama_jurusan) }}', '{{ $j->warna }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('jurusan.destroy', $j->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus jurusan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data jurusan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Jurusan Modal --}}
<div class="modal fade" id="editJurusanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editJurusanForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" id="edit_nama_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Warna Label</label>
                        <input type="color" name="warna" id="edit_warna_jurusan" class="form-control form-control-color w-100">
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
    function editJurusan(id, nama, warna) {
        document.getElementById('edit_nama_jurusan').value = nama;
        document.getElementById('edit_warna_jurusan').value = warna || '#6c757d';
        document.getElementById('editJurusanForm').action = `/master/jurusan/${id}`;
        new bootstrap.Modal(document.getElementById('editJurusanModal')).show();
    }
</script>
@endpush
