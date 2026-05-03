@extends('layouts.app')

@section('title', 'Database Backup')
@section('page_title', 'Manajemen Backup Data')
@section('page_description', 'Amankan data Anda dengan melakukan backup berkala. Seluruh file tersimpan di storage lokal.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Backup</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Riwayat Backup</h4>
                <form action="{{ route('backups.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-arrow-up me-2"></i> Backup Sekarang
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 15%">Waktu</th>
                                <th style="width: 35%">Nama File</th>
                                <th style="width: 10%">Ukuran</th>
                                <th style="width: 10%">Status</th>
                                <th style="width: 15%">Petugas</th>
                                <th style="width: 15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                            <tr>
                                <td class="text-sm">{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <code class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $backup->filename }}">
                                        {{ $backup->filename }}
                                    </code>
                                </td>
                                <td>{{ $backup->size_for_humans }}</td>
                                <td>
                                    <span class="badge bg-{{ $backup->status === 'success' ? 'success' : 'danger' }}">
                                        {{ strtoupper($backup->status) }}
                                    </span>
                                </td>
                                <td><span class="small">{{ $backup->user?->name ?: 'System' }}</span></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('backups.download', $backup->id) }}" class="btn btn-sm btn-primary" title="Download SQL">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @if($backup->status === 'success')
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="confirmRestore({{ $backup->id }}, '{{ $backup->filename }}')"
                                                title="Restore Database">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada riwayat backup.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        {{-- Import Backup Card --}}
        <div class="card mb-4">
            <div class="card-header p-3">
                <h6 class="card-title mb-0">Import File Backup</h6>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('backups.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih File SQL (.sql)</label>
                        <input type="file" name="backup_file" class="form-control form-control-sm" accept=".sql" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-info w-100">
                        <i class="bi bi-upload me-1"></i> Import
                    </button>
                </form>
            </div>
        </div>

        <div class="card bg-light-primary">
            <div class="card-body p-3">
                <h6 class="text-primary"><i class="bi bi-info-circle me-1"></i> Info</h6>
                <p class="x-small mb-1">Backup otomatis: 00:00 WIB.</p>
                <p class="x-small mb-2">Lokasi: <code>storage/app/backups/</code></p>
                <hr class="my-2">
                <div class="alert alert-warning p-2 x-small mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i> <strong>Restore:</strong> Menimpa data saat ini.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Restore --}}
<div class="modal fade" id="modalRestore" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRestore" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white"><i class="bi bi-exclamation-octagon me-2"></i>Konfirmasi Restore</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin me-restore database menggunakan file:</p>
                    <div class="p-2 bg-light border rounded mb-3">
                        <code id="namaFileRestore"></code>
                    </div>
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        <strong>Tindakan ini tidak dapat dibatalkan!</strong> Seluruh data yang ada saat ini akan hilang dan digantikan oleh data dari backup ini.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Ya, Restore Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmRestore(id, filename) {
        document.getElementById('namaFileRestore').innerText = filename;
        document.getElementById('formRestore').action = `/backups/${id}/restore`;
        var myModal = new bootstrap.Modal(document.getElementById('modalRestore'));
        myModal.show();
    }
</script>
@endpush
