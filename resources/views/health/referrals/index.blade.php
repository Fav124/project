@extends('layouts.app')

@section('title', 'Rujukan RS')
@section('page-title', 'Manajemen Rujukan Rumah Sakit')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Data Rujukan">
            <x-slot name="header">
                <h4 class="card-title">Daftar Rujukan Santri</h4>
                <button type="button" class="btn btn-primary btn-icon-text" data-toggle="modal" data-target="#createModal">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Buat Rujukan Baru
                </button>
            </x-slot>

            <form action="{{ route('referrals.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="search" class="form-control text-white" placeholder="Cari santri atau RS..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="status" class="form-select text-white">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Tgl Rujuk</th>
                        <th>Santri</th>
                        <th>Rumah Sakit</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $referral->referral_date->format('d/m/Y') }}</td>
                            <td>{{ $referral->santri->name }}</td>
                            <td>{{ $referral->hospital_name }}</td>
                            <td>
                                @php
                                    $statusMap = match($referral->status) {
                                        'pending' => ['class' => 'badge-outline-warning', 'label' => 'Pending'],
                                        'ongoing' => ['class' => 'badge-outline-info', 'label' => 'Diproses'],
                                        'completed' => ['class' => 'badge-outline-success', 'label' => 'Selesai'],
                                        default => ['class' => 'badge-outline-secondary', 'label' => $referral->status]
                                    };
                                @endphp
                                <div class="badge {{ $statusMap['class'] }}">{{ $statusMap['label'] }}</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('referrals.index', array_merge(request()->query(), ['detail' => $referral->id])) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('referrals.index', array_merge(request()->query(), ['edit' => $referral->id])) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('referrals.destroy', $referral) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data rujukan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $referrals->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Rujukan Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('referrals.store') }}" method="POST" data-ajax="true">
                @csrf
                <div class="modal-body">
                    <div id="referral-rows">
                        <div class="referral-row border-bottom border-secondary mb-4 pb-3">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Santri</label>
                                        <select name="referrals[0][santri_id]" class="form-select text-white select2" required>
                                            <option value="">Pilih Santri</option>
                                            @foreach($santris as $santri)
                                                <option value="{{ $santri->id }}">{{ $santri->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Tanggal Rujuk</label>
                                        <input type="date" name="referrals[0][referral_date]" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Rumah Sakit</label>
                                        <input type="text" name="referrals[0][hospital_name]" class="form-control" placeholder="RSUD dr. Soetomo" required>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Diagnosa Awal</label>
                                        <input type="text" name="referrals[0][diagnosis]" class="form-control" placeholder="Demam Tinggi" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="form-label text-small">Alasan Rujukan</label>
                                        <textarea name="referrals[0][reason]" class="form-control" rows="2" required></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="form-label text-small">Status</label>
                                        <select name="referrals[0][status]" class="form-select text-white" required>
                                            <option value="pending">Pending</option>
                                            <option value="ongoing">Diproses</option>
                                            <option value="completed">Selesai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-row mb-1" style="display:none;">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info btn-block btn-sm" id="add-row">
                        <i class="mdi mdi-plus"></i> Tambah Baris Rujukan
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
@if($editReferral)
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Rujukan: {{ $editReferral->santri->name }}</h5>
                <a href="{{ route('referrals.index') }}" class="close text-white"></a>
            </div>
            <form action="{{ route('referrals.update', $editReferral) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Santri</label>
                            <select name="santri_id" class="form-select text-white" required>
                                @foreach($santris as $santri)
                                    <option value="{{ $santri->id }}" {{ $editReferral->santri_id == $santri->id ? 'selected' : '' }}>{{ $santri->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="referral_date" class="form-control" value="{{ $editReferral->referral_date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rumah Sakit</label>
                        <input type="text" name="hospital_name" class="form-control" value="{{ $editReferral->hospital_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" required>{{ $editReferral->reason }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select text-white" required>
                                <option value="pending" {{ $editReferral->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="ongoing" {{ $editReferral->status == 'ongoing' ? 'selected' : '' }}>Diproses</option>
                                <option value="completed" {{ $editReferral->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $editReferral->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('referrals.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Detail Modal --}}
@if($detailReferral)
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Detail Rujukan RS: {{ $detailReferral->santri->name }}</h5>
                <a href="{{ route('referrals.index') }}" class="close text-white"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-4 text-center">
                    <div class="col-12">
                        <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                            <i class="mdi mdi-hospital-building text-white" style="font-size: 32px;"></i>
                        </div>
                        <h4 class="text-white">{{ $detailReferral->hospital_name }}</h4>
                        <p class="text-muted">Tanggal: {{ $detailReferral->referral_date->format('d F Y') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Informasi Santri</h6>
                        <p class="mb-1 text-muted small">Nama</p>
                        <p class="text-white font-weight-bold">{{ $detailReferral->santri->name }}</p>
                        <p class="mb-1 text-muted small">Wali</p>
                        <p class="text-white">{{ $detailReferral->santri->guardian_name ?: '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Status & Petugas</h6>
                        <p class="mb-1 text-muted small">Status</p>
                        <div class="badge {{ $statusMap['class'] }}">{{ $statusMap['label'] }}</div>
                        <p class="mt-2 mb-1 text-muted small">Dirujuk Oleh</p>
                        <p class="text-white">{{ $detailReferral->referrer->name }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Diagnosa / Alasan Rujukan</h6>
                        <p class="text-white p-3 bg-dark rounded">{{ $detailReferral->reason }}</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-primary border-bottom border-secondary pb-2">Catatan Perkembangan</h6>
                        <p class="text-white p-3 bg-dark rounded">{{ $detailReferral->notes ?: 'Belum ada catatan perkembangan.' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('referrals.index') }}" class="btn btn-secondary">Tutup</a>
                <a href="{{ route('referrals.notify', $detailReferral) }}" class="btn btn-success">
                    <i class="mdi mdi-whatsapp"></i> Kabari Wali Santri
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($editReferral)
            new bootstrap.Modal(document.getElementById('editModal')).show();
        @endif
        @if($detailReferral)
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        @endif

        // Dynamic Rows Logic
        let rowCount = 1;
        const addRowBtn = document.getElementById('add-row');
        const referralRows = document.getElementById('referral-rows');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.querySelector('.referral-row').cloneNode(true);
            
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/referrals\[\d+\]/, `referrals[${rowCount}]`));
                }
                if (input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') {
                    if (!input.name.includes('[referral_date]')) {
                        input.value = '';
                    }
                }
            });

            newRow.querySelector('.remove-row').style.display = 'block';
            referralRows.appendChild(newRow);
            rowCount++;
        });

        referralRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.referral-row').remove();
            }
        });
    });
</script>
@endpush
@endsection
