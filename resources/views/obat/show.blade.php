@extends('layouts.app')

@section('title', 'Detail Obat')
@section('page_title', 'Detail Obat: ' . $obat->nama_obat)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('obat.index') }}">Obat</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Informasi Obat</h4>
                @php
                    $gol = $obat->golongan;
                    $icon = match($gol) {
                        'bebas' => '<span class="d-inline-block rounded-circle bg-success" style="width: 24px; height: 24px; border: 2px solid #000;" title="Obat Bebas"></span>',
                        'bebas_terbatas' => '<span class="d-inline-block rounded-circle bg-info" style="width: 24px; height: 24px; border: 2px solid #000;" title="Obat Bebas Terbatas"></span>',
                        'keras' => '<div class="d-inline-block rounded-circle bg-danger text-white text-center fw-bold" style="width: 28px; height: 28px; border: 2px solid #000; font-size: 16px; line-height: 24px;" title="Obat Keras">K</div>',
                        'narkotika' => '<div class="d-inline-block rounded-circle bg-white text-danger text-center fw-bold" style="width: 28px; height: 28px; border: 2px solid #f00; font-size: 16px; line-height: 24px;" title="Narkotika"><i class="bi bi-plus-circle-fill"></i></div>',
                        'psikotropika' => '<div class="d-inline-block rounded-circle bg-white text-info text-center fw-bold" style="width: 28px; height: 28px; border: 2px solid #0dcaf0; font-size: 16px; line-height: 24px;" title="Psikotropika"><i class="bi bi-star-fill"></i></div>',
                        default => '',
                    };
                @endphp
                {!! $icon !!}
            </div>
            <div class="card-body">
                @if($obat->foto)
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/' . $obat->foto) }}" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                    </div>
                @endif
                <table class="table table-sm table-borderless">
                    <tr><td width="40%" class="text-muted">Kode Obat</td><td class="fw-bold">: {{ $obat->kode_obat }}</td></tr>
                    <tr><td class="text-muted">Nama Obat</td><td class="fw-bold">: {{ $obat->nama_obat }}</td></tr>
                    <tr><td class="text-muted">Golongan</td><td class="text-capitalize">: {{ str_replace('_', ' ', $obat->golongan) }}</td></tr>
                    <tr><td class="text-muted">Kategori</td><td>: {{ $obat->kategori }}</td></tr>
                    <tr><td class="text-muted">Bentuk Sediaan</td><td>: {{ $obat->bentuk_sediaan }}</td></tr>
                    <tr><td class="text-muted">Satuan</td><td>: {{ $obat->satuan }}</td></tr>
                    <tr><td class="text-muted">Lokasi Simpan</td><td>: {{ $obat->lokasi_penyimpanan }}</td></tr>
                    <tr><td class="text-muted">Tgl Kadaluarsa</td><td>: 
                        <span class="{{ $obat->tanggal_kadaluarsa->isPast() ? 'text-danger fw-bold' : '' }}">
                            {{ $obat->tanggal_kadaluarsa->format('d F Y') }}
                        </span>
                    </td></tr>
                </table>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted small">Stok Saat Ini</p>
                        <h2 class="mb-0 {{ $obat->stok <= $obat->stok_minimum ? 'text-danger' : 'text-primary' }}">{{ $obat->stok }} <small class="text-muted fs-6">{{ $obat->satuan }}</small></h2>
                    </div>
                    <div class="text-end">
                        <p class="mb-0 text-muted small">Stok Minimum</p>
                        <h5 class="mb-0">{{ $obat->stok_minimum }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-outline-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalMutasi">
                    <i class="bi bi-arrow-left-right me-2"></i> Mutasi Stok
                </button>
                <a href="{{ route('obat.edit', $obat->id) }}" class="btn btn-warning text-white">
                    <i class="bi bi-pencil"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h4>Riwayat Mutasi Stok (15 Terakhir)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Sisa</th>
                                <th>Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($obat->riwayatStok as $riwayat)
                            <tr>
                                <td class="text-sm">{{ $riwayat->created_at->format('d/m/y H:i') }}</td>
                                <td>
                                    @php
                                        $badge = match($riwayat->jenis_mutasi) {
                                            'masuk' => 'success',
                                            'keluar' => 'danger',
                                            'penyesuaian' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ strtoupper($riwayat->jenis_mutasi) }}</span>
                                </td>
                                <td class="fw-bold {{ in_array($riwayat->jenis_mutasi, ['keluar', 'rusak', 'kadaluarsa']) ? 'text-danger' : 'text-success' }}">
                                    {{ in_array($riwayat->jenis_mutasi, ['keluar', 'rusak', 'kadaluarsa']) ? '-' : '+' }}{{ $riwayat->jumlah }}
                                </td>
                                <td>{{ $riwayat->stok_sesudah }}</td>
                                <td class="text-sm">{{ $riwayat->user?->name ?: 'System' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">Belum ada riwayat mutasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Mutasi Stok --}}
<div class="modal fade" id="modalMutasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('obat.update-stok', $obat->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catat Mutasi Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Mutasi</label>
                        <select name="jenis_mutasi" class="form-select" required>
                            <option value="masuk">Stok Masuk / Restok</option>
                            <option value="keluar">Stok Keluar / Distribusi</option>
                            <option value="penyesuaian">Penyesuaian Opname</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah ({{ $obat->satuan }})</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Alasan mutasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
