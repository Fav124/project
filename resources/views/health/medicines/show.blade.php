@extends('layouts.app')

@section('title', 'Detail Obat - ' . $medicine->name)
@section('page-title', 'Detail Obat')

@section('page-actions')
    <a href="{{ route('medicines.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
    <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px;">
        
        {{-- Medicine Info --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-pills"></i> Informasi Obat</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">{{ $medicine->name }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Kode: {{ $medicine->kode_obat ?: '-' }}</div>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border); padding-top: 24px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Kategori</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $medicine->kategori ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Bentuk Sediaan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $medicine->bentuk_sediaan ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Satuan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $medicine->unit }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Lokasi Penyimpanan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $medicine->lokasi_penyimpanan ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <div style="display: flex; flex-direction: column; gap: 32px;">
            {{-- Stock Info --}}
            <x-ui.card>
                <x-slot name="header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2><i class="fas fa-warehouse"></i> Stok & Batch</h2>
                        <button type="button" class="btn btn-xs btn-outline-success mutation-btn" data-toggle="modal" data-target="#mutationModal" data-id="{{ $medicine->id }}" data-name="{{ $medicine->name }}" data-stock="{{ $medicine->stock }}">
                            <i class="fas fa-exchange-alt"></i> Mutasi Stok
                        </button>
                    </div>
                </x-slot>
                <div style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                        <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Total Stok</div>
                            <div style="font-size: 24px; font-weight: 800; color: var(--brand-start);">{{ $medicine->stock }}</div>
                        </div>
                        <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Minimum</div>
                            <div style="font-size: 24px; font-weight: 800; color: {{ $medicine->stock <= $medicine->minimum_stock ? 'var(--danger)' : 'var(--text-main)' }};">{{ $medicine->minimum_stock }}</div>
                        </div>
                        <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border); text-align: center;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Kadaluarsa</div>
                            <div style="font-size: 16px; font-weight: 800; color: {{ $medicine->expiry_date ? ($medicine->expiry_date->isPast() ? 'var(--danger)' : ($medicine->expiry_date->diffInDays(now()) < 90 ? 'var(--warning)' : 'var(--success)')) : 'var(--text-muted)' }};">
                                {{ $medicine->expiry_date ? $medicine->expiry_date->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <h3 style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">Batch/Stok per Tanggal Kadaluarsa</h3>
                    <table class="table" style="color: var(--text-main);">
                        <thead>
                            <tr>
                                <th>No. Batch</th>
                                <th>Jumlah</th>
                                <th>Tgl Kadaluarsa</th>
                                <th>Tgl Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicine->batches as $batch)
                                <tr>
                                    <td><small>{{ $batch->batch_number ?: '-' }}</small></td>
                                    <td>{{ $batch->quantity }}</td>
                                    <td>{{ $batch->expiry_date->format('d M Y') }}</td>
                                    <td>{{ $batch->received_date->format('d M Y') }}</td>
                                    <td>
                                        @if($batch->isExpired())
                                            <span class="badge badge-danger">Kadaluarsa</span>
                                        @elseif($batch->isExpiringSoon())
                                            <span class="badge badge-warning">Segera Expired</span>
                                        @else
                                            <span class="badge badge-success">Aman</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data batch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            {{-- Mutation History --}}
            <x-ui.card>
                <x-slot name="header">
                    <h2><i class="fas fa-history"></i> Riwayat Mutasi Stok</h2>
                </x-slot>
                <div style="padding: 24px;">
                    <table class="table" style="color: var(--text-main);">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Sebelum</th>
                                <th>Sesudah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicine->mutations as $mutation)
                                <tr>
                                    <td>{{ $mutation->created_at->translatedFormat('d M Y H:i') }}</td>
                                    <td>
                                        @if($mutation->type === 'in')
                                            <span class="badge badge-success">Stok Masuk</span>
                                        @elseif($mutation->type === 'out')
                                            <span class="badge badge-danger">Stok Keluar</span>
                                        @else
                                            <span class="badge badge-info">Penyesuaian</span>
                                        @endif
                                    </td>
                                    <td>{{ $mutation->amount }}</td>
                                    <td>{{ $mutation->before_stock }}</td>
                                    <td>{{ $mutation->after_stock }}</td>
                                    <td style="max-width: 150px; word-wrap: break-word;">{{ $mutation->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada riwayat mutasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>

{{-- Mutation Modal --}}
<div class="modal fade" id="mutationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mutasi Stok Obat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('medicines.mutation') }}" method="POST" data-ajax="true">
                @csrf
                <input type="hidden" name="medicine_id" id="mutation-medicine-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Obat</label>
                        <input type="text" id="mutation-medicine-name" class="form-control text-white bg-dark" readonly style="color: #64748b !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="text" id="mutation-medicine-stock" class="form-control text-white bg-dark" readonly style="color: #64748b !important;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Mutasi</label>
                        <select name="type" class="form-select text-white" required>
                            <option value="in">Stok Masuk (+)</option>
                            <option value="out">Stok Keluar (-)</option>
                            <option value="adjustment">Penyesuaian Stok (Set)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah / Nilai Baru</label>
                        <input type="number" name="amount" class="form-control text-white" min="1" required placeholder="Masukkan jumlah stok...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kadaluarsa (untuk Stok Masuk)</label>
                        <input type="date" name="expiry_date" class="form-control text-white">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Catatan</label>
                        <textarea name="notes" class="form-control text-white" rows="2" placeholder="Catatan mutasi stok..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('click', '.mutation-btn', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const stock = $(this).data('stock');
    
    $('#mutation-medicine-id').val(id);
    $('#mutation-medicine-name').val(name);
    $('#mutation-medicine-stock').val(stock);
});
</script>
@endpush
@endsection
