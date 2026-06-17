@extends('layouts.app')

@section('title', 'Detail Jurusan - ' . $major->name)
@section('page-title', 'Detail Jurusan')

@section('page-actions')
    <a href="{{ route('majors.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
    <a href="{{ route('majors.edit', $major) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-microscope"></i> Informasi Jurusan</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 16px;">{{ $major->name }}</div>

                <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--border); padding-top: 20px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Deskripsi</div>
                        <p style="color: var(--text-main); margin: 0; line-height: 1.6;">{{ $major->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Jumlah Santri</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--brand-start);">{{ $major->santris->count() }}</div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-users"></i> Daftar Santri ({{ $major->santris->count() }})</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="max-height: 500px; overflow-y: auto;">
                    <table class="table" style="color: var(--text-main);">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($major->santris as $santri)
                                <tr>
                                    <td>
                                        <a href="{{ route('santri.show', $santri) }}" style="color: var(--brand-start); font-weight: 600;">{{ $santri->name }}</a>
                                    </td>
                                    <td>{{ $santri->nis ?: '-' }}</td>
                                    <td>{{ optional($santri->schoolClass)->name ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada santri di jurusan ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
