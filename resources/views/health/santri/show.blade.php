@extends('layouts.app')

@section('title', 'Detail Santri - ' . $santri->name)
@section('page-title', 'Profil & Rekam Jejak Santri')

@section('page-actions')
    <a href="{{ route('santri.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
    
    {{-- Identity Card --}}
    <x-ui.card>
        <div style="padding: 40px 32px; text-align: center; background: linear-gradient(to bottom, var(--bg-main), white); border-bottom: 1px solid var(--border);">
            <div class="user-avatar" style="width: 100px; height: 100px; font-size: 40px; margin: 0 auto 20px; box-shadow: var(--shadow-lg); border: 4px solid white; background: var(--brand-start); color: white;">
                {{ strtoupper(substr($santri->name, 0, 1)) }}
            </div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $santri->name }}</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 16px;">NIS: {{ $santri->nis ?: 'Belum diatur' }}</p>
            
            <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                <span class="badge badge-info" style="padding: 6px 14px;"><i class="fas fa-school"></i> {{ optional($santri->schoolClass)->name ?: '-' }}</span>
                <span class="badge badge-primary" style="padding: 6px 14px;"><i class="fas fa-microscope"></i> {{ optional($santri->major)->name ?: '-' }}</span>
            </div>
        </div>
        
        <div style="padding: 24px 32px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Jenis Kelamin</div>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $santri->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Kamar Asrama</div>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $santri->dorm_room ?: '-' }}</div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tempat, Tanggal Lahir</div>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $santri->birth_place ?: '-' }}, {{ optional($santri->birth_date)->translatedFormat('d F Y') ?: '-' }}</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <div style="display:flex; flex-direction:column; gap:32px;">
        {{-- Guardian / Contact --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-address-book"></i> Kontak Darurat (Wali)</h2>
            </x-slot>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Nama Wali</div>
                    <div style="font-size: 16px; font-weight: 700;">{{ $santri->guardian_name ?: '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">No. WhatsApp / Telepon</div>
                    <div style="font-size: 16px; font-weight: 700; color: var(--success);">{{ $santri->guardian_phone ?: '-' }}</div>
                </div>
                <div>
                    @if($santri->guardian_phone)
                        <a href="https://wa.me/{{ $santri->guardian_phone }}" target="_blank" class="btn btn-success" style="background: #25d366; border-color: #25d366; color: white;">
                            <i class="fab fa-whatsapp"></i> Hubungi
                        </a>
                    @else
                        <button class="btn btn-outline" disabled>Belum ada nomor</button>
                    @endif
                </div>
            </div>
        </x-ui.card>

        {{-- Medical Notes --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-notes-medical"></i> Catatan Medis Khusus</h2>
            </x-slot>
            @if($santri->notes)
                <div style="padding: 16px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px;">
                    <p style="margin: 0; color: #92400e; line-height: 1.6;">{{ $santri->notes }}</p>
                </div>
            @else
                <x-ui.empty-state message="Tidak ada catatan medis khusus (alergi, riwayat penyakit, dll)." />
            @endif
        </x-ui.card>
    </div>
</div>
@endsection
