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
                            <td>{{ $referral->referral_date->translatedFormat('d F Y') }}</td>
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
                                    <a href="{{ route('referrals.show', $referral) }}" class="btn btn-outline-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    @if($referral->status != 'pending')
                                        <form action="{{ route('referrals.updateStatus', $referral) }}" method="POST" class="d-inline" onsubmit="return confirm('Ubah status ke Pending?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Set Pending">
                                                <i class="mdi mdi-clock-outline"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($referral->status != 'ongoing')
                                        <form action="{{ route('referrals.updateStatus', $referral) }}" method="POST" class="d-inline" onsubmit="return confirm('Ubah status ke Diproses?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="ongoing">
                                            <button type="submit" class="btn btn-outline-info btn-sm" title="Set Diproses">
                                                <i class="mdi mdi-play-circle-outline"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($referral->status != 'completed')
                                        <form action="{{ route('referrals.updateStatus', $referral) }}" method="POST" class="d-inline" onsubmit="return confirm('Ubah status ke Selesai?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Set Selesai">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('referrals.edit', $referral) }}" class="btn btn-outline-warning btn-sm">
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
                                            @foreach($allSantris as $santri)
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



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
