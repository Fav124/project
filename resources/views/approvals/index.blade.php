@extends('layouts.app')

@section('title', 'Persetujuan (Approval)')
@section('page_title', 'Antrian Persetujuan')
@section('page_description', 'Tinjau dan setujui permintaan perubahan data atau registrasi akun baru.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Approval</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pemohon</th>
                        <th>Jenis Permintaan</th>
                        <th>Data Terkait</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvals as $approval)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $approval->created_at->format('d/m/y') }}</div>
                            <small class="text-muted">{{ $approval->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            @if($approval->requester)
                                <div class="fw-bold">{{ $approval->requester->name }}</div>
                                <small class="text-muted">{{ $approval->requester->email }}</small>
                            @else
                                <span class="badge bg-light-secondary text-secondary">Registrasi Baru</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeLabel = match($approval->action) {
                                    'register_user' => 'Registrasi User',
                                    'update' => 'Pembaruan Data',
                                    'delete' => 'Penghapusan Data',
                                    default => class_basename($approval->approvable_type)
                                };
                                $color = match($approval->action) {
                                    'register_user' => 'primary',
                                    'update' => 'warning',
                                    'delete' => 'danger',
                                    default => 'info'
                                };
                            @endphp
                            <span class="badge bg-light-{{ $color }} text-{{ $color }}">{{ $typeLabel }}</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    onclick="showDetail({{ json_encode($approval->payload) }}, '{{ $typeLabel }}')">
                                <i class="bi bi-info-circle me-1"></i> Detail Data
                            </button>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $approval->notes }}">
                                {{ $approval->notes ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('approvals.approve', $approval->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success px-3 me-1" onclick="return confirm('Setujui permintaan ini?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger px-3" onclick="openRejectModal({{ $approval->id }})">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle fs-1 mb-2 d-block text-success"></i>
                            <h5>Semua Beres!</h5>
                            <p>Tidak ada permintaan persetujuan yang tertunda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Permintaan: <span id="detailTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    {{-- Data will be injected here --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formReject" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Permintaan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" required placeholder="Sebutkan alasan mengapa permintaan ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDetail(payload, title) {
        document.getElementById('detailTitle').innerText = title;
        let content = '<ul class="list-group list-group-flush">';
        
        for (let key in payload) {
            let label = key.replace('_', ' ').toUpperCase();
            let value = payload[key];
            
            // Format nested or boolean values
            if (typeof value === 'object' && value !== null) value = JSON.stringify(value);
            if (typeof value === 'boolean') value = value ? 'YA' : 'TIDAK';
            
            content += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">${label}</span>
                    <span class="fw-bold">${value || '-'}</span>
                </li>
            `;
        }
        content += '</ul>';
        
        document.getElementById('detailContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('modalDetail')).show();
    }

    function openRejectModal(id) {
        document.getElementById('formReject').action = `/approvals/${id}/reject`;
        new bootstrap.Modal(document.getElementById('modalReject')).show();
    }
</script>
@endpush
