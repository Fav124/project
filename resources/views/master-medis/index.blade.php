@extends('layouts.app')
@section('title','Master Data Medis')
@section('page_title','Master Data Medis')

@section('breadcrumb')
    <li class="breadcrumb-item active">Master Medis</li>
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
.nav-tabs .nav-link { font-weight:600; }
.nav-tabs .nav-link.active { border-bottom:3px solid #0d6efd; }
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
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#b02a37)">
            <div class="icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <div class="info"><h3>{{ $diagnosas->total() }}</h3><p>Total Diagnosa (ICD)</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#fd7e14,#ca6510)">
            <div class="icon"><i class="bi bi-chat-square-text-fill"></i></div>
            <div class="info"><h3>{{ $keluhanList->total() }}</h3><p>Total Master Keluhan</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0aa2c0)">
            <div class="icon"><i class="bi bi-bandaid-fill"></i></div>
            <div class="info"><h3>{{ $tindakans->total() }}</h3><p>Total Master Tindakan</p></div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="card shadow-sm">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="masterTab">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-diagnosa"><i class="bi bi-clipboard2-pulse me-1 text-danger"></i>Diagnosa</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-keluhan"><i class="bi bi-chat-square-text me-1 text-warning"></i>Keluhan</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-tindakan"><i class="bi bi-bandaid me-1 text-info"></i>Tindakan Medis</a></li>
        </ul>
    </div>
    <div class="card-body tab-content">

        {{-- ═══ TAB DIAGNOSA ═══ --}}
        <div class="tab-pane fade show active" id="tab-diagnosa">
            {{-- Add Form --}}
            <form action="{{ route('master-medis.diagnosa.store') }}" method="POST" class="row g-2 align-items-end mb-4 p-3 bg-light rounded">
                @csrf
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Kode ICD</label>
                    <input type="text" name="kode" class="form-control form-control-sm" placeholder="A09">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Nama Diagnosa <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Nama diagnosa..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Kategori</label>
                    <input type="text" name="kategori" class="form-control form-control-sm" list="diagnosa-kat-list" placeholder="Pilih atau tulis...">
                    <datalist id="diagnosa-kat-list">
                        @foreach($diagnosaKategori as $k)<option value="{{ $k }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control form-control-sm" placeholder="Opsional">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>

            <table class="table table-hover table-sm">
                <thead class="table-primary-header">
                    <tr><th width="80">Kode</th><th>Nama Diagnosa</th><th>Kategori</th><th>Deskripsi</th><th width="80" class="text-center">Status</th><th width="120" class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($diagnosas as $d)
                <tr>
                    <td><span class="badge bg-secondary">{{ $d->kode ?: '-' }}</span></td>
                    <td class="fw-semibold">{{ $d->nama }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $d->kategori ?: '-' }}</span></td>
                    <td class="text-muted small">{{ Str::limit($d->deskripsi, 40) ?: '-' }}</td>
                    <td class="text-center">
                        <span class="{{ $d->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center table-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="openEditDiagnosa({{ $d->id }}, '{{ addslashes($d->nama) }}', '{{ $d->kode }}', '{{ addslashes($d->kategori) }}', '{{ addslashes($d->deskripsi) }}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('master-medis.diagnosa.toggle', $d->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $d->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $d->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi bi-{{ $d->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('master-medis.diagnosa.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus diagnosa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data diagnosa.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $diagnosas->links() }}
        </div>

        {{-- ═══ TAB KELUHAN ═══ --}}
        <div class="tab-pane fade" id="tab-keluhan">
            <form action="{{ route('master-medis.keluhan.store') }}" method="POST" class="row g-2 align-items-end mb-4 p-3 bg-light rounded">
                @csrf
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Nama Keluhan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Nama keluhan..." required>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Kategori</label>
                    <input type="text" name="kategori" class="form-control form-control-sm" list="keluhan-kat-list" placeholder="Pilih atau tulis...">
                    <datalist id="keluhan-kat-list">
                        @foreach($keluhanKategori as $k)<option value="{{ $k }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>

            <table class="table table-hover table-sm">
                <thead class="table-primary-header">
                    <tr><th>Nama Keluhan</th><th>Kategori</th><th width="80" class="text-center">Status</th><th width="120" class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($keluhanList as $k)
                <tr>
                    <td class="fw-semibold">{{ $k->nama }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $k->kategori ?: '-' }}</span></td>
                    <td class="text-center">
                        <span class="{{ $k->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="text-center table-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="openEditKeluhan({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->kategori) }}')"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('master-medis.keluhan.toggle', $k->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $k->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                <i class="bi bi-{{ $k->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('master-medis.keluhan.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus keluhan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data keluhan.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $keluhanList->links() }}
        </div>

        {{-- ═══ TAB TINDAKAN ═══ --}}
        <div class="tab-pane fade" id="tab-tindakan">
            <form action="{{ route('master-medis.tindakan.store') }}" method="POST" class="row g-2 align-items-end mb-4 p-3 bg-light rounded">
                @csrf
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Nama Tindakan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control form-control-sm" placeholder="Nama tindakan medis..." required>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Kategori</label>
                    <input type="text" name="kategori" class="form-control form-control-sm" list="tindakan-kat-list" placeholder="Pilih atau tulis...">
                    <datalist id="tindakan-kat-list">
                        @foreach($tindakanKategori as $k)<option value="{{ $k }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info btn-sm w-100 text-white"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>

            <table class="table table-hover table-sm">
                <thead class="table-primary-header">
                    <tr><th>Nama Tindakan</th><th>Kategori</th><th width="80" class="text-center">Status</th><th width="120" class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($tindakans as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->nama }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $t->kategori ?: '-' }}</span></td>
                    <td class="text-center">
                        <span class="{{ $t->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $t->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="text-center table-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="openEditTindakan({{ $t->id }}, '{{ addslashes($t->nama) }}', '{{ addslashes($t->kategori) }}')"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('master-medis.tindakan.toggle', $t->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $t->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                <i class="bi bi-{{ $t->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('master-medis.tindakan.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tindakan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data tindakan.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $tindakans->links() }}
        </div>

    </div>{{-- /tab-content --}}
</div>

{{-- ═══ EDIT MODALS ═══ --}}

{{-- Edit Diagnosa Modal --}}
<div class="modal fade" id="modalEditDiagnosa" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditDiagnosa" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Diagnosa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-bold">Kode ICD</label><input type="text" name="kode" id="edit_diagnosa_kode" class="form-control"></div>
                    <div class="mb-3"><label class="form-label fw-bold">Nama Diagnosa <span class="text-danger">*</span></label><input type="text" name="nama" id="edit_diagnosa_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Kategori</label><input type="text" name="kategori" id="edit_diagnosa_kat" class="form-control" list="diagnosa-kat-list"></div>
                    <div class="mb-3"><label class="form-label fw-bold">Deskripsi</label><input type="text" name="deskripsi" id="edit_diagnosa_desk" class="form-control"></div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_diagnosa_active" value="1" checked>
                        <label class="form-check-label" for="edit_diagnosa_active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Keluhan Modal --}}
<div class="modal fade" id="modalEditKeluhan" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditKeluhan" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Keluhan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-bold">Nama Keluhan <span class="text-danger">*</span></label><input type="text" name="nama" id="edit_keluhan_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Kategori</label><input type="text" name="kategori" id="edit_keluhan_kat" class="form-control" list="keluhan-kat-list"></div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_keluhan_active" value="1" checked>
                        <label class="form-check-label" for="edit_keluhan_active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Tindakan Modal --}}
<div class="modal fade" id="modalEditTindakan" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditTindakan" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Tindakan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-bold">Nama Tindakan <span class="text-danger">*</span></label><input type="text" name="nama" id="edit_tindakan_nama" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Kategori</label><input type="text" name="kategori" id="edit_tindakan_kat" class="form-control" list="tindakan-kat-list"></div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_tindakan_active" value="1" checked>
                        <label class="form-check-label" for="edit_tindakan_active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditDiagnosa(id, nama, kode, kat, desk) {
    document.getElementById('edit_diagnosa_nama').value = nama;
    document.getElementById('edit_diagnosa_kode').value = kode;
    document.getElementById('edit_diagnosa_kat').value  = kat;
    document.getElementById('edit_diagnosa_desk').value = desk;
    document.getElementById('formEditDiagnosa').action = `/master-medis/diagnosa/${id}`;
    new bootstrap.Modal(document.getElementById('modalEditDiagnosa')).show();
}
function openEditKeluhan(id, nama, kat) {
    document.getElementById('edit_keluhan_nama').value = nama;
    document.getElementById('edit_keluhan_kat').value  = kat;
    document.getElementById('formEditKeluhan').action = `/master-medis/keluhan/${id}`;
    new bootstrap.Modal(document.getElementById('modalEditKeluhan')).show();
}
function openEditTindakan(id, nama, kat) {
    document.getElementById('edit_tindakan_nama').value = nama;
    document.getElementById('edit_tindakan_kat').value  = kat;
    document.getElementById('formEditTindakan').action = `/master-medis/tindakan/${id}`;
    new bootstrap.Modal(document.getElementById('modalEditTindakan')).show();
}

// Keep active tab after redirect
const hash = window.location.hash;
if (hash) {
    const tab = document.querySelector(`a[href="${hash}"]`);
    if (tab) new bootstrap.Tab(tab).show();
}
</script>
@endpush
