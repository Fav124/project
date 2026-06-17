@extends('layouts.app')

@section('title', 'Edit Obat - ' . $medicine->name)
@section('page-title', 'Edit Obat')

@section('page-actions')
    <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Obat</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('medicines.update', $medicine) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Nama Obat</label>
                        <input type="text" name="name" class="form-control text-white" value="{{ $medicine->name }}" required>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Kode Obat</label>
                        <input type="text" name="kode_obat" class="form-control text-white" value="{{ $medicine->kode_obat }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Kategori</label>
                        <input type="text" name="kategori" class="form-control text-white" value="{{ $medicine->kategori }}">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Bentuk Sediaan</label>
                        <input type="text" name="bentuk_sediaan" class="form-control text-white" value="{{ $medicine->bentuk_sediaan }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Satuan</label>
                        <input type="text" name="unit" class="form-control text-white" value="{{ $medicine->unit }}" required>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Lokasi Penyimpanan</label>
                        <input type="text" name="lokasi_penyimpanan" class="form-control text-white" value="{{ $medicine->lokasi_penyimpanan }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Stok Saat Ini</label>
                        <input type="number" name="stock" class="form-control text-white" value="{{ $medicine->stock }}" required min="0">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Stok Minimum</label>
                        <input type="number" name="minimum_stock" class="form-control text-white" value="{{ $medicine->minimum_stock }}" required min="0">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Kadaluarsa</label>
                    <input type="date" name="expiry_date" class="form-control text-white" value="{{ $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '' }}">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Deskripsi</label>
                    <textarea name="description" class="form-control text-white" rows="3">{{ $medicine->description }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
