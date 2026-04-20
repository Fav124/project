@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page-title', 'Manajemen Ruang Kelas')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-school"></i> Daftar Kelas Santri</h2>
        <button type="button" class="btn btn-primary" onclick="openModal('formModal')">
            <i class="fas fa-plus"></i> Tambah Kelas
        </button>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari nama kelas atau jurusan..." :value="request('search')" style="flex:1;" />
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('classes.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Nama Kelas</th>
                <th>Jurusan Terkait</th>
                <th>Keterangan</th>
                <th style="width:120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $class)
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $class->name }}</td>
                    <td>
                        <div class="flex gap-1 flex-wrap">
                            @forelse($class->majors as $major)
                                <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">{{ $major->name }}</span>
                            @empty
                                <span class="text-muted" style="font-size: 12px; font-style: italic;">Umum</span>
                            @endforelse
                        </div>
                    </td>
                    <td style="color: var(--text-muted); font-size: 13px;">{{ $class->description ?: '-' }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('classes.index', array_merge(request()->query(), ['edit' => $class->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Hapus data kelas ini?')">
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
                <x-ui.empty-state :colspan="4" message="Belum ada data kelas yang terdaftar." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $classes->links() }}
    </x-slot>
</x-ui.card>

{{-- Form Modal --}}
<div id="formModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas {{ $editClass ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editClass ? 'Edit Konfigurasi Kelas' : 'Tambah Kelas Baru' }}</h3>
            <button type="button" class="modal-close" onclick="closeModal('formModal'); {{ $editClass ? 'window.location.href=\''.route('classes.index').'\'' : '' }}">&times;</button>
        </div>
        <form method="POST" action="{{ $editClass ? route('classes.update', $editClass) : route('classes.store') }}">
            <div class="modal-body">
                @csrf
                @if($editClass)
                    @method('PUT')
                @endif
                
                <x-form.field name="name" label="Nama Kelas">
                    <x-form.input name="name" :value="$editClass->name ?? ''" placeholder="Contoh: X IPA 1, XII SMK, dll" />
                </x-form.field>

                <x-form.field name="major_ids" label="Pilih Jurusan (Bisa lebih dari satu)">
                    <select id="major_ids" name="major_ids[]" multiple class="form-control {{ $errors->has('major_ids') ? 'is-invalid' : '' }}" style="height: 150px; border-radius:12px; border:1px solid var(--border); padding:8px;">
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ ($editClass && $editClass->majors->contains($major->id)) || collect(old('major_ids'))->contains($major->id) ? 'selected' : '' }} style="padding:8px; border-bottom:1px solid #f8fafc;">
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Tahan tombol <b>Ctrl</b> (Windows) atau <b>Cmd</b> (Mac) untuk memilih beberapa jurusan sekaligus.
                    </div>
                </x-form.field>

                <x-form.field name="description" label="Deskripsi / Catatan Kelas">
                    <x-form.textarea name="description" :value="$editClass->description ?? ''" placeholder="Informasi tambahan mengenai kelas ini..." />
                </x-form.field>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('formModal'); {{ $editClass ? 'window.location.href=\''.route('classes.index').'\'' : '' }}">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editClass ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                </button>
            </div>
        </form>
    </div>
</div>

@if($showForm || $errors->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                openModal('formModal');
            });
        </script>
    @endpush
@endif
@endsection
