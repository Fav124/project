@extends('layouts.app')

@section('title', 'Detail Rujukan - ' . $referral->santri->name)
@section('page-title', 'Dokumen Rujukan Medis')

@section('page-actions')
    <a href="{{ route('referrals.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
    <button onclick="window.print()" class="btn btn-primary" style="margin-left: 8px;">
        <i class="fas fa-print"></i> Cetak Dokumen
    </button>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: var(--shadow-sm); border: 1px solid var(--border);" class="print-container">
    
    {{-- Kop Surat / Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border); padding-bottom: 20px; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 900; color: var(--brand-start); margin-bottom: 4px;">SURAT RUJUKAN MEDIS</h1>
            <p style="color: var(--text-muted); font-size: 14px;">UKU - Sistem Informasi Manajemen Kesehatan Deisa</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Nomor Rujukan</div>
            <div style="font-size: 18px; font-weight: 800;">#RJK-{{ str_pad($referral->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size: 13px; color: var(--text-muted);">Tanggal: {{ $referral->referral_date->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    {{-- Tujuan Rujukan --}}
    <div style="background: var(--bg-main); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Kepada Yth.</div>
        <div style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Tim Medis {{ $referral->hospital_name }}</div>
        <div style="font-size: 14px; color: var(--text-muted);">Di Tempat</div>
    </div>

    {{-- Identitas Pasien --}}
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px dashed var(--border); padding-bottom: 8px; margin-bottom: 16px;">I. IDENTITAS PASIEN</h3>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="width: 200px; padding: 6px 0; color: var(--text-muted); font-weight: 600;">Nama Lengkap</td>
                <td style="font-weight: 700;">: {{ $referral->santri->name }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600;">Nomor Induk Santri (NIS)</td>
                <td>: {{ $referral->santri->nis ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600;">Kelas / Asrama</td>
                <td>: {{ optional($referral->santri->schoolClass)->name ?: '-' }} / {{ $referral->santri->dorm_room ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600;">Kontak Darurat (Wali)</td>
                <td>: {{ $referral->santri->guardian_name ?: '-' }} ({{ $referral->santri->guardian_phone ?: '-' }})</td>
            </tr>
        </table>
    </div>

    {{-- Resume Medis --}}
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px dashed var(--border); padding-bottom: 8px; margin-bottom: 16px;">II. RESUME MEDIS SINGKAT</h3>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="width: 200px; padding: 6px 0; color: var(--text-muted); font-weight: 600; vertical-align: top;">Anamnesa / Keluhan</td>
                <td style="padding: 6px 0;">: {{ $referral->complaint }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600; vertical-align: top;">Diagnosis Sementara</td>
                <td style="padding: 6px 0; font-weight: 700;">: {{ $referral->diagnosis ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600; vertical-align: top;">Alasan Rujukan</td>
                <td style="padding: 6px 0;">: {{ $referral->notes ?: '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Info Transport & Pendamping --}}
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px dashed var(--border); padding-bottom: 8px; margin-bottom: 16px;">III. TRANSPORTASI & PENDAMPINGAN</h3>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="width: 200px; padding: 6px 0; color: var(--text-muted); font-weight: 600;">Transportasi</td>
                <td>: {{ $referral->transport ?: 'Kendaraan Pribadi' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600;">Pendamping</td>
                <td>: {{ $referral->companion_name ?: 'Tidak ada (Didampingi keluarga)' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: var(--text-muted); font-weight: 600;">Status Rujukan</td>
                <td>: 
                    @php
                        $statusTheme = match($referral->status) {
                            'referred' => 'warning',
                            'treated' => 'primary',
                            'returned' => 'success',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge badge-{{ $statusTheme }}">{{ strtoupper($referral->status) }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Tanda Tangan --}}
    <div style="display: flex; justify-content: flex-end; margin-top: 50px;">
        <div style="text-align: center; width: 250px;">
            <div style="font-size: 14px; margin-bottom: 80px;">Petugas Pengirim,</div>
            <div style="font-size: 15px; font-weight: 700; text-decoration: underline;">{{ optional($referral->referredBy)->name ?: 'Petugas UKS' }}</div>
            <div style="font-size: 12px; color: var(--text-muted);">Sistem Informasi DEIHealth</div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .print-container, .print-container * { visibility: visible; }
    .print-container { position: absolute; left: 0; top: 0; width: 100%; padding: 0; box-shadow: none; border: none; }
    .btn { display: none !important; }
}
</style>
@endsection
