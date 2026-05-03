@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page_title', 'Log Aktivitas Sistem')
@section('page_description', 'Rekam jejak seluruh perubahan data yang dilakukan oleh pengguna.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Audit Trail</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-4">
                <form action="{{ route('audit-logs.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari User/Aksi/Model..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Model / Entitas</th>
                        <th>Keterangan</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-sm">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <div class="fw-bold">{{ $log->user?->name ?: 'System' }}</div>
                            <small class="text-muted">{{ $log->user?->role?->value }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $log->event === 'created' ? 'success' : ($log->event === 'deleted' ? 'danger' : 'info') }}">
                                {{ strtoupper($log->event) }}
                            </span>
                        </td>
                        <td>
                            <code class="text-sm">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</code>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="showDiff({{ $log->id }})">
                                <i class="bi bi-eye"></i> Lihat Perubahan
                            </button>
                        </td>
                        <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Belum ada aktivitas tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

            {{ $logs->links() }}
        </div>
    </div>
</div>

{{-- Modal for Diff --}}
<div class="modal fade" id="diffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="diffContainer" class="table-responsive">
                    {{-- Table will be injected here via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const auditLogs = @json($logs->items());
    const fieldLabels = {
        'nama_lengkap': 'Nama Lengkap',
        'nis': 'NIS',
        'nisn': 'NISN',
        'jenis_kelamin': 'Jenis Kelamin',
        'tempat_lahir': 'Tempat Lahir',
        'tanggal_lahir': 'Tanggal Lahir',
        'nama_obat': 'Nama Obat',
        'kode_obat': 'Kode Obat',
        'stok': 'Stok',
        'stok_minimum': 'Stok Minimum',
        'tanggal_kadaluarsa': 'Tgl Kadaluarsa',
        'harga_beli': 'Harga Beli',
        'harga_jual': 'Harga Jual',
        'status': 'Status',
        'role': 'Role',
        'keluhan_utama': 'Keluhan Utama',
        'diagnosa_sementara': 'Diagnosa',
        'tindakan': 'Tindakan',
        'kategori': 'Kategori',
        'golongan': 'Golongan',
        'lokasi_penyimpanan': 'Lokasi',
        'updated_at': 'Waktu Update',
        'created_at': 'Waktu Dibuat',
        'deleted_at': 'Waktu Dihapus'
    };

    function showDiff(id) {
        const log = auditLogs.find(l => l.id === id);
        if (!log) return;

        let oldData = {};
        let newData = {};

        try {
            oldData = log.old_values ? (typeof log.old_values === 'string' ? JSON.parse(log.old_values) : log.old_values) : {};
            newData = log.new_values ? (typeof log.new_values === 'string' ? JSON.parse(log.new_values) : log.new_values) : {};
        } catch (e) {
            console.error("Error parsing JSON", e);
        }

        const allKeys = [...new Set([...Object.keys(oldData), ...Object.keys(newData)])];
        let html = `
            <table class="table table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th>Bidang (Field)</th>
                        <th>Nilai Lama</th>
                        <th>Nilai Baru</th>
                    </tr>
                </thead>
                <tbody>
        `;

        allKeys.forEach(key => {
            if (['id', 'password', 'remember_token', 'deleted_at'].includes(key) && !newData[key]) return;
            
            const label = fieldLabels[key] || key.replace(/_/g, ' ').toUpperCase();
            const oldVal = oldData[key] === null ? '<span class="text-muted italic">Kosong</span>' : oldData[key];
            const newVal = newData[key] === null ? '<span class="text-muted italic">Kosong</span>' : newData[key];
            
            const isChanged = oldData[key] !== newData[key];
            const rowClass = isChanged ? 'table-warning' : '';

            html += `
                <tr class="${rowClass}">
                    <td><strong>${label}</strong></td>
                    <td class="text-danger"><del>${oldVal}</del></td>
                    <td class="text-success fw-bold">${newVal}</td>
                </tr>
            `;
        });

        if (allKeys.length === 0) {
            html += `<tr><td colspan="3" class="text-center">Tidak ada detail perubahan field.</td></tr>`;
        }

        html += '</tbody></table>';

        document.getElementById('diffContainer').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('diffModal'));
        modal.show();
    }
</script>
@endsection
