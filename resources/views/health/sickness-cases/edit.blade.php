@extends('layouts.app')

@section('title', 'Edit Kasus - ' . $sicknessCase->santri->name)
@section('page-title', 'Edit Kasus Sakit')

@section('page-actions')
    <a href="{{ route('sickness-cases.show', $sicknessCase) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Kasus Sakit</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('sickness-cases.update', $sicknessCase) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Santri</label>
                        <select name="santri_id" class="form-select text-white" required>
                            <option value="">Pilih Santri</option>
                            @foreach($santris as $santri)
                                <option value="{{ $santri->id }}" {{ $sicknessCase->santri_id == $santri->id ? 'selected' : '' }}>{{ $santri->name }} ({{ optional($santri->schoolClass)->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Kunjungan</label>
                        <input type="date" name="visit_date" class="form-control text-white" value="{{ $sicknessCase->visit_date->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Keluhan</label>
                    <textarea name="complaint" class="form-control text-white" rows="3" required>{{ $sicknessCase->complaint }}</textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Diagnosa</label>
                    <textarea name="diagnosis" class="form-control text-white" rows="3">{{ $sicknessCase->diagnosis }}</textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Status</label>
                    <select name="status" class="form-select text-white" required>
                        <option value="observed" {{ $sicknessCase->status === 'observed' ? 'selected' : '' }}>Observasi</option>
                        <option value="handled" {{ $sicknessCase->status === 'handled' ? 'selected' : '' }}>Ditangani</option>
                        <option value="recovered" {{ $sicknessCase->status === 'recovered' ? 'selected' : '' }}>Sembuh</option>
                        <option value="referred" {{ $sicknessCase->status === 'referred' ? 'selected' : '' }}>Dirujuk</option>
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Catatan</label>
                    <textarea name="notes" class="form-control text-white" rows="3">{{ $sicknessCase->notes }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('sickness-cases.show', $sicknessCase) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
