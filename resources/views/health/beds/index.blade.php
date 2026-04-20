@extends('layouts.app')

@section('title', 'Kasur UKS')
@section('page-title', 'Ketersediaan Kasur UKS')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-bed"></i> Daftar Kasur UKS</h2>
        <a href="{{ route('beds.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kasur
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari kode, ruang, atau penghuni..." :value="request('search')" />
        <x-form.select name="status">
            <option value="">Semua Status</option>
            <option value="available" @selected(request('status') === 'available')>Tersedia</option>
            <option value="occupied" @selected(request('status') === 'occupied')>Terisi</option>
            <option value="maintenance" @selected(request('status') === 'maintenance')>Perawatan</option>
        </x-form.select>
        <button type="submit" class="btn btn-outline">Filter</button>
        <a href="{{ route('beds.index') }}" class="btn btn-secondary">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Ruang</th>
                <th>Status</th>
                <th>Pengguna Saat Ini</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($beds as $bed)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $bed->code }}</td>
                    <td>{{ $bed->room_name }}</td>
                    <td>
                        @php
                            $statusTheme = match($bed->status) {
                                'available' => ['class' => 'badge-success', 'label' => 'Tersedia', 'icon' => 'fa-check-circle'],
                                'occupied' => ['class' => 'badge-warning', 'label' => 'Terisi', 'icon' => 'fa-user-clock'],
                                default => ['class' => 'badge-danger', 'label' => 'Perawatan', 'icon' => 'fa-tools'],
                            };
                        @endphp
                        <span class="badge {{ $statusTheme['class'] }}">
                            <i class="fas {{ $statusTheme['icon'] }}"></i> {{ $statusTheme['label'] }}
                        </span>
                    </td>
                    <td>
                        @if($bed->occupant_name)
                            <div style="font-weight: 600;">{{ $bed->occupant_name }}</div>
                        @else
                            <span class="text-muted" style="font-size: 13px; font-style: italic;">Kosong</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('beds.index', array_merge(request()->query(), ['edit' => $bed->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('beds.destroy', $bed) }}" onsubmit="return confirm('Hapus data kasur ini?')">
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
                <x-ui.empty-state :colspan="5" message="Belum ada data kasur UKS yang terdaftar." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $beds->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editBed ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editBed ? 'Edit Kasur' : 'Tambah Kasur UKS' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editBed ? route('beds.update', $editBed) : route('beds.store') }}">
            @csrf
            @if($editBed)
                @method('PUT')
            @endif
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="code" label="Kode Kasur">
                    <x-form.input name="code" :value="$editBed->code ?? ''" placeholder="Contoh: K-01" />
                </x-form.field>
                <x-form.field name="room_name" label="Nama Ruangan">
                    <x-form.input name="room_name" :value="$editBed->room_name ?? 'UKS Utama'" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="status" label="Status Ketersediaan">
                    <x-form.select name="status">
                        <option value="available" @selected(old('status', $editBed->status ?? 'available') === 'available')>Tersedia</option>
                        <option value="occupied" @selected(old('status', $editBed->status ?? '') === 'occupied')>Terisi (Digunakan)</option>
                        <option value="maintenance" @selected(old('status', $editBed->status ?? '') === 'maintenance')>Dalam Perawatan</option>
                    </x-form.select>
                </x-form.field>
                <x-form.field name="occupant_name" label="Nama Penghuni (Jika Ada)">
                    <x-form.input name="occupant_name" :value="$editBed->occupant_name ?? ''" placeholder="Masukkan nama santri" />
                </x-form.field>
            </div>

            <x-form.field name="notes" label="Catatan Tambahan">
                <x-form.textarea name="notes" :value="$editBed->notes ?? ''" placeholder="Kondisi kasur, fasilitas, dll..." />
            </x-form.field>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    {{ $editBed ? 'Simpan Perubahan' : 'Tambah Kasur' }}
                </button>
                <a href="{{ route('beds.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
