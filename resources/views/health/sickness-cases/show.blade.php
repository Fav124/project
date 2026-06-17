@extends('layouts.app')

@section('title', 'Detail Kasus - ' . $sicknessCase->santri->name)
@section('page-title', 'Detail Pemantauan Pasien')

@section('page-actions')
    <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
    
    {{-- Patient Info --}}
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-user-injured"></i> Informasi Pasien</h2>
            <a href="{{ route('santri.show', $sicknessCase->santri) }}" class="btn btn-xs btn-primary">Lihat Profil Santri</a>
        </x-slot>
        <div style="padding: 24px;">
            <div style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">{{ $sicknessCase->santri->name }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">NIS: {{ $sicknessCase->santri->nis ?: '-' }}</div>
            
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border); padding-top: 24px;">
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Ditangani Oleh</div>
                    <div style="font-weight: 600; color: var(--text-main);"><i class="fas fa-user-nurse" style="color: var(--brand-start);"></i> {{ optional($sicknessCase->handledBy)->name ?: 'Petugas Sistem' }}</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <div style="display:flex; flex-direction:column; gap:32px;">
        {{-- Case Details --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-file-medical-alt"></i> Detail Kunjungan & Tindakan</h2>
                @php
                    $statusTheme = match($sicknessCase->status) {
                        'observed' => ['class' => 'badge-warning', 'label' => 'Observasi'],
                        'handled' => ['class' => 'badge-info', 'label' => 'Ditangani'],
                        'recovered' => ['class' => 'badge-success', 'label' => 'Sembuh'],
                        'referred' => ['class' => 'badge-danger', 'label' => 'Dirujuk'],
                        default => ['class' => 'badge-outline', 'label' => $sicknessCase->status],
                    };
                @endphp
                <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Masuk</div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 15px;">{{ $sicknessCase->visit_date->translatedFormat('d F Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Selesai / Sembuh</div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 15px;">{{ optional($sicknessCase->return_date)->translatedFormat('d F Y') ?: 'Belum ditentukan' }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                    <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                        <div style="font-size: 12px; font-weight: 700; color: var(--brand-start); text-transform: uppercase; margin-bottom: 8px;">Keluhan Utama</div>
                        <p style="margin: 0; line-height: 1.6; color: var(--text-main);">{{ $sicknessCase->complaint ?: '-' }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Diagnosis</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $sicknessCase->diagnosis ?: '-' }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Tindakan Awal</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $sicknessCase->action_taken ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Facilities --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 32px;">
            <x-ui.card>
                <x-slot name="header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2><i class="fas fa-pills"></i> Terapi Obat</h2>
                        <div>
                            <button type="button" class="btn btn-xs btn-outline-info" id="add-medicine-btn">
                                <i class="fas fa-plus"></i> Tambah Obat
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-success" id="mutation-btn">
                                <i class="fas fa-exchange-alt"></i> Mutasi Stok
                            </button>
                        </div>
                    </div>
                </x-slot>
                <div style="padding: 24px;">
                    @if($sicknessCase->medicines->isNotEmpty())
                        <table class="table" style="color: var(--text-main);">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sicknessCase->medicines as $med)
                                    <tr>
                                        <td>{{ $med->name }}</td>
                                        <td>{{ $med->pivot->quantity }} {{ $med->unit }}</td>
                                        <td>
                                            <span class="badge {{ $med->pivot->status == 'taken' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $med->pivot->status == 'taken' ? 'Sudah Diminum' : 'Belum Diminum' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                class="btn btn-xs btn-{{ $med->pivot->status == 'taken' ? 'outline-warning' : 'success' }} update-med-status"
                                                data-pivot-id="{{ $med->pivot->id }}"
                                                data-status="{{ $med->pivot->status == 'taken' ? 'pending' : 'taken' }}">
                                                {{ $med->pivot->status == 'taken' ? 'Batalkan' : 'Tandai Diminum' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <x-ui.empty-state message="Tidak ada obat yang dicatat." />
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</div>

{{-- Add Medicine Modal --}}
<div class="modal fade" id="addMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Obat ke Kasus</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="form-label">Pilih Obat</label>
                    <select id="add-medicine-select" class="form-select text-white">
                        <option value="">Pilih Obat</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}">{{ $medicine->name }} (Stok: {{ $medicine->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" id="add-medicine-quantity" class="form-control text-white" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirm-add-medicine">Tambah</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Add medicine to case
    $('#add-medicine-btn').click(function() {
        $('#addMedicineModal').modal('show');
    });
    
    $('#confirm-add-medicine').click(function() {
        const caseId = {{ $sicknessCase->id }};
        const medicineSelect = $('#add-medicine-select');
        const quantityInput = $('#add-medicine-quantity');
        
        if (!medicineSelect.val()) {
            alert('Pilih obat terlebih dahulu');
            return;
        }
        
        const quantity = parseInt(quantityInput.val()) || 1;
        
        $.ajax({
            url: `/santri-sakit/${caseId}/medicine`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                medicine_id: medicineSelect.val(),
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#addMedicineModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menambahkan obat');
            }
        });
    });

    // Update medicine status
    $('.update-med-status').click(function() {
        const pivotId = $(this).data('pivot-id');
        const status = $(this).data('status');
        
        $.ajax({
            url: `/santri-sakit/medicine/${pivotId}/update-status`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status: status
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengupdate status obat');
            }
        });
    });
});
</script>
@endpush
@endsection
