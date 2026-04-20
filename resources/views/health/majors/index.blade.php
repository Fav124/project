@extends('layouts.app')

@section('title', 'Data Jurusan')
@section('page-title', 'Manajemen Program Studi / Jurusan')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-microscope"></i> Daftar Jurusan Santri</h2>
        <a href="{{ route('majors.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Jurusan
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari nama jurusan..." :value="request('search')" style="flex:1;" />
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('majors.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Nama Jurusan</th>
                <th>Deskripsi Singkat</th>
                <th style="width:120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($majors as $major)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $major->name }}</td>
                    <td style="color: var(--text-muted); font-size: 13px;">{{ $major->description ?: '-' }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('majors.index', array_merge(request()->query(), ['edit' => $major->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('majors.destroy', $major) }}" onsubmit="return confirm('Hapus data jurusan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="3" message="Belum ada data jurusan yang terdaftar." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $majors->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editMajor ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editMajor ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editMajor ? route('majors.update', $editMajor) : route('majors.store') }}">
            @csrf
            @if($editMajor)
                @method('PUT')
            @endif
            
            <x-form.field name="name" label="Nama Jurusan">
                <x-form.input name="name" :value="$editMajor->name ?? ''" placeholder="Contoh: Ilmu Pengetahuan Alam (IPA)" />
            </x-form.field>

            <x-form.field name="description" label="Keterangan Tambahan">
                <x-form.textarea name="description" :value="$editMajor->description ?? ''" placeholder="Penjelasan singkat mengenai jurusan ini..." />
            </x-form.field>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editMajor ? 'Simpan Perubahan' : 'Tambah Jurusan' }}
                </button>
                <a href="{{ route('majors.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
