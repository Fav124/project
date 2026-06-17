@extends('layouts.app')

@section('title', 'Edit Kelas - ' . $class->name)
@section('page-title', 'Edit Kelas')

@section('page-actions')
    <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
@endpush

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Kelas</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('classes.update', $class) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Nama Kelas</label>
                    <input type="text" name="name" class="form-control text-white" value="{{ $class->name }}" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Pilih Jurusan</label>
                    <select name="major_ids[]" class="form-control text-white select2-multiple" multiple style="width: 100%;">
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ $class->majors->contains($major->id) ? 'selected' : '' }}>{{ $major->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Deskripsi</label>
                    <textarea name="description" class="form-control text-white" rows="3">{{ $class->description }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('classes.show', $class) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('template-assets/vendors/select2/select2.min.js') }}"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "Pilih Jurusan",
            allowClear: true,
            theme: "bootstrap"
        });
    });
</script>
@endpush
