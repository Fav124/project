@extends('layouts.app')
@section('title', 'Data Kasur UKS')
@section('page_title', 'Master Data Kasur')
@section('page_description', 'Kelola daftar kasur yang tersedia di UKS.')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kasur</li>
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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 mb-2">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d6efd,#0a58ca)">
            <div class="icon"><i class="bi bi-hospital"></i></div>
            <div class="info"><h3>{{ $kasurs->count() }}</h3><p>Total Kasur</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#146c43)">
            <div class="icon"><i class="bi bi-check-circle"></i></div>
            <div class="info"><h3>{{ $kasurs->where('status', 'tersedia')->count() }}</h3><p>Tersedia</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#b02a37)">
            <div class="icon"><i class="bi bi-person-bed"></i></div>
            <div class="info"><h3>{{ $kasurs->where('status', 'terisi')->count() }}</h3><p>Terisi / Digunakan</p></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent pt-4 pb-2 border-0">
                <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kasur Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kasur.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Kasur</label>
                        <input type="text" name="kode_kasur" class="form-control" placeholder="Misal: UKS-01, BED-A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Awal</label>
                        <select name="status" class="form-select" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="terisi">Terisi</option>
                            <option value="rusak">Rusak / Perbaikan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Kasur</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-primary-header">
                            <tr>
                                <th>Kode Kasur</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Riwayat Pakai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kasurs as $k)
                            <tr>
                                <td class="fw-bold ps-3">{{ $k->kode_kasur }}</td>
                                <td class="text-center">
                                    @if($k->status == 'tersedia')
                                        <span class="badge bg-success">Tersedia</span>
                                    @elseif($k->status == 'terisi')
                                        <span class="badge bg-danger">Terisi</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Rusak</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $k->riwayats_count }} Kali</span></td>
                                <td class="text-center pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Edit" 
                                            onclick="editKasur('{{ $k->id }}', '{{ addslashes($k->kode_kasur) }}', '{{ $k->status }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('kasur.destroy', $k->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus kasur ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data kasur terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Kasur Modal --}}
<div class="modal fade" id="editKasurModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKasurForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kasur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Kasur</label>
                        <input type="text" name="kode_kasur" id="edit_kode_kasur" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" id="edit_status_kasur" class="form-select" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="terisi">Terisi</option>
                            <option value="rusak">Rusak / Perbaikan</option>
                        </select>
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
    function editKasur(id, kode, status) {
        document.getElementById('edit_kode_kasur').value = kode;
        document.getElementById('edit_status_kasur').value = status;
        document.getElementById('editKasurForm').action = `/master/kasur/${id}`;
        new bootstrap.Modal(document.getElementById('editKasurModal')).show();
    }
</script>
@endpush
