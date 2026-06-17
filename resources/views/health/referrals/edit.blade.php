@extends('layouts.app')

@section('title', 'Edit Rujukan - ' . $referral->santri->name)
@section('page-title', 'Edit Rujukan Rumah Sakit')

@section('page-actions')
    <a href="{{ route('referrals.show', $referral) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Rujukan</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('referrals.update', $referral) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Santri</label>
                        <select name="santri_id" class="form-select text-white" required>
                            <option value="">Pilih Santri</option>
                            @foreach($santris as $santri)
                                <option value="{{ $santri->id }}" {{ $referral->santri_id == $santri->id ? 'selected' : '' }}>{{ $santri->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Rujuk</label>
                        <input type="date" name="referral_date" class="form-control text-white" value="{{ $referral->referral_date->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Rumah Sakit Tujuan</label>
                    <input type="text" name="hospital_name" class="form-control text-white" value="{{ $referral->hospital_name }}" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Diagnosa Awal</label>
                    <input type="text" name="diagnosis" class="form-control text-white" value="{{ $referral->diagnosis }}">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Alasan Rujukan</label>
                    <textarea name="reason" class="form-control text-white" rows="3" required>{{ $referral->reason }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Transportasi</label>
                        <input type="text" name="transport" class="form-control text-white" value="{{ $referral->transport }}" placeholder="Ambulans / Kendaraan Pribadi">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Pendamping</label>
                        <input type="text" name="companion_name" class="form-control text-white" value="{{ $referral->companion_name }}" placeholder="Nama pendamping">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Status</label>
                        <select name="status" class="form-select text-white" required>
                            <option value="pending" {{ $referral->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ongoing" {{ $referral->status == 'ongoing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $referral->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Catatan</label>
                    <textarea name="notes" class="form-control text-white" rows="2">{{ $referral->notes }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('referrals.show', $referral) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
