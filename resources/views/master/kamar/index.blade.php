@extends('layouts.app')

@section('title', 'Data Kamar / Asrama')
@section('page_title', 'Master Data Kamar')
@section('page_description', 'Kelola daftar kamar dan asrama santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kamar</li>
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
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0aa2c0)">
            <div class="icon"><i class="bi bi-door-closed-fill"></i></div>
            <div class="info"><h3>{{ $kamars->count() }}</h3><p>Total Kamar</p></div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kamar Baru</h5>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('kamar.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kamar / Gedung</label>
                        <input type="text" name="nama_kamar" class="form-control" placeholder="Misal: Al-Fatih 01" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Kamar</button>
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
                                <th>Nama Kamar</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kamars as $km)
                            <tr>
                                <td class="fw-bold">{{ $km->nama_kamar }}</td>
                                <td class="text-center">
                                    <form action="{{ route('kamar.destroy', $km->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kamar ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">Belum ada data kamar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
