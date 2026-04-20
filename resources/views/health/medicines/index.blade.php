@extends('layouts.app')

@section('title', 'Inventori Obat')
@section('page-title', 'Manajemen Stok Obat')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-pills"></i> Inventori Obat UKS</h2>
        <a href="{{ route('medicines.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Obat
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari nama obat..." :value="request('search')" style="flex:1;" />
        <label class="badge badge-outline" style="display:flex; align-items:center; gap:8px; border:1px solid var(--border); padding:8px 12px; border-radius:10px; cursor:pointer;">
            <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
            <span style="font-size:13px; font-weight:600; color:var(--text-muted);">Stok Menipis</span>
        </label>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('medicines.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Satuan</th>
                <th>Ketersediaan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicines as $medicine)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $medicine->name }}</td>
                    <td style="color: var(--text-muted);">{{ $medicine->unit }}</td>
                    <td>
                        <div style="font-weight: 700;">{{ $medicine->stock }} <span style="font-weight: 400; font-size: 11px; color: var(--text-muted);">/ min {{ $medicine->minimum_stock }}</span></div>
                    </td>
                    <td>
                        @if($medicine->stock <= $medicine->minimum_stock)
                            <span class="badge badge-danger"><i class="fas fa-triangle-exclamation"></i> Kritis</span>
                        @else
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aman</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('medicines.index', array_merge(request()->query(), ['edit' => $medicine->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('medicines.destroy', $medicine) }}" onsubmit="return confirm('Hapus data obat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Tidak ada data obat yang terdaftar." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $medicines->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editMedicine ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editMedicine ? 'Edit Data Obat' : 'Tambah Obat Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editMedicine ? route('medicines.update', $editMedicine) : route('medicines.store') }}">
            @csrf
            @if($editMedicine)
                @method('PUT')
            @endif

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <x-form.field name="name" label="Nama Obat / Alkes">
                    <x-form.input name="name" :value="$editMedicine->name ?? ''" placeholder="Masukkan nama obat" />
                </x-form.field>
                <x-form.field name="unit" label="Satuan">
                    <x-form.select name="unit">
                        @foreach(['Tablet', 'Kapsul', 'Sirup', 'Strip', 'Botol', 'Pcs', 'Box'] as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $editMedicine->unit ?? '') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="stock" label="Stok Saat Ini">
                    <x-form.input name="stock" type="number" min="0" :value="$editMedicine->stock ?? 0" />
                </x-form.field>
                <x-form.field name="minimum_stock" label="Batas Stok Minimal">
                    <x-form.input name="minimum_stock" type="number" min="0" :value="$editMedicine->minimum_stock ?? 5" />
                </x-form.field>
            </div>

            <x-form.field name="description" label="Keterangan / Aturan Pakai">
                <x-form.textarea name="description" :value="$editMedicine->description ?? ''" placeholder="Informasi tambahan mengenai obat ini..." />
            </x-form.field>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editMedicine ? 'Simpan Perubahan' : 'Tambah Obat' }}
                </button>
                <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
