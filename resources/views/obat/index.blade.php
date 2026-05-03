@extends('layouts.app')

@section('title', 'Data Obat')
@section('page_title', 'Manajemen Inventaris Obat')
@section('page_description', 'Kelola stok, pantau kadaluarsa, dan mutasi obat kesehatan.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Obat</li>
@endsection

@section('content')
<div class="row">
    {{-- Stats Row --}}
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-4 px-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon green mb-2"><i class="bi bi-capsule"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted font-semibold">Total Item</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['aktif'] + $stats['stok_menipis'] + $stats['kadaluarsa'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="{{ route('obat.index', ['filter' => 'stok_menipis']) }}" class="card">
            <div class="card-body py-4 px-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon orange mb-2"><i class="bi bi-graph-down-arrow"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted font-semibold">Stok Menipis</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['stok_menipis'] }}</h6>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('obat.index', ['filter' => 'kadaluarsa']) }}" class="card">
            <div class="card-body py-4 px-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon red mb-2"><i class="bi bi-calendar-x"></i></div>
                    <div class="ms-3">
                        <h6 class="text-muted font-semibold">Kadaluarsa</h6>
                        <h6 class="font-extrabold mb-0">{{ $stats['kadaluarsa'] }}</h6>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-4 px-3 text-center">
                <a href="{{ route('obat.create') }}" class="btn btn-primary w-100 py-3">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Obat Baru
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Daftar Inventaris Obat</h4>
            <div class="d-flex gap-2">
                @if(request('filter'))
                    <a href="{{ route('obat.index') }}" class="btn btn-sm btn-light">Hapus Filter</a>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('obat.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari Kode/Nama Obat..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama Obat</th>
                        <th>Golongan</th>
                        <th>Stok</th>
                        <th>Tgl Kadaluarsa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obats as $obat)
                    <tr>
                        <td>
                            @if($obat->foto)
                                <img src="{{ asset('storage/' . $obat->foto) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded text-center d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-capsule text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td><code>{{ $obat->kode_obat }}</code></td>
                        <td>
                            <div class="fw-bold">{{ $obat->nama_obat }}</div>
                            <small class="text-muted">{{ $obat->bentuk_sediaan }}</small>
                        </td>
                        <td class="text-center">
                            @php
                                $gol = $obat->golongan;
                                $icon = match($gol) {
                                    'bebas' => '<span class="d-inline-block rounded-circle bg-success" style="width: 20px; height: 20px; border: 2px solid #000;" title="Obat Bebas"></span>',
                                    'bebas_terbatas' => '<span class="d-inline-block rounded-circle bg-info" style="width: 20px; height: 20px; border: 2px solid #000;" title="Obat Bebas Terbatas"></span>',
                                    'keras' => '<div class="d-inline-block rounded-circle bg-danger text-white text-center fw-bold" style="width: 24px; height: 24px; border: 2px solid #000; font-size: 14px; line-height: 20px;" title="Obat Keras">K</div>',
                                    'narkotika' => '<div class="d-inline-block rounded-circle bg-white text-danger text-center fw-bold" style="width: 24px; height: 24px; border: 2px solid #f00; font-size: 14px; line-height: 20px;" title="Narkotika"><i class="bi bi-plus-circle-fill"></i></div>',
                                    'psikotropika' => '<div class="d-inline-block rounded-circle bg-white text-info text-center fw-bold" style="width: 24px; height: 24px; border: 2px solid #0dcaf0; font-size: 14px; line-height: 20px;" title="Psikotropika"><i class="bi bi-star-fill"></i></div>',
                                    default => '<span class="badge bg-secondary">Umum</span>',
                                };
                            @endphp
                            {!! $icon !!}
                        </td>
                        <td>
                            <div class="fw-bold {{ $obat->stok <= $obat->stok_minimum ? 'text-danger' : 'text-success' }}">
                                {{ $obat->stok }} <small class="text-muted">{{ $obat->satuan }}</small>
                            </div>
                            <button class="btn btn-xs btn-outline-primary mt-1" onclick="openModalStok({{ $obat->id }}, '{{ $obat->nama_obat }}')">
                                <i class="bi bi-arrow-repeat me-1"></i> Mutasi
                            </button>
                        </td>
                        <td>
                            <span class="{{ $obat->tanggal_kadaluarsa->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $obat->tanggal_kadaluarsa->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $status = $obat->status_obat;
                                $badge = match($status) {
                                    'kadaluarsa' => 'danger',
                                    'hampir_kadaluarsa' => 'warning',
                                    'stok_menipis' => 'orange',
                                    'habis' => 'dark',
                                    default => 'success',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ strtoupper(str_replace('_', ' ', $status)) }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('obat.show', $obat->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('obat.edit', $obat->id) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Data obat tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $obats->links() }}
        </div>
    </div>
</div>

{{-- Modal Mutasi Stok --}}
<div class="modal fade" id="modalStok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formStok" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mutasi Stok: <span id="namaObatModal"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Mutasi <span class="text-danger">*</span></label>
                        <select name="jenis_mutasi" class="form-select" required>
                            <option value="masuk">Restok (Barang Masuk)</option>
                            <option value="keluar">Barang Keluar (Manual)</option>
                            <option value="penyesuaian">Penyesuaian (Audit)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah" class="form-control" required min="1">
                            <span class="input-group-text" id="satuanObatModal">Unit</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Misal: Pembelian baru, Rusak, dll"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModalStok(id, nama) {
        document.getElementById('namaObatModal').innerText = nama;
        document.getElementById('formStok').action = `/obat/${id}/update-stok`;
        var myModal = new bootstrap.Modal(document.getElementById('modalStok'));
        myModal.show();
    }
</script>
@endpush
