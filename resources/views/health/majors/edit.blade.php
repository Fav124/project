@extends('layouts.app')

@section('title', 'Edit Jurusan - ' . $major->name)
@section('page-title', 'Edit Jurusan')

@section('page-actions')
    <a href="{{ route('majors.show', $major) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Jurusan</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('majors.update', $major) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Nama Jurusan</label>
                    <input type="text" name="name" class="form-control text-white" value="{{ $major->name }}" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Deskripsi</label>
                    <textarea name="description" class="form-control text-white" rows="4">{{ $major->description }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('majors.show', $major) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
