@extends('layouts.app')

@section('title', 'Detail Kasus - ' . $sicknessCase->santri->name)
@section('page-title', 'Detail Pemantauan Pasien')

@section('page-actions')
    <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
    
    {{-- Patient Info --}}
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-user-injured"></i> Informasi Pasien</h2>
            <a href="{{ route('santri.show', $sicknessCase->santri) }}" class="btn btn-xs btn-primary">Lihat Profil Santri</a>
        </x-slot>
        <div style="padding: 24px;">
            <div style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">{{ $sicknessCase->santri->name }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">NIS: {{ $sicknessCase->santri->nis ?: '-' }}</div>
            
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 24px; border-top: 1px solid var(--border); padding-top: 24px;">
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Ditangani Oleh</div>
                    <div style="font-weight: 600; color: var(--text-main);"><i class="fas fa-user-nurse" style="color: var(--brand-start);"></i> {{ optional($sicknessCase->handledBy)->name ?: 'Petugas Sistem' }}</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <div style="display:flex; flex-direction:column; gap:32px;">
        {{-- Case Details --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-file-medical-alt"></i> Detail Kunjungan & Tindakan</h2>
                @php
                    $statusTheme = match($sicknessCase->status) {
                        'observed' => ['class' => 'badge-warning', 'label' => 'Observasi'],
                        'handled' => ['class' => 'badge-info', 'label' => 'Ditangani'],
                        'recovered' => ['class' => 'badge-success', 'label' => 'Sembuh'],
                        'referred' => ['class' => 'badge-danger', 'label' => 'Dirujuk'],
                        default => ['class' => 'badge-outline', 'label' => $sicknessCase->status],
                    };
                @endphp
                <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Masuk</div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 15px;">{{ $sicknessCase->visit_date->format('d F Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Selesai / Sembuh</div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 15px;">{{ optional($sicknessCase->return_date)->format('d F Y') ?: 'Belum ditentukan' }}</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                    <div style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                        <div style="font-size: 12px; font-weight: 700; color: var(--brand-start); text-transform: uppercase; margin-bottom: 8px;">Keluhan Utama</div>
                        <p style="margin: 0; line-height: 1.6; color: var(--text-main);">{{ $sicknessCase->complaint ?: '-' }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Diagnosis</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $sicknessCase->diagnosis ?: '-' }}</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Tindakan Awal</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $sicknessCase->action_taken ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Facilities --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
            <x-ui.card>
                <x-slot name="header">
                    <h2><i class="fas fa-pills"></i> Terapi Obat</h2>
                </x-slot>
                <div style="padding: 24px;">
                    @if($sicknessCase->medicine)
                        <div style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $sicknessCase->medicine->name }}</div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Diberikan sesuai stok UKS.</p>
                        @if($sicknessCase->medicine_notes)
                            <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px; border-radius: 6px; font-size: 13px;">
                                <strong>Catatan:</strong> {{ $sicknessCase->medicine_notes }}
                            </div>
                        @endif
                    @else
                        <x-ui.empty-state message="Tidak ada obat yang dicatat." />
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot name="header">
                    <h2><i class="fas fa-bed"></i> Fasilitas Kasur</h2>
                </x-slot>
                <div style="padding: 24px;">
                    @if($sicknessCase->bed)
                        <div style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $sicknessCase->bed->code }}</div>
                        <div style="font-size: 14px; font-weight: 600; color: var(--brand-start);">{{ $sicknessCase->bed->room_name }}</div>
                    @else
                        <x-ui.empty-state message="Pasien dirawat jalan (tanpa kasur)." />
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
