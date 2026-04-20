@extends('layouts.app')

@section('title', 'Rekam Medis')
@section('page-title', 'Arsip Rekam Medis Santri')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-file-medical"></i> Log Pemeriksaan Medis</h2>
        <a href="{{ route('health-records.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Rekam
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari santri..." :value="request('search')" style="flex:1;" />
        <div style="display:flex; gap:8px;">
            <x-form.input name="date_from" type="date" :value="request('date_from')" />
            <span style="align-self:center; color:var(--text-muted);">-</span>
            <x-form.input name="date_to" type="date" :value="request('date_to')" />
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('health-records.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Nama Santri</th>
                <th>Diagnosis Utama</th>
                <th>Tindakan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td style="white-space:nowrap;">
                        <div style="font-weight:700;">{{ $record->record_date->format('d/m/Y') }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $record->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">{{ $record->santri->name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);" title="{{ $record->complaint }}">
                            {{ Str::limit($record->complaint, 40) }}
                        </div>
                    </td>
                    <td>
                        @if($record->diagnosis)
                            <span class="badge badge-info" style="font-size:11px;">{{ $record->diagnosis }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ Str::limit($record->treatment, 50) }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('health-records.index', array_merge(request()->query(), ['edit' => $record->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('health-records.destroy', $record) }}" onsubmit="return confirm('Hapus rekam kesehatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Belum ada catatan medis yang tersimpan." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $records->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editRecord ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editRecord ? 'Edit Rekam Medis' : 'Input Rekam Medis Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editRecord ? route('health-records.update', $editRecord) : route('health-records.store') }}">
            @csrf
            @if($editRecord)
                @method('PUT')
            @endif

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <x-form.field name="santri_id" label="Pilih Santri">
                    <x-form.select name="santri_id">
                        <option value="">Cari santri...</option>
                        @foreach($santris as $santri)
                            <option value="{{ $santri->id }}" @selected((string) old('santri_id', $editRecord->santri_id ?? '') === (string) $santri->id)>{{ $santri->name }} ({{ $santri->nis ?: 'No NIS' }})</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="record_date" label="Tanggal Periksa">
                    <x-form.input name="record_date" type="date" :value="optional($editRecord->record_date ?? null)->format('Y-m-d') ?: now()->toDateString()" />
                </x-form.field>
            </div>

            <x-form.field name="complaint" label="Keluhan Utama">
                <x-form.textarea name="complaint" :value="$editRecord->complaint ?? ''" placeholder="Apa yang dirasakan santri?" />
            </x-form.field>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="diagnosis" label="Hasil Diagnosis / Penyakit">
                    <x-form.input name="diagnosis" :value="$editRecord->diagnosis ?? ''" placeholder="Misal: Influenza, Typus, dll" />
                </x-form.field>
                <x-form.field name="treatment" label="Tindakan Medis / Obat yang Diberikan">
                    <x-form.input name="treatment" :value="$editRecord->treatment ?? ''" placeholder="Misal: Pemberian Paracetamol & Istirahat" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                <x-form.field name="blood_pressure" label="Tekanan Darah (mmHg)">
                    <x-form.input name="blood_pressure" :value="$editRecord->blood_pressure ?? ''" placeholder="120/80" />
                </x-form.field>
                <x-form.field name="temperature" label="Suhu Tubuh (°C)">
                    <x-form.input name="temperature" type="number" step="0.1" :value="$editRecord->temperature ?? ''" placeholder="36.5" />
                </x-form.field>
                <x-form.field name="weight" label="Berat Badan (kg)">
                    <x-form.input name="weight" type="number" step="0.1" :value="$editRecord->weight ?? ''" placeholder="50" />
                </x-form.field>
            </div>

            <x-form.field name="notes" label="Catatan Tambahan Petugas">
                <x-form.textarea name="notes" :value="$editRecord->notes ?? ''" placeholder="Catatan opsional..." />
            </x-form.field>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editRecord ? 'Simpan Perubahan' : 'Rekam Data' }}
                </button>
                <a href="{{ route('health-records.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
